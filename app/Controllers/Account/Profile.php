<?php

namespace App\Controllers\Account;

use App\Models\SellerModel;
use App\Models\TransporterModel;
use App\Models\UserModel;

class Profile extends AccountBase
{
    public function index()
    {
        $user = (new UserModel())->find($this->uid());
        if ($user === null) {
            $this->flash('error', 'User not found.');
            return redirect()->to('logout');
        }

        $seller = $this->uRole() === 'seller' ? (new SellerModel())->findByUser($this->uid()) : null;
        $transporter = $this->uRole() === 'transporter' ? (new TransporterModel())->findByUser($this->uid()) : null;

        // Force the subscription paywall before the profile is usable.
        $membership = null;
        if ($seller !== null) {
            $memModel = new \App\Models\SellerMembershipModel();
            if (!$memModel->isSubscribed((int) $seller['id'])) {
                $this->flash('error', 'Your seller subscription is not active. Please complete payment to continue.');
                return redirect()->to('account/subscribe');
            }
            $membership = $memModel->latestWithPlan((int) $seller['id']);
        }

        if ($this->request->getMethod() === 'POST') {
            $name  = trim((string) $this->request->getPost('name'));
            $email = trim((string) $this->request->getPost('email'));
            $newPwd = (string) $this->request->getPost('password');

            $userModel = new UserModel();
            $rules = [
                'name'  => 'required|min_length[2]|max_length[120]',
                'email' => 'permit_empty|valid_email|is_unique[users.email,id,' . $this->uid() . ']',
            ];
            if ($newPwd !== '') {
                $rules['password'] = 'min_length[6]|max_length[128]';
            }

            $validation = \Config\Services::validation();
            if (!$validation->setRules($rules)->run(['name' => $name, 'email' => $email, 'password' => $newPwd])) {
                $this->flash('error', implode(' ', array_values($validation->getErrors())));
                return redirect()->back();
            }

            $fields = [
                'name'  => $name,
                'email' => $email !== '' ? $email : null,
            ];
            if ($newPwd !== '') {
                $fields['password'] = $newPwd;
            }
            $userModel->update($this->uid(), $fields);

            // Seller business profile updates.
            if ($seller !== null) {
                $sellerModel = new SellerModel();
                $sfields = [
                    'business_name' => trim((string) $this->request->getPost('business_name')),
                    'owner_name'    => trim((string) $this->request->getPost('owner_name')),
                    'owner_phone'   => trim((string) $this->request->getPost('owner_phone')),
                    'gstin'         => trim((string) $this->request->getPost('gstin')),
                    'city'          => trim((string) $this->request->getPost('city')),
                    'state'         => trim((string) $this->request->getPost('state')),
                    'pincode'       => trim((string) $this->request->getPost('pincode')),
                    'address_line1' => trim((string) $this->request->getPost('address_line1')),
                    'website'       => trim((string) $this->request->getPost('website')),
                    'about'         => trim((string) $this->request->getPost('about')),
                ];
                if ($sfields['business_name'] !== '' && $sfields['business_name'] !== $seller['business_name']) {
                    $sfields['slug'] = url_title($sfields['business_name'], '-', true) . '-' . $this->uid();
                }
                foreach ($sfields as $k => $v) {
                    if ($v === '') {
                        $sfields[$k] = null;
                    }
                }

                // Handle seller logo upload.
                $logoFile = $this->request->getFile('logo');
                if ($logoFile !== null && $logoFile->isValid() && !$logoFile->hasMoved()) {
                    if (!in_array($logoFile->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)) {
                        $this->flash('error', 'Logo must be a JPG, PNG or WEBP image.');
                        return redirect()->back();
                    }
                    if ($logoFile->getSize() > 2 * 1024 * 1024) {
                        $this->flash('error', 'Logo must be smaller than 2 MB.');
                        return redirect()->back();
                    }

                    $dir = WRITEPATH . 'uploads/sellers';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0775, true);
                    }
                    $newName = $logoFile->getRandomName();
                    $logoFile->move($dir, $newName);
                    $sfields['logo'] = 'sellers/' . $newName;

                    // Delete the old logo file (only if it lives under sellers/).
                    if (!empty($seller['logo']) && strpos($seller['logo'], 'sellers/') === 0) {
                        $old = WRITEPATH . 'uploads/' . basename($seller['logo']);
                        if (is_file($old)) {
                            @unlink($old);
                        }
                    }
                }

                $sellerModel->update($seller['id'], $sfields);
            }

            // Transporter fleet profile updates.
            if ($transporter !== null) {
                $transportModel = new TransporterModel();
                $tfields = [
                    'fleet_name'    => trim((string) $this->request->getPost('fleet_name')),
                    'vehicle_count' => max(0, (int) $this->request->getPost('vehicle_count')),
                    'route_from'    => trim((string) $this->request->getPost('route_from')),
                    'route_to'      => trim((string) $this->request->getPost('route_to')),
                ];
                foreach ($tfields as $k => $v) {
                    if ($v === '') {
                        $tfields[$k] = null;
                    }
                }
                if ($tfields['vehicle_count'] <= 0) {
                    $tfields['vehicle_count'] = 1;
                }
                $transportModel->update($transporter['id'], $tfields);
            }

            $this->session->set('u_name', $name);
            $this->flash('success', 'Profile updated.');

            return redirect()->to('account/profile');
        }

        return $this->render('account/profile', [
            'user'      => $user,
            'seller'    => $seller,
            'transporter' => $transporter,
            'membership'  => $membership,
            'activeNav' => 'profile',
        ], 'Profile');
    }
}
