<?php

namespace App\Libraries;

use Config\Services;

/**
 * Razorpay gateway wrapper.
 *
 * Mode is automatic:
 *   - live   : when razorpay.key_id + razorpay.key_secret are set in .env
 *   - sandbox: otherwise — creates simulated order IDs and signatures,
 *              so the whole payment flow can be exercised locally on WAMP.
 */
class Razorpay
{
    private const API_BASE = 'https://api.razorpay.com/v1';

    /** @var string|null */
    protected $keyId;

    /** @var string|null */
    protected $keySecret;

    public function __construct()
    {
        $this->keyId     = env('razorpay.key_id');
        $this->keySecret = env('razorpay.key_secret');
    }

    public function isLive(): bool
    {
        return (bool) ($this->keyId && $this->keySecret);
    }

    /**
     * Sandbox simulation is only permitted in the development environment.
     * In production, missing keys must fail closed — never silently approve.
     */
    public function simulationAllowed(): bool
    {
        return !$this->isLive() && ENVIRONMENT === 'development';
    }

    public function keyId(): ?string
    {
        return $this->keyId;
    }

    /**
     * Create a Razorpay order (amount in paise).
     *
     * @param array $payload ['amount', 'currency', 'receipt', ...]
     * @return array { id, amount, currency }
     */
    public function createOrder(array $payload): array
    {
        if ($this->isLive()) {
            $response = $this->request('POST', '/orders', $payload);

            return [
                'id'       => $response['id'],
                'amount'   => (int) $response['amount'],
                'currency' => $response['currency'],
            ];
        }

        // Sandbox only in development — never approve without a real gateway.
        if (!$this->simulationAllowed()) {
            throw new \RuntimeException('Payment gateway is not configured.');
        }

        return [
            'id'       => 'order_' . bin2hex(random_bytes(10)),
            'amount'   => (int) $payload['amount'],
            'currency' => $payload['currency'] ?? 'INR',
        ];
    }

    /**
     * Verify the payment signature returned by the checkout.js flow.
     *
     * @param array $attrs razorpay_order_id, razorpay_payment_id, razorpay_signature
     */
    public function verifySignature(array $attrs): bool
    {
        if ($this->isLive()) {
            $expected = hash_hmac(
                'sha256',
                ($attrs['razorpay_order_id'] ?? '') . '|' . ($attrs['razorpay_payment_id'] ?? ''),
                (string) $this->keySecret
            );

            return hash_equals($expected, (string) ($attrs['razorpay_signature'] ?? ''));
        }

        // Sandbox only in development — in production, a missing gateway must fail.
        if (!$this->simulationAllowed()) {
            return false;
        }

        // Sandbox: return a deterministic "valid" signature.
        $expected = hash('sha256', $attrs['razorpay_order_id'] . '|' . $attrs['razorpay_payment_id'] . '|sandbox');

        return hash_equals($expected, (string) ($attrs['razorpay_signature'] ?? ''));
    }

    /**
     * Deterministic sandbox signature so the verify() path is exercised
     * end-to-end without live keys.
     */
    public function sandboxSignature(string $orderId, string $paymentId): string
    {
        return hash('sha256', $orderId . '|' . $paymentId . '|sandbox');
    }

    /**
     * Low-level REST call using CI4 CURLRequest with HTTP Basic auth.
     */
    protected function request(string $method, string $path, array $body = []): array
    {
        $http = Services::curlrequest();

        $response = $http->request($method, self::API_BASE . $path, [
            'auth'    => [$this->keyId, $this->keySecret],
            'json'    => $body,
            'headers' => ['Accept' => 'application/json'],
            'timeout' => 30,
        ]);

        $data = json_decode((string) $response->getBody(), true);

        if (is_array($data) && isset($data['error'])) {
            throw new \RuntimeException(
                'Razorpay error: ' . ($data['error']['description'] ?? 'unknown error')
            );
        }

        if (!is_array($data)) {
            throw new \RuntimeException('Razorpay returned an invalid response.');
        }

        return $data;
    }
}