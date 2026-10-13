<?php

namespace App\Libraries;

use App\Models\OtpModel;

/**
 * OtpService — generates and validates OTPs. In development the OTP is
 * returned in the response so the flow can be tested without a real
 * SMS/email gateway. Swap __deliver() with a live provider in production.
 */
class OtpService
{
    public function __construct(private OtpModel $otpModel)
    {
    }

    public function generateAndSend(string $purpose, ?string $mobile = null, ?string $email = null): array
    {
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $this->otpModel->insert([
            'mobile'     => $mobile,
            'email'      => $email,
            'otp'        => $otp,
            'purpose'    => $purpose,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
            'used'       => 0,
            'attempts'   => 0,
        ]);

        $delivered = $this->deliver($purpose, $otp, $mobile, $email);

        return ['otp' => $otp, 'delivered' => $delivered, 'expires_at' => date('Y-m-d H:i:s', strtotime('+10 minutes'))];
    }

    public function verify(string $purpose, string $otp, ?string $mobile = null, ?string $email = null): int
    {
        $record = $this->otpModel->validFor($mobile, $email, $otp, $purpose);

        if ($record === null) {
            return 0; // invalid / expired / already used
        }

        // Allow a handful of verify attempts before locking.
        if ((int) $record['attempts'] >= 5) {
            return 2; // too many attempts
        }

        $this->otpModel->update($record['id'], ['used' => 1, 'attempts' => (int) $record['attempts'] + 1]);

        return 1;
    }

    private function deliver(string $purpose, string $otp, ?string $mobile, ?string $email): bool
    {
        if (getenv('CI_ENVIRONMENT') !== 'production') {
            return true; // dev: OTP returned in response
        }

        // TODO: integrate production SMS (Msg91/MSGIndia) + email gateway here.
        log_message('info', "OTP deliver: purpose=$purpose mobile=$mobile email=$email otp=$otp");

        return true;
    }
}