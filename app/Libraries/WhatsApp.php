<?php

namespace App\Libraries;

use App\Config\WhatsApp as WhatsAppConfig;

/**
 * WhatsApp Cloud API integration.
 *
 * Usage:
 *   $wa = new \App\Libraries\WhatsApp();
 *   $wa->sendTemplate('919876543210', 'whatsapp5', ['Hello!'], 'booking_customer');
 *   $wa->sendText('919876543210', 'Your order is confirmed.', 'order_confirmed');
 *   $wa->sendToAdmins('New order received', 'admin_alert');
 */
class WhatsApp
{
    private WhatsAppConfig $config;
    private string $apiBase;

    public function __construct(?WhatsAppConfig $config = null)
    {
        $this->config   = $config ?? new WhatsAppConfig();
        $this->apiBase  = 'https://graph.facebook.com/' . $this->config->graphApiVersion;
    }

    /**
     * Normalize an Indian phone number to 91XXXXXXXXXX format.
     */
    public function normalizeNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        // Strip leading 0 or 91 prefix if already present
        if (strlen($digits) === 13 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return '91' . $digits;
    }

    /**
     * Send a template message.
     *
     * @param string $phone            Raw phone number (will be normalized)
     * @param string $templateKey      Key from WhatsAppConfig::templates (e.g. 'generic')
     * @param array  $params           Body parameters for the template
     * @param string $triggerSource    What action triggered this send (for logging)
     * @param int    $userId           Associated user ID (for logging)
     * @param string $userType         User type: buyer/seller/admin (for logging)
     */
    public function sendTemplate(
        string $phone,
        string $templateKey,
        array  $params = [],
        string $triggerSource = '',
        int    $userId = 0,
        string $userType = ''
    ): bool {
        $normalized = $this->normalizeNumber($phone);
        $templateName = $this->resolveTemplate($templateKey);
        $paramCount = $this->config->templateParamCounts[$templateName] ?? count($params);

        // Pad parameters to expected count
        $params = array_values($params);
        while (count($params) < $paramCount) {
            $params[] = '-';
        }
        $params = array_slice($params, 0, $paramCount);

        $bodyParams = array_map(fn($p) => ['type' => 'text', 'text' => (string) $p], $params);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $normalized,
            'type'              => 'template',
            'template'          => [
                'name'     => $templateName,
                'language' => ['code' => $this->config->templateLanguage],
                'components' => [
                    [
                        'type'       => 'body',
                        'parameters' => $bodyParams,
                    ],
                ],
            ],
        ];

        $result = $this->post($payload);

        $this->log($normalized, $templateName, $triggerSource, $userId, $userType, $result);

        // Auto-fallback to plain text on template errors
        if (!$result['success'] && in_array($result['error_code'] ?? 0, $this->config->templateFallbackErrors, true)) {
            $textBody = implode("\n", array_filter($params, fn($p) => $p !== '-'));
            if ($textBody !== '') {
                return $this->sendText($phone, $textBody, $triggerSource . '_fallback', $userId, $userType);
            }
        }

        return $result['success'];
    }

    /**
     * Send a plain text message.
     */
    public function sendText(
        string $phone,
        string $message,
        string $triggerSource = '',
        int    $userId = 0,
        string $userType = ''
    ): bool {
        $normalized = $this->normalizeNumber($phone);

        log_message('info', "WhatsApp sendText: phone=$phone normalized=$normalized trigger=$triggerSource");

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $normalized,
            'type'              => 'text',
            'text'              => ['body' => $message],
        ];

        $result = $this->post($payload);

        log_message('info', "WhatsApp sendText result: success=" . ($result['success'] ? 'YES' : 'NO')
            . " http={$result['http_status']} error={$result['error_message']}");

        $this->log($normalized, 'text_message', $triggerSource, $userId, $userType, $result, $message);

        return $result['success'];
    }

    /**
     * Send a message to all configured admin phone numbers.
     */
    public function sendToAdmins(
        string $message,
        string $triggerSource = 'admin_alert',
        string $templateKey = 'generic',
        array  $params = []
    ): array {
        $results = [];
        foreach ($this->config->adminNumbers as $adminPhone) {
            if (!empty($params)) {
                $results[$adminPhone] = $this->sendTemplate($adminPhone, $templateKey, $params, $triggerSource);
            } else {
                $results[$adminPhone] = $this->sendText($adminPhone, $message, $triggerSource);
            }
        }
        return $results;
    }

    /**
     * Resolve a template key (e.g. 'generic') to its Meta template name (e.g. 'whatsapp5').
     */
    public function resolveTemplate(string $key): string
    {
        return $this->config->templates[$key] ?? $key;
    }

    /**
     * Check if WhatsApp is configured (has access token and phone number ID).
     */
    public function isConfigured(): bool
    {
        return $this->config->accessToken !== '' && $this->config->phoneNumberId !== '';
    }

    // ------------------------------------------------------------------
    //  Internal
    // ------------------------------------------------------------------

    /**
     * POST payload to WhatsApp Graph API.
     *
     * @return array{success: bool, http_status: int, response: array, error_code: ?int, error_message: ?string}
     */
    private function post(array $payload): array
    {
        if (!$this->isConfigured()) {
            return [
                'success'        => false,
                'http_status'    => 0,
                'response'       => [],
                'error_code'     => null,
                'error_message'  => 'WhatsApp not configured (missing access_token or phone_number_id)',
            ];
        }

        $url = $this->apiBase . '/' . $this->config->phoneNumberId . '/messages';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->config->accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => !$this->config->disableSslVerify,
        ]);

        $body     = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        $response = json_decode($body, true) ?? [];

        if ($curlErr) {
            return [
                'success'       => false,
                'http_status'   => $httpCode,
                'response'      => $response,
                'error_code'    => null,
                'error_message' => 'cURL error: ' . $curlErr,
            ];
        }

        $success = $httpCode >= 200 && $httpCode < 300;
        $errorCode = $response['error']['code'] ?? null;
        $errorMsg  = $response['error']['message'] ?? null;

        return [
            'success'       => $success,
            'http_status'   => $httpCode,
            'response'      => $response,
            'error_code'    => $errorCode,
            'error_message' => $errorMsg,
        ];
    }

    /**
     * Log a send attempt to whatsapp_message_logs table.
     */
    private function log(
        string $normalizedPhone,
        string $templateName,
        string $triggerSource,
        int    $userId,
        string $userType,
        array  $result,
        string $composedMessage = ''
    ): void {
        try {
            $db = \Config\Database::connect();
            $db->table('whatsapp_message_logs')->insert([
                'user_id'          => $userId ?: null,
                'user_type'        => $userType ?: null,
                'phone'            => $normalizedPhone,
                'normalized_phone' => $normalizedPhone,
                'template_name'    => $templateName,
                'trigger_source'   => $triggerSource,
                'send_status'      => $result['success'] ? 'success' : 'failed',
                'http_status'      => $result['http_status'],
                'error_code'       => $result['error_code'] ?? null,
                'error_message'    => $result['error_message'] ?? null,
                'api_response'     => json_encode($result['response']),
                'composed_message' => $composedMessage ?: null,
                'created_at'       => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'WhatsApp log write failed: ' . $e->getMessage());
        }
    }
}