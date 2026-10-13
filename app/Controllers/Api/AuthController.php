<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Libraries\Jwt;
use App\Libraries\OtpService;
use App\Models\OtpModel;
use App\Models\SellerModel;
use App\Models\UserModel;

class AuthController extends ApiController
{
    protected $helpers = ['url'];

    public function register()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'   => 'required|min_length[2]|max_length[120]',
            'mobile' => 'required|regex_match[/^[6-9][0-9]{9}$/]|is_unique[users.mobile]',
            'role'   => 'required|in_list[buyer,seller,transporter]',
            'email'  => 'permit_empty|valid_email|is_unique[users.email]',
            'password' => 'permit_empty|min_length[6]|max_length[128]',
        ], [
            'mobile' => ['is_unique' => 'Number is already registered. Login to your account or use different number.'],
            'email'  => ['is_unique' => 'An account already exists with this email address.'],
        ]);

        if (!$validation->run($data)) {
            return $this->respondValidationErrors($validation->getErrors());
        }

        $roleMap = ['buyer' => 5, 'seller' => 6, 'transporter' => 7];
        $roleId  = $roleMap[$data['role']];

        $email = ($data['email'] ?? '') !== '' ? $data['email'] : null;
        $pwd   = ($data['password'] ?? '') !== '' ? $data['password'] : null;

        $userModel = new UserModel();
        $userId = (int) $userModel->insert([
            'role_id'    => $roleId,
            'name'       => $data['name'],
            'mobile'     => $data['mobile'],
            'email'      => $email,
            'password'   => $pwd,
            'status'     => 'active',
        ]);

        // Sellers need a business profile row to trade.
        if ($data['role'] === 'seller') {
            $sellerModel = new SellerModel();
            $slug = url_title($data['name'], '-', true) . '-' . $userId;
            $sellerModel->insert([
                'user_id'        => $userId,
                'business_name'  => $data['name'],
                'slug'           => $slug,
                'status'         => 'pending',
            ]);
        }

        return $this->respondCreated(
            ['user_id' => $userId, 'mobile' => $data['mobile'], 'role' => $data['role']],
            'Registration successful. Verify your mobile OTP to continue.'
        );
    }

    public function sendOtp()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'mobile'  => 'required|regex_match[/^[6-9][0-9]{9}$/]',
            'purpose' => 'permit_empty|in_list[register,login,forgot,verify_mobile,verify_email]',
        ]);
        if (!$validation->run($data)) {
            return $this->respondValidationErrors($validation->getErrors());
        }

        $purpose = $data['purpose'] ?? 'login';
        $email   = $data['email'] ?? null;

        $otp = (new OtpService(new OtpModel()))->generateAndSend($purpose, $data['mobile'], $email);

        return $this->respondOK(['otp' => $otp['otp'], 'expires_at' => $otp['expires_at']], 'OTP sent.');
    }

    public function verifyOtp()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'mobile'  => 'required|regex_match[/^[6-9][0-9]{9}$/]',
            'otp'     => 'required|exact_length[6]|numeric',
            'purpose' => 'permit_empty|in_list[register,login,forgot,verify_mobile,verify_email]',
        ]);
        if (!$validation->run($data)) {
            return $this->respondValidationErrors($validation->getErrors());
        }

        $purpose = $data['purpose'] ?? 'login';
        $result  = (new OtpService(new OtpModel()))->verify($purpose, $data['otp'], $data['mobile']);

        if ($result === 0) {
            return $this->respondValidationErrors(['otp' => 'Invalid or expired OTP.']);
        }
        if ($result === 2) {
            return $this->respondValidationErrors(['otp' => 'Too many attempts. Please request a new OTP.']);
        }

        $userModel = new UserModel();
        $user = $userModel->findByMobile($data['mobile']);

        // Mark mobile verified.
        if ($user !== null) {
            $userModel->update($user['id'], ['mobile_verified' => 1]);
        }

        return $this->respondOK(['mobile_verified' => true], 'OTP verified.');
    }

    public function login()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $userModel = new UserModel();

        // --- Mobile OTP login ---
        if (isset($data['mobile']) && !empty($data['otp'])) {
            $otpService = new OtpService(new OtpModel());
            if ($otpService->verify('login', $data['otp'], $data['mobile']) !== 1) {
                return $this->respondValidationErrors(['otp' => 'Invalid or expired OTP.']);
            }
            $user = $userModel->findByMobile($data['mobile']);
            if ($user === null) {
                return $this->respondUnauthorized('No account found for this mobile number.');
            }
        }
        // --- Email + password login ---
        elseif (isset($data['email']) && isset($data['password'])) {
            $user = $userModel->findByEmail($data['email']);
            if ($user === null || empty($user['password']) || !password_verify($data['password'], $user['password'])) {
                return $this->respondUnauthorized('Invalid email or password.');
            }
        } else {
            return $this->respondValidationErrors(['login' => 'Provide mobile+otp or email+password.']);
        }

        if ($user['status'] !== 'active') {
            return $this->respondForbidden('Account is ' . $user['status'] . '.');
        }

        $roleCode = (new UserModel())->roleCode($user['role_id']);
        $token    = (new Jwt())->issue($user['id'], $user['role_id'], $roleCode);

        $userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        return $this->respondOK([
            'token'       => $token,
            'token_type'  => 'Bearer',
            'user'        => [
                'id'     => (int) $user['id'],
                'name'   => $user['name'],
                'email'  => $user['email'],
                'mobile' => $user['mobile'],
                'role'   => $roleCode,
            ],
        ], 'Login successful.');
    }

    public function forgotPassword()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'mobile'  => 'required|regex_match[/^[6-9][0-9]{9}$/]',
            'otp'     => 'required|exact_length[6]|numeric',
            'password' => 'required|min_length[6]|max_length[128]',
        ]);
        if (!$validation->run($data)) {
            return $this->respondValidationErrors($validation->getErrors());
        }

        if ((new OtpService(new OtpModel()))->verify('forgot', $data['otp'], $data['mobile']) !== 1) {
            return $this->respondValidationErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $userModel = new UserModel();
        $user = $userModel->findByMobile($data['mobile']);
        if ($user === null) {
            return $this->respondNotFound('No account found for this mobile number.');
        }

        // Re-hash: hashPassword hook re-hashes raw values, so pass plain.
        $userModel->update($user['id'], ['password' => $data['password']]);

        return $this->respondOK(null, 'Password reset successful. Please login.');
    }

    public function me()
    {
        $userId = $this->userId();

        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if ($user === null) {
            return $this->respondNotFound('User not found.');
        }

        $seller = (new SellerModel())->findByUser($userId);

        return $this->respondOK([
            'id'     => (int) $user['id'],
            'name'   => $user['name'],
            'email'  => $user['email'],
            'mobile' => $user['mobile'],
            'role'   => $this->userRole(),
            'mobile_verified' => (bool) $user['mobile_verified'],
            'email_verified'  => (bool) $user['email_verified'],
            'seller' => $seller,
        ]);
    }
}