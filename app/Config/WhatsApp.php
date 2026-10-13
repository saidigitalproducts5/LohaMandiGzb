<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * WhatsApp Cloud API configuration.
 * Values come from .env (WHATSAPP_*) with sensible defaults.
 */
class WhatsApp extends BaseConfig
{
    /** Meta Graph API access token (WhatsApp Cloud API). */
    public string $accessToken = '';

    /** Phone Number ID from Meta WhatsApp Business Manager. */
    public string $phoneNumberId = '';

    /** Legacy Phone Number ID (fallback). */
    public string $legacyPhoneNumberId = '';

    /** WhatsApp Business Account ID. */
    public string $businessAccountId = '';

    /** Graph API version. */
    public string $graphApiVersion = 'v22.0';

    /** Set to true to disable SSL certificate verification (dev only). */
    public bool $disableSslVerify = false;

    /** Template language code (e.g. en_US). */
    public string $templateLanguage = 'en_US';

    /**
     * WhatsApp message templates.
     * Each key maps to a Meta-approved template name.
     */
    public array $templates = [
        'register'                => 'registration',
        'login_success'           => 'whatsapp88',
        'booking_confirmation'    => 'booking_confirmation',
        'booking_no_preference'   => 'booking_no_preference',
        'booking_done'            => 'bookingdone',
        'upcoming_payment'        => 'upcomingpayment',
        'cancellation'            => 'whatsapp88',
        'generic'                 => 'whatsapp88',
    ];

    /** Number of body parameters each template expects. */
    public array $templateParamCounts = [
        'registration'            => 2,
        'whatsapp5'               => 1,
        'booking_confirmation'    => 5,
        'booking_no_preference'   => 3,
        'bookingdone'             => 0,
        'upcomingpayment'         => 3,
        'whatsapp88'               => 1,
    ];

    /** Admin phone numbers (with country code, e.g. 919999999999) for notifications. */
    public array $adminNumbers = [];

    /** Error codes that trigger automatic template-to-text fallback. */
    public array $templateFallbackErrors = [132000, 132001];

    public function __construct()
    {
        parent::__construct();

        // Override from .env if present
        $this->accessToken         = getenv('WHATSAPP_ACCESS_TOKEN') ?: $this->accessToken;
        $this->phoneNumberId       = getenv('WHATSAPP_PHONE_NUMBER_ID') ?: $this->phoneNumberId;
        $this->legacyPhoneNumberId = getenv('WHATSAPP_LEGACY_PHONE_NUMBER_ID') ?: $this->legacyPhoneNumberId;
        $this->businessAccountId   = getenv('WHATSAPP_BUSINESS_ACCOUNT_ID') ?: $this->businessAccountId;
        $this->graphApiVersion     = getenv('WHATSAPP_GRAPH_API_VERSION') ?: $this->graphApiVersion;

        $ssl = getenv('WHATSAPP_DISABLE_SSL_VERIFY');
        if ($ssl !== false) {
            $this->disableSslVerify = (bool) $ssl;
        }

        $admins = getenv('WHATSAPP_ADMIN_NUMBERS');
        if ($admins !== false && $admins !== '') {
            $this->adminNumbers = array_map('trim', explode(',', $admins));
        }
    }
}