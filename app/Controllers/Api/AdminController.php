<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Models\AuditLogModel;
use App\Models\ProductModel;
use App\Models\SellerModel;
use App\Models\UserModel;
use App\Services\NotificationService;
use App\Models\NotificationModel;

/**
 * AdminController — platform moderation & oversight. Auth: super_admin/admin/moderator.
 */
class AdminController extends ApiController
{
    private function isAdmin(): bool
    {
        return in_array($this->userRole(), ['super_admin', 'admin', 'moderator'], true);
    }

    private function guardAdmin()
    {
        if (!$this->isAdmin()) {
            return $this->respondForbidden('Admin access required.');
        }
        return null;
    }

    /**
     * Pending products awaiting moderation.
     */
    public function pendingProducts()
    {
        if ($r = $this->guardAdmin()) {
            return $r;
        }
        $rows = (new ProductModel())->where('status', 'pending')->orderBy('created_at', 'ASC')->findAll();

        return $this->respondOK((new \App\Services\ProductService())->enrichMany($rows));
    }

    /**
     * Approve / reject a product. POST /api/admin/products/{id}/moderate {status: approved|rejected}
     */
    public function moderateProduct($id = null)
    {
        if ($r = $this->guardAdmin()) {
            return $r;
        }
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $status = $data['status'] ?? null;

        if (!in_array($status, ['approved', 'rejected'], true)) {
            return $this->respondValidationErrors(['status' => 'Must be approved or rejected.']);
        }

        $productModel = new ProductModel();
        $product = $productModel->find($id);
        if ($product === null) {
            return $this->respondNotFound('Product not found.');
        }

        $productModel->update($product['id'], [
            'status'    => $status,
            'is_active' => $status === 'approved' ? 1 : 0,
        ]);

        (new AuditLogModel())->log($this->userId(), 'product_moderate', 'product', (int) $product['id'], $product, ['status' => $status]);

        return $this->respondOK(['product_id' => (int) $product['id'], 'status' => $status], 'Product ' . $status . '.');
    }

    /**
     * Pending seller business profiles.
     */
    public function pendingSellers()
    {
        if ($r = $this->guardAdmin()) {
            return $r;
        }
        return $this->respondOK((new SellerModel())->where('status', 'pending')->findAll());
    }

    /**
     * Approve / reject a seller business profile.
     * POST /api/admin/sellers/{id}/moderate {status: active|rejected}
     */
    public function moderateSeller($id = null)
    {
        if ($r = $this->guardAdmin()) {
            return $r;
        }
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $status = $data['status'] ?? null;

        if (!in_array($status, ['active', 'rejected'], true)) {
            return $this->respondValidationErrors(['status' => 'Must be active or rejected.']);
        }

        $sellerModel = new SellerModel();
        $seller = $sellerModel->find($id);
        if ($seller === null) {
            return $this->respondNotFound('Seller profile not found.');
        }

        $sellerModel->update($seller['id'], ['status' => $status]);

        // Sync user status.
        (new UserModel())->update($seller['user_id'], ['status' => $status === 'active' ? 'active' : 'suspended']);

        (new AuditLogModel())->log($this->userId(), 'seller_moderate', 'seller', (int) $seller['id'], $seller, ['status' => $status]);

        // Notify seller owner.
        (new NotificationService(new NotificationModel()))->send(
            (int) $seller['user_id'],
            'Business profile ' . $status,
            'Your business profile was ' . $status . ' by the moderator team.',
            'in_app',
            'seller',
            (int) $seller['id']
        );

        return $this->respondOK(['seller_id' => (int) $seller['id'], 'status' => $status], 'Seller ' . $status . '.');
    }
}