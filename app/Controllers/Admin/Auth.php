<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;

class Auth extends AdminBase
{
    public function login()
    {
        if ($this->session->has('admin_id')) {
            return redirect()->to('admin/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $mobile = trim((string) $this->request->getPost('mobile'));
            $password = (string) $this->request->getPost('password');

            $validation = \Config\Services::validation();
            $validation->setRules([
                'mobile'   => 'required|regex_match[/^[6-9][0-9]{9}$/]',
                'password' => 'required',
            ]);

            if (!$validation->run(['mobile' => $mobile, 'password' => $password])) {
                return view('admin/login', ['errors' => $validation->getErrors()]);
            }

            $user = (new UserModel())->findByMobile($mobile);
            if ($user === null || empty($user['password']) || !password_verify($password, $user['password'])) {
                return view('admin/login', ['errors' => ['password' => 'Invalid mobile or password.']]);
            }

            $roleCode = (new UserModel())->roleCode($user['role_id']);
            if (!in_array($roleCode, ['super_admin', 'admin', 'moderator'], true)) {
                return view('admin/login', ['errors' => ['password' => 'You do not have admin access.']]);
            }

            $this->session->set([
                'admin_id'   => (int) $user['id'],
                'admin_name' => $user['name'],
                'admin_role' => $roleCode,
            ]);

            // WhatsApp: notify admin login
            try {
                $wa = new \App\Libraries\WhatsApp();
                $configured = $wa->isConfigured();
                log_message('info', "WhatsApp admin login: role=$roleCode configured=" . ($configured ? 'YES' : 'NO'));
                if ($configured) {
                    $result = $wa->sendText(
                        $user['mobile'],
                        'You have successfully logged in to www.lohamandigzb.online (Admin Panel)',
                        'admin_login',
                        (int) $user['id'],
                        'admin'
                    );
                    log_message('info', "WhatsApp admin login sendText result: " . ($result ? 'SUCCESS' : 'FAILED') . " mobile={$user['mobile']}");
                }
            } catch (\Throwable $e) {
                log_message('error', 'WhatsApp admin login notify failed: ' . $e->getMessage());
            }

            return redirect()->to('admin/dashboard');
        }

        return view('admin/login');
    }

    public function logout()
    {
        $this->session->remove(['admin_id', 'admin_name', 'admin_role']);
        $this->session->destroy();

        return redirect()->to('admin/login');
    }
}
