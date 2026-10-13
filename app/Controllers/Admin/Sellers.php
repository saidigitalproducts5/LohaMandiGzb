<?php

namespace App\Controllers\Admin;

use App\Models\AuditLogModel;
use App\Models\LocationModel;
use App\Models\SellerModel;
use App\Models\UserModel;
use App\Services\NotificationService;
use App\Models\NotificationModel;

class Sellers extends AdminBase
{
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sellers s')
            ->select('s.*, u.name AS owner_name, u.mobile AS owner_mobile, u.email AS owner_email')
            ->join('users u', 'u.id = s.user_id', 'left');

        $q = trim((string) $this->request->getGet('q'));
        $status = trim((string) $this->request->getGet('status'));
        $city = trim((string) $this->request->getGet('city'));
        $premium = (int) ($this->request->getGet('premium') ?? 0);
        $verified = (int) ($this->request->getGet('verified') ?? 0);

        if ($q !== '') {
            $builder->groupStart()->like('s.business_name', $q)->orLike('s.gstin', $q)->orLike('u.mobile', $q)->groupEnd();
        }
        if ($status !== '' && $status !== 'all') {
            $builder->where('s.status', $status);
        }
        if ($city !== '') {
            $builder->where('s.city', $city);
        }
        if ($premium === 1) {
            $builder->where('s.is_premium', 1);
        }
        if ($verified === 1) {
            $builder->where('s.gst_verified', 1);
        }

        $total = (clone $builder)->countAllResults(false);
        $limit = 25;
        $page  = max((int) ($this->request->getGet('page') ?? 1), 1);
        $rows = $builder->orderBy('s.status = "pending"', 'DESC', false)->orderBy('s.id', 'DESC')->limit($limit, ($page - 1) * $limit)->get()->getResultArray();

        $cities = $db->table('sellers')->select('city')->distinct()->where('city IS NOT NULL')->get()->getResultArray();

        return $this->render('admin/sellers', [
            'sellers' => $rows,
            'cities'  => array_column($cities, 'city'),
            'total'   => $total,
            'page'    => $page,
            'limit'   => $limit,
            'filters' => ['q' => $q, 'status' => $status, 'city' => $city, 'premium' => $premium, 'verified' => $verified],
            'activeNav' => 'sellers',
        ], 'Sellers');
    }

    public function show($id)
    {
        $seller = (new SellerModel())->find($id);
        if ($seller === null) {
            $this->flash('error', 'Seller not found.');
            return redirect()->to('admin/sellers');
        }

        $db = \Config\Database::connect();
        $seller['user'] = $db->table('users')->where('id', $seller['user_id'])->get()->getRowArray();
        $seller['product_count'] = (int) $db->table('products')->where('seller_id', (int) $id)->countAllResults();
        $seller['lead_count'] = (int) $db->table('leads')->where('seller_id', (int) $id)->countAllResults();
        $seller['rfq_count'] = (int) $db->table('rfq_sellers')->where('seller_id', (int) $id)->countAllResults();
        $seller['membership'] = $db->table('seller_membership sm')
            ->select('sm.*, mp.name AS plan_name')
            ->join('membership_plans mp', 'mp.id = sm.plan_id', 'left')
            ->where('sm.seller_id', (int) $id)
            ->orderBy('sm.id', 'DESC')->limit(1)->get()->getRowArray();

        return $this->render('admin/seller_view', [
            'seller' => $seller,
            'activeNav' => 'sellers',
        ], 'Seller: ' . $seller['business_name']);
    }

    /**
     * Moderate: active / rejected / suspended / pending.
     */
    public function moderate($id)
    {
        $status = trim((string) $this->request->getPost('status'));
        if (!in_array($status, ['pending', 'active', 'rejected', 'suspended'], true)) {
            $this->flash('error', 'Invalid status.');
            return redirect()->back();
        }

        $sellerModel = new SellerModel();
        $seller = $sellerModel->find($id);
        if ($seller === null) {
            $this->flash('error', 'Seller not found.');
            return redirect()->to('admin/sellers');
        }

        $sellerModel->update($seller['id'], ['status' => $status]);
        (new UserModel())->update($seller['user_id'], ['status' => in_array($status, ['active', 'pending'], true) ? 'active' : 'suspended']);
        (new AuditLogModel())->log($this->adminId(), 'seller_moderate', 'seller', (int) $seller['id'], $seller['status'], $status);
        (new NotificationService(new NotificationModel()))->send(
            (int) $seller['user_id'],
            'Business profile ' . $status,
            'Your business profile on LOHA MANDI is now: ' . $status . '.',
            'in_app', 'seller', (int) $seller['id']
        );

        $this->flash('success', 'Seller status updated to "' . $status . '".');

        return redirect()->to('admin/sellers');
    }

    /**
     * Edit KYC/location fields inline.
     */
    public function update($id)
    {
        $sellerModel = new SellerModel();
        $seller = $sellerModel->find($id);
        if ($seller === null) {
            $this->flash('error', 'Seller not found.');
            return redirect()->to('admin/sellers');
        }

        $patch = [];
        foreach (['gstin', 'city', 'state', 'pincode', 'address_line1', 'business_hours', 'years_in_business', 'employees_count'] as $f) {
            if ($this->request->getPost($f) !== null) {
                $patch[$f] = $this->request->getPost($f);
            }
        }
        $gstVerified = (int) $this->request->getPost('gst_verified');
        $isVerified  = (int) $this->request->getPost('is_verified');
        $isPremium   = (int) $this->request->getPost('is_premium');
        $patch['gst_verified'] = $gstVerified;
        $patch['is_verified']  = $isVerified;
        $patch['is_premium']   = $isPremium;

        $sellerModel->update($seller['id'], $patch);
        (new AuditLogModel())->log($this->adminId(), 'seller_update', 'seller', (int) $seller['id']);

        $this->flash('success', 'Seller details updated.');

        return redirect()->to('admin/sellers/' . (int) $id);
    }
}
