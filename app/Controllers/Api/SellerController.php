<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Models\BulkPriceModel;
use App\Models\LeadModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\RfqSellerModel;
use App\Models\SellerModel;
use App\Services\NotificationService;

class SellerController extends ApiController
{
    /**
     * Complete/update own seller business profile. Auth: seller.
     */
    public function updateProfile()
    {
        if ($this->userRole() !== 'seller') {
            return $this->respondForbidden('Only sellers can update business profile.');
        }

        $sellerModel = new SellerModel();
        $seller = $sellerModel->findByUser($this->userId());
        if ($seller === null) {
            return $this->respondNotFound('Business profile not found.');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $patch = [];
        foreach ([
            'business_name', 'gstin', 'owner_name', 'owner_phone', 'address_line1',
            'address_line2', 'city', 'state', 'pincode', 'business_hours', 'about',
            'website', 'infrastructure', 'brands',
        ] as $f) {
            if (isset($data[$f])) {
                $patch[$f] = $data[$f];
            }
        }
        foreach (['years_in_business', 'employees_count'] as $f) {
            if (isset($data[$f]) && $data[$f] !== '') {
                $patch[$f] = (int) $data[$f];
            }
        }
        foreach (['latitude', 'longitude'] as $f) {
            if (isset($data[$f]) && $data[$f] !== '') {
                $patch[$f] = $data[$f];
            }
        }
        if (isset($data['business_category_id']) && $data['business_category_id'] !== '') {
            $patch['business_category_id'] = (int) $data['business_category_id'];
        }
        if (isset($data['logo'])) {
            $patch['logo'] = $data['logo'];
        }

        if (isset($patch['business_name']) && ($patch['business_name'] !== $seller['business_name'])) {
            $patch['slug'] = url_title($patch['business_name'], '-', true) . '-' . $seller['id'];
        }

        // Re-submit for moderation on edited KYC-level fields.
        if (isset($patch['gstin']) && $patch['gstin'] !== $seller['gstin']) {
            $patch['gst_verified'] = 0;
        }

        if ($patch !== []) {
            $sellerModel->update($seller['id'], $patch);
        }

        return $this->respondOK(['seller_id' => (int) $seller['id']], 'Business profile updated.');
    }

    /**
     * Public seller profile page data.
     * GET /api/sellers/{id}
     */
    public function show($id = null)
    {
        $seller = (new SellerModel())->find($id);
        if ($seller === null || $seller['status'] !== 'active') {
            return $this->respondNotFound('Seller not found.');
        }

        $products = (new ProductModel())
            ->where('seller_id', $id)->where('status', 'approved')->where('is_active', 1)
            ->orderBy('is_featured', 'DESC')->orderBy('created_at', 'DESC')->limit(20)->findAll();

        return $this->respondOK([
            'seller'   => $seller,
            'products' => (new \App\Services\ProductService())->enrichMany($products),
        ]);
    }

    // -----------------------------------------------------------------
    // Dashboard
    // -----------------------------------------------------------------

    /**
     * Auth: seller. Returns counts.
     */
    public function dashboard()
    {
        $seller = (new SellerModel())->findByUser($this->userId());
        if ($seller === null) {
            return $this->respondNotFound('Business profile not found.');
        }

        $db = \Config\Database::connect();
        $sid = (int) $seller['id'];

        $productCount = $db->table('products')->where('seller_id', $sid)->countAllResults();
        $leadCount    = $db->table('leads')->where('seller_id', $sid)->countAllResults();
        $hotLeads     = $db->table('leads')->where('seller_id', $sid)->where('status', 'hot')->countAllResults();
        $openRfqs     = $db->table('rfq_sellers')->where('seller_id', $sid)->whereIn('status', ['sent', 'viewed'])->countAllResults();

        return $this->respondOK([
            'product_count' => $productCount,
            'lead_count'    => $leadCount,
            'hot_leads'     => $hotLeads,
            'open_rfqs'     => $openRfqs,
            'profile_status' => $seller['status'],
        ]);
    }

    /**
     * Seller leads. GET /api/sellers/leads?status=new|all
     */
    public function leads()
    {
        $seller = (new SellerModel())->findByUser($this->userId());
        if ($seller === null) {
            return $this->respondNotFound('Business profile not found.');
        }

        $status = $this->request->getGet('status') ?? 'all';

        return $this->respondOK((new LeadModel())->sellerLeads((int) $seller['id'], $status));
    }

    /**
     * Update lead status / notes.
     */
    public function updateLead($id = null)
    {
        $seller = (new SellerModel())->findByUser($this->userId());
        if ($seller === null) {
            return $this->respondNotFound('Business profile not found.');
        }

        $leadModel = new LeadModel();
        $lead = $leadModel->findForSeller((int) $seller['id'], (int) $id);
        if ($lead === null) {
            return $this->respondNotFound('Lead not found.');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $patch = [];

        $validStatuses = ['new', 'interested', 'hot', 'closed', 'rejected', 'lost', 'won'];
        if (isset($data['status']) && in_array($data['status'], $validStatuses, true)) {
            $patch['status'] = $data['status'];
        }
        if (isset($data['notes'])) {
            $patch['notes'] = $data['notes'];
        }
        if (isset($data['reminder_at'])) {
            $patch['reminder_at'] = $data['reminder_at'];
        }

        if ($patch !== []) {
            $leadModel->update($lead['id'], $patch);
        }

        return $this->respondOK(['lead_id' => (int) $lead['id']], 'Lead updated.');
    }
}