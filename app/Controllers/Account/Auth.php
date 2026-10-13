<?php

namespace App\Controllers\Account;

use App\Models\SellerModel;
use App\Models\TransporterModel;
use App\Models\UserModel;
use App\Models\MembershipPlanModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends AccountBase
{
    private const SELLER_PLAN_PRICE = 9999.00;

    public function register()
    {
        if ($this->session->has('uid')) {
            return redirect()->to('account');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name'     => trim((string) $this->request->getPost('name')),
                'mobile'   => trim((string) $this->request->getPost('mobile')),
                'email'    => trim((string) $this->request->getPost('email')),
                'password' => (string) $this->request->getPost('password'),
                'role'     => trim((string) $this->request->getPost('role')),
                'business_name' => trim((string) $this->request->getPost('business_name')),
                'fleet_name'    => trim((string) $this->request->getPost('fleet_name')),
                'vehicle_count' => max(0, (int) $this->request->getPost('vehicle_count')),
                'route_from'    => trim((string) $this->request->getPost('route_from')),
                'route_to'      => trim((string) $this->request->getPost('route_to')),
            ];

            $roleRules = ['role' => 'required|in_list[buyer,seller,transporter]'];
            if ($data['role'] === 'seller') {
                $roleRules['business_name'] = 'required|min_length[2]|max_length[190]';
            }
            if ($data['role'] === 'transporter') {
                $roleRules['fleet_name'] = 'required|min_length[2]|max_length[190]';
            }

            $validation = \Config\Services::validation();
            $validation->setRules($roleRules + [
                'name'     => 'required|min_length[2]|max_length[120]',
                'mobile'   => 'required|regex_match[/^[6-9][0-9]{9}$/]|is_unique[users.mobile]',
                'email'    => 'permit_empty|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]|max_length[128]',
            ], [
                'mobile' => ['is_unique' => 'Number is already registered. Login to your account or use different number.'],
                'email'  => ['is_unique' => 'An account already exists with this email address.'],
            ]);

            if (!$validation->run($data)) {
                return view('account/register', [
                    'errors' => $validation->getErrors(),
                    'old'    => $data,
                    'title'  => 'Create Account',
                ]);
            }

            // Sellers must pay the yearly subscription BEFORE an account is created.
            if ($data['role'] === 'seller') {
                $this->session->set('pending_seller_reg', [
                    'name'          => $data['name'],
                    'mobile'        => $data['mobile'],
                    'email'         => $data['email'],
                    'password'      => $data['password'],
                    'business_name' => $data['business_name'] !== '' ? $data['business_name'] : $data['name'],
                ]);

                return redirect()->to('account/register/seller/checkout');
            }

            if ($data['role'] === 'transporter') {
                return $this->createTransporter($data);
            }

            return $this->createBuyer($data);
        }

        return view('account/register', ['title' => 'Create Account']);
    }

    /**
     * Seller checkout — must pay ₹9,999/year before the account exists.
     */
    public function sellerCheckout()
    {
        if ($this->session->has('uid')) {
            return redirect()->to('account');
        }

        $pending = $this->session->get('pending_seller_reg');
        if (!is_array($pending)) {
            $this->flash('error', 'Start your seller registration first.');
            return redirect()->to('register');
        }

        $plan = (new MembershipPlanModel())->ensureSellerPlan();

        return view('account/seller_checkout', [
            'pending' => $pending,
            'plan'    => $plan,
            'title'   => 'Seller Subscription',
        ]);
    }

    /**
     * Initiate the seller subscription payment (UPI/Razorpay).
     */
    public function sellerPay()
    {
        if ($this->session->has('uid')) {
            return redirect()->to('account');
        }

        $pending = $this->session->get('pending_seller_reg');
        if (!is_array($pending)) {
            $this->flash('error', 'Your registration session expired. Please register again.');
            return redirect()->to('register');
        }

        $plan = (new MembershipPlanModel())->ensureSellerPlan();
        $amountPaise = (int) round(((float) $plan['price']) * 100);

        $gateway = new \App\Libraries\Razorpay();

        // Fail closed when the gateway is unavailable (no keys configured).
        if (!$gateway->isLive() && !$gateway->simulationAllowed()) {
            $this->flash('error', 'Payment gateway is not configured. Please try again later.');
            return redirect()->to('account/register/seller/checkout');
        }

        try {
            $rzpOrder = $gateway->createOrder([
                'amount'   => $amountPaise,
                'currency' => 'INR',
                'receipt'  => 'SELLER-REG-' . time(),
                'notes'    => ['mobile' => $pending['mobile'], 'kind' => 'seller_subscription'],
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Seller subscription createOrder failed: ' . $e->getMessage());
            $this->flash('error', 'Payment gateway error. Please try again.');
            return redirect()->to('account/register/seller/checkout');
        }

        // Hold the gateway order so verify() can match it.
        $this->session->set('seller_rzp_order', [
            'id'       => $rzpOrder['id'],
            'amount'   => (float) $plan['price'],
            'plan_id'  => (int) $plan['id'],
            'is_live'  => $gateway->isLive(),
        ]);

        $sandboxPaymentId = '';
        $sandboxSignature = '';
        if (!$gateway->isLive()) {
            $sandboxPaymentId = 'pay_' . substr(bin2hex(random_bytes(8)), 0, 12);
            $sandboxSignature = $gateway->sandboxSignature($rzpOrder['id'], $sandboxPaymentId);
        }

        return view('account/seller_verify', [
            'pending'            => $pending,
            'plan'               => $plan,
            'amountPaise'        => $amountPaise,
            'rzpKeyId'           => $gateway->keyId(),
            'rzpOrderId'         => $rzpOrder['id'],
            'isLive'             => $gateway->isLive(),
            'sandboxAllowed'     => $gateway->simulationAllowed(),
            'sandboxPaymentId'   => $sandboxPaymentId,
            'sandboxSignature'   => $sandboxSignature,
            'verifyUrl'          => base_url('account/register/seller/pay'),
            'title2'             => 'Complete Payment',
        ]);
    }

    /**
     * Verify the subscription payment. Only on success is the seller account created.
     */
    public function sellerVerify()
    {
        if ($this->session->has('uid')) {
            return redirect()->to('account');
        }

        $pending = $this->session->get('pending_seller_reg');
        $rzp = $this->session->get('seller_rzp_order');
        if (!is_array($pending) || !is_array($rzp)) {
            $this->flash('error', 'Your registration session expired. Please register again.');
            return redirect()->to('register');
        }

        $gateway = new \App\Libraries\Razorpay();

        // Fail closed when the gateway is unavailable (no keys configured).
        if (!$gateway->isLive() && !$gateway->simulationAllowed()) {
            $this->flash('error', 'Payment gateway is not configured. Please try again later.');
            return redirect()->to('account/register/seller/checkout');
        }

        $success = $gateway->verifySignature([
            'razorpay_order_id'   => (string) $this->request->getPost('razorpay_order_id'),
            'razorpay_payment_id' => (string) $this->request->getPost('razorpay_payment_id'),
            'razorpay_signature'  => (string) $this->request->getPost('razorpay_signature'),
        ]);

        if (!$success) {
            $this->flash('error', 'Payment verification failed. Your seller account was not created. Please try again.');
            return redirect()->to('account/register/seller/checkout');
        }

        $db = db_connect();
        $db->transStart();

        $userId = (int) (new UserModel())->insert([
            'role_id'  => 6,
            'name'     => $pending['name'],
            'mobile'   => $pending['mobile'],
            'email'    => $pending['email'] !== '' ? $pending['email'] : null,
            'password' => $pending['password'],
            'status'   => 'active',
        ]);

        $business = $pending['business_name'];
        $slug = url_title($business, '-', true) . '-' . $userId;
        $sellerId = (int) (new SellerModel())->insert([
            'user_id'       => $userId,
            'business_name' => $business,
            'slug'          => $slug,
            'status'        => 'pending',
        ]);

        (new \App\Models\SellerMembershipModel())->grantYearly($sellerId, (int) $rzp['plan_id'], (float) $rzp['amount']);

        // Record the subscription payment (method upi via razorpay).
        $db->table('payments')->insert([
            'order_id' => null,
            'user_id'  => $userId,
            'amount'   => (float) $rzp['amount'],
            'method'   => 'razorpay',
            'txn_id'   => (string) $this->request->getPost('razorpay_payment_id'),
            'status'   => 'success',
            'gateway_response' => json_encode([
                'rzp_order_id'   => (string) $this->request->getPost('razorpay_order_id'),
                'kind'           => 'seller_subscription',
                'plan_id'        => (int) $rzp['plan_id'],
            ]),
        ]);

        $db->transCommit();

        $this->session->remove(['pending_seller_reg', 'seller_rzp_order']);

        $this->session->set([
            'uid'    => $userId,
            'u_name' => $pending['name'],
            'u_role' => 'seller',
        ]);

        $this->flash('success', 'Payment successful! Your yearly seller subscription is active. Complete your business profile.');

        return redirect()->to('account');
    }

    private function createBuyer(array $data)
    {
        $roleMap = ['buyer' => 5, 'seller' => 6, 'transporter' => 7];
        $userId = (int) (new UserModel())->insert([
            'role_id'    => $roleMap[$data['role']],
            'name'       => $data['name'],
            'mobile'     => $data['mobile'],
            'email'      => $data['email'] !== '' ? $data['email'] : null,
            'password'   => $data['password'],
            'status'     => 'active',
        ]);

        $this->session->set([
            'uid'    => $userId,
            'u_name' => $data['name'],
            'u_role' => $data['role'],
        ]);

        $this->flash('success', 'Account created. Welcome to Loha Mandi - GZB!');

        return redirect()->to('account');
    }

    /**
     * Transporter registration — free & instant; also creates a fleet profile.
     */
    private function createTransporter(array $data)
    {
        $db = db_connect();
        $db->transStart();

        $userId = (int) (new UserModel())->insert([
            'role_id'    => 7,
            'name'       => $data['name'],
            'mobile'     => $data['mobile'],
            'email'      => $data['email'] !== '' ? $data['email'] : null,
            'password'   => $data['password'],
            'status'     => 'active',
        ]);

        (new TransporterModel())->insert([
            'user_id'       => $userId,
            'fleet_name'    => $data['fleet_name'] !== '' ? $data['fleet_name'] : $data['name'] . ' Logistics',
            'vehicle_count' => $data['vehicle_count'] > 0 ? $data['vehicle_count'] : 1,
            'route_from'    => $data['route_from'] !== '' ? $data['route_from'] : null,
            'route_to'      => $data['route_to'] !== '' ? $data['route_to'] : null,
            'verified'      => 0,
        ]);

        $db->transComplete();

        $this->session->set([
            'uid'    => $userId,
            'u_name' => $data['name'],
            'u_role' => 'transporter',
        ]);

        $this->flash('success', 'Fleet profile created. Welcome aboard, transporter!');

        return redirect()->to('account');
    }

    public function login()
    {
        if ($this->session->has('uid')) {
            return redirect()->to('account');
        }

        if ($this->request->getMethod() === 'POST') {
            $mobile    = trim((string) $this->request->getPost('mobile'));
            $password  = (string) $this->request->getPost('password');

            $validation = \Config\Services::validation();
            $validation->setRules([
                'mobile'   => 'required|regex_match[/^[6-9][0-9]{9}$/]',
                'password' => 'required',
            ]);

            if (!$validation->run(['mobile' => $mobile, 'password' => $password])) {
                return view('account/login', ['errors' => $validation->getErrors(), 'title' => 'Sign In']);
            }

            $user = (new UserModel())->findByMobile($mobile);
            if ($user === null || empty($user['password']) || !password_verify($password, $user['password'])) {
                return view('account/login', ['errors' => ['password' => 'Invalid mobile or password.'], 'title' => 'Sign In']);
            }

            if ($user['status'] !== 'active') {
                return view('account/login', ['errors' => ['password' => 'Your account is ' . $user['status'] . '.'], 'title' => 'Sign In']);
            }

            $roleCode = (new UserModel())->roleCode($user['role_id']);
            if (!in_array($roleCode, ['buyer', 'seller', 'transporter'], true)) {
                return view('account/login', ['errors' => ['password' => 'This account is not a buyer or seller account.'], 'title' => 'Sign In']);
            }

            $this->session->set([
                'uid'    => (int) $user['id'],
                'u_name' => $user['name'],
                'u_role' => $roleCode,
            ]);

            // WhatsApp: notify on successful login (all roles)
            try {
                $wa = new \App\Libraries\WhatsApp();
                $configured = $wa->isConfigured();
                log_message('info', "WhatsApp login: role=$roleCode configured=" . ($configured ? 'YES' : 'NO'));
                if ($configured) {
                    $result = $wa->sendTemplate(
                        $user['mobile'],
                        'login_success',
                        [$user['name'], 'www.lohamandigzb.online'],
                        $roleCode . '_login',
                        (int) $user['id'],
                        $roleCode
                    );
                    log_message('info', "WhatsApp login template result: " . ($result ? 'SUCCESS' : 'FAILED') . " role=$roleCode mobile={$user['mobile']}");
                } else {
                    log_message('warning', "WhatsApp login: skipped — not configured. role=$roleCode");
                }
            } catch (\Throwable $e) {
                log_message('error', 'WhatsApp login notify failed: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            }

            $gate = $this->sellerGate();
            if ($gate !== null) {
                return $gate;
            }

            return redirect()->to('account');
        }

        return view('account/login', ['title' => 'Sign In']);
    }

    /**
     * Show the Firebase OTP login page (client-side handles Firebase flows).
     */
    public function loginOtp()
    {
        if ($this->session->has('uid')) {
            return redirect()->to('account');
        }

        return view('account/login_otp', ['title' => 'Login via OTP']);
    }

    /**
     * Receive a POST from the client after Firebase verification and create a session.
     * NOTE: This accepts a mobile number and will log in existing users by mobile.
     * For production, verify the Firebase ID token server-side before trusting the request.
     */
    public function loginFirebase()
    {
        if ($this->session->has('uid')) {
            return $this->response->setJSON(['ok' => true, 'redirect' => base_url('account')]);
        }

        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        $idToken = trim((string) $this->request->getPost('idToken'));
        if ($idToken === '') {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Missing idToken']);
        }

        // Verify Firebase ID token using Google's public certs
        try {
            $projectId = getenv('FIREBASE_PROJECT_ID') ?: '';
            if ($projectId === '') {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Firebase project ID not configured']);
            }

            // Optional server-side reCAPTCHA Enterprise verification
            $recaptchaToken = trim((string) $this->request->getPost('recaptcha_token'));
            $recaptchaApiKey = getenv('RECAPTCHA_API_KEY') ?: getenv('RECAPTCHA_KEY') ?: '';
            $recaptchaSiteKey = getenv('RECAPTCHA_SITE_KEY') ?: '';
            if ($recaptchaApiKey !== '' && $recaptchaToken !== '') {
                $assessUrl = 'https://recaptchaenterprise.googleapis.com/v1/projects/' . $projectId . '/assessments?key=' . $recaptchaApiKey;
                $body = json_encode([
                    'event' => [
                        'token' => $recaptchaToken,
                        'siteKey' => $recaptchaSiteKey,
                        'expectedAction' => 'LOGIN',
                    ],
                ]);

                $opts = [
                    'http' => [
                        'method'  => 'POST',
                        'header'  => "Content-Type: application/json\r\n",
                        'content' => $body,
                        'timeout' => 5,
                    ],
                ];
                $context = stream_context_create($opts);
                $res = @file_get_contents($assessUrl, false, $context);
                if ($res === false) {
                    return $this->response->setStatusCode(502)->setJSON(['error' => 'reCAPTCHA assessment failed']);
                }

                $assess = json_decode($res);
                if (!isset($assess->tokenProperties->valid) || $assess->tokenProperties->valid !== true) {
                    return $this->response->setStatusCode(401)->setJSON(['error' => 'reCAPTCHA token invalid']);
                }
                $score = $assess->riskAnalysis->score ?? 0.0;
                if ($score < 0.35) {
                    return $this->response->setStatusCode(403)->setJSON(['error' => 'reCAPTCHA risk too high', 'score' => $score]);
                }
            }

            // Cache Google's Firebase certs in writable/cache to reduce network calls
            $cacheFile = defined('WRITEPATH') ? WRITEPATH . 'cache/firebase_certs.json' : __DIR__ . '/../../writable/cache/firebase_certs.json';
            $certs = null;
            if (is_file($cacheFile)) {
                $cached = json_decode(@file_get_contents($cacheFile), true);
                if (is_array($cached) && isset($cached['fetched_at']) && isset($cached['certs'])) {
                    // refresh every 6 hours
                    if ((time() - (int) $cached['fetched_at']) < 21600) {
                        $certs = $cached['certs'];
                    }
                }
            }

            if (!is_array($certs)) {
                $certsJson = @file_get_contents('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');
                if ($certsJson === false) {
                    return $this->response->setStatusCode(502)->setJSON(['error' => 'Unable to fetch Firebase certs']);
                }

                $certs = json_decode($certsJson, true);
                if (!is_array($certs) || empty($certs)) {
                    return $this->response->setStatusCode(502)->setJSON(['error' => 'Invalid Firebase certs']);
                }

                // try to persist cache, ignore failures
                @file_put_contents($cacheFile, json_encode(['fetched_at' => time(), 'certs' => $certs]));
            }

            // Get kid from token header
            $parts = explode('.', $idToken);
            if (count($parts) < 2) {
                return $this->response->setStatusCode(422)->setJSON(['error' => 'Invalid ID token format']);
            }

            $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
            $kid = is_array($header) ? ($header['kid'] ?? null) : null;

            if (empty($kid) || !isset($certs[$kid])) {
                return $this->response->setStatusCode(401)->setJSON(['error' => 'Unable to find cert for token kid']);
            }

            $cert = $certs[$kid];

            // Prefer the Kreait Firebase Admin SDK when credentials are available.
            // FirebaseAdmin reads FIREBASE_CREDENTIALS / GOOGLE_APPLICATION_CREDENTIALS.
            $phone = '';
            if (class_exists('\App\Libraries\FirebaseAdmin')) {
                try {
                    $auth = \App\Libraries\FirebaseAdmin::auth();
                    $verified = $auth->verifyIdToken($idToken);
                    // Kreait verifies audience/issuer; extract the phone claim.
                    $claims = $verified->claims();
                    $phone = (string) ($claims->get('phone_number') ?? '');
                    if ($phone === '' && is_array($claims)) {
                        $phone = (string) ($claims['phone_number'] ?? '');
                    }
                } catch (\Throwable $e) {
                    log_message('debug', 'Kreait verify failed, falling back to JWT: ' . $e->getMessage());
                    // fall back to JWT verification below
                    $phone = '';
                }
            }

            if ($phone === '') {
                // Decode and validate using public certs as a fallback
                $payload = JWT::decode($idToken, new Key($cert, 'RS256'));

                // Validate claims
                if (($payload->aud ?? '') !== $projectId) {
                    return $this->response->setStatusCode(401)->setJSON(['error' => 'Token audience mismatch']);
                }
                if (($payload->iss ?? '') !== 'https://securetoken.google.com/' . $projectId) {
                    return $this->response->setStatusCode(401)->setJSON(['error' => 'Token issuer invalid']);
                }
                if (empty($payload->sub)) {
                    return $this->response->setStatusCode(401)->setJSON(['error' => 'Token subject missing']);
                }

                // Retrieve phone number claim
                if (!empty($payload->phone_number)) {
                    $phone = (string) $payload->phone_number;
                } elseif (!empty($payload->firebase->identities->phone_number)) {
                    $phone = (string) $payload->firebase->identities->phone_number[0] ?? '';
                }
            }

            if ($phone === '') {
                return $this->response->setStatusCode(422)->setJSON(['error' => 'Phone number not present in token']);
            }

            // Normalize to 10-digit mobile (assumes +91 country)
            $mobile = preg_replace('/[^0-9]/', '', $phone);
            if (strlen($mobile) > 10 && substr($mobile, 0, strlen($mobile) - 10) === '91') {
                $mobile = substr($mobile, -10);
            }
            if (!preg_match('/^[6-9][0-9]{9}$/', $mobile)) {
                return $this->response->setStatusCode(422)->setJSON(['error' => 'Normalized mobile invalid: ' . $mobile]);
            }

            $userModel = new UserModel();
            $user = $userModel->findByMobile($mobile);
            if ($user === null) {
                // Auto-create a buyer account when the OTP-verified mobile is new.
                $autoName = 'Buyer ' . substr($mobile, -4);
                $userId = (int) $userModel->insert([
                    'role_id'         => 5, // buyer
                    'name'            => $autoName,
                    'mobile'          => $mobile,
                    'password'        => bin2hex(random_bytes(8)), // random; user can set a real one via profile
                    'mobile_verified' => 1,
                    'status'          => 'active',
                ]);
                log_message('debug', 'Auto-created buyer via OTP login: ' . $mobile);
                $user = $userModel->find($userId);
            }

            $roleCode = $userModel->roleCode($user['role_id']);

            $this->session->set([
                'uid'    => (int) $user['id'],
                'u_name' => $user['name'],
                'u_role' => $roleCode,
            ]);

            // WhatsApp: notify on successful OTP login (all roles)
            try {
                $wa = new \App\Libraries\WhatsApp();
                $configured = $wa->isConfigured();
                log_message('info', "WhatsApp OTP login: role=$roleCode configured=" . ($configured ? 'YES' : 'NO'));
                if ($configured) {
                    $result = $wa->sendText(
                        $user['mobile'],
                        'You have successfully logged in to www.lohamandigzb.online',
                        $roleCode . '_login_otp',
                        (int) $user['id'],
                        $roleCode
                    );
                    log_message('info', "WhatsApp OTP login sendText result: " . ($result ? 'SUCCESS' : 'FAILED') . " role=$roleCode mobile={$user['mobile']}");
                } else {
                    log_message('warning', "WhatsApp OTP login: skipped — not configured. role=$roleCode");
                }
            } catch (\Throwable $e) {
                log_message('error', 'WhatsApp OTP login notify failed: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            }

            $gate = $this->sellerGate();
            if ($gate !== null) {
                return $this->response->setJSON(['ok' => true, 'redirect' => base_url('account/subscribe')]);
            }

            return $this->response->setJSON(['ok' => true, 'redirect' => base_url('account')]);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'ID token expired']);
        } catch (\UnexpectedValueException $e) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Invalid ID token: ' . $e->getMessage()]);
        } catch (\Throwable $e) {
            log_message('error', 'loginFirebase verify error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Token verification failed']);
        }
    }

    public function logout()
    {
        $this->session->remove(['uid', 'u_name', 'u_role']);
        $this->session->destroy();

        return redirect()->to('/');
    }
}
