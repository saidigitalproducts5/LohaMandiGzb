<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Models\CategoryModel;
use App\Models\RfqModel;
use App\Models\RfqSellerModel;
use App\Models\SellerModel;
use App\Services\RfqMatchService;

class RfqController extends ApiController
{
    /**
     * Buyer creates an RFQ. Auth: buyer role.
     * POST /api/rfqs
     */
    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'product_name' => 'required|max_length[190]',
            'quantity'     => 'required|greater_than[0]|decimal',
            'unit'         => 'permit_empty|max_length[16]',
            'category_id'  => 'permit_empty|integer|is_not_unique[categories.id]',
            'specification' => 'permit_empty',
            'required_date' => 'permit_empty|valid_date',
            'delivery_location' => 'permit_empty|max_length[190]',
            'city'         => 'permit_empty|max_length[64]',
            'state'        => 'permit_empty|max_length[64]',
            'budget_min'   => 'permit_empty|decimal',
            'budget_max'   => 'permit_empty|decimal',
        ]);
        if (!$validation->run($data)) {
            return $this->respondValidationErrors($validation->getErrors());
        }

        $rfqModel = new RfqModel();
        $rfqId = (int) $rfqModel->insert([
            'buyer_id'         => $this->userId(),
            'product_name'     => $data['product_name'],
            'category_id'      => !empty($data['category_id']) ? (int) $data['category_id'] : null,
            'quantity'         => $data['quantity'],
            'unit'             => $data['unit'] ?? 'ton',
            'specification'    => $data['specification'] ?? null,
            'required_date'    => $data['required_date'] ?? null,
            'delivery_location' => $data['delivery_location'] ?? null,
            'city'             => $data['city'] ?? null,
            'state'            => $data['state'] ?? null,
            'budget_min'       => $data['budget_min'] ?? null,
            'budget_max'       => $data['budget_max'] ?? null,
            'status'           => 'open',
            'source'           => 'web',
        ]);

        // Auto-match sellers that carry this category and notify them.
        $matched = (new RfqMatchService())->matchAndNotify($rfqId, $this->userId(), $data);

        $notifications = new \App\Services\NotificationService(new \App\Models\NotificationModel());
        $notifications->send(
            $this->userId(),
            'RFQ submitted',
            'Your RFQ for ' . $data['product_name'] . ' is live. ' . count($matched) . ' seller(s) matched.',
            'in_app',
            'rfq',
            $rfqId
        );

        return $this->respondCreated([
            'rfq_id'           => $rfqId,
            'matched_sellers'  => $matched,
        ], 'RFQ created. Matching sellers have been notified.');
    }

    /**
     * Buyer's own RFQs. Auth: any.
     */
    public function mine()
    {
        $rfqs = (new RfqModel())->buyerRfqs($this->userId());

        return $this->respondOK($rfqs);
    }

    /**
     * Public RFQ detail (buyer + matched sellers). 
     */
    public function show($id = null)
    {
        $rfq = (new RfqModel())->find($id);
        if ($rfq === null) {
            return $this->respondNotFound('RFQ not found.');
        }

        // Owner can see everything; public sees the RFQ + count only.
        $isOwner = (int) $rfq['buyer_id'] === (int) $this->userId();

        if ($isOwner || $this->userRole() === 'admin' || $this->userRole() === 'super_admin') {
            $rfq['sellers'] = (new RfqSellerModel())->sellersForRfq((int) $id);
        } else {
            $rfq['seller_match_count'] = count((new RfqSellerModel())->matchingSellerIds((int) $id));
            unset($rfq['buyer_id']);
        }

        return $this->respondOK($rfq);
    }

    /**
     * Seller: list RFQs matched to me. Auth: seller.
     */
    public function forSellers()
    {
        $seller = (new SellerModel())->findByUser($this->userId());
        if ($seller === null) {
            return $this->respondNotFound('Business profile not found.');
        }

        return $this->respondOK((new RfqSellerModel())->rfqsForSeller((int) $seller['id']));
    }

    /**
     * Seller: mark RFQ as viewed / responded. Auth: seller.
     */
    public function sellerAction($id = null)
    {
        $seller = (new SellerModel())->findByUser($this->userId());
        if ($seller === null) {
            return $this->respondNotFound('Business profile not found.');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $action = $data['action'] ?? 'viewed';

        $db = \Config\Database::connect();
        $row = $db->table('rfq_sellers')
            ->where('rfq_id', (int) $id)
            ->where('seller_id', (int) $seller['id'])
            ->get()->getRow();

        if ($row === null) {
            return $this->respondNotFound('RFQ not matched to you.');
        }

        $update = [];
        if ($action === 'viewed') {
            $update['viewed_at'] = date('Y-m-d H:i:s');
            $update['status']    = 'viewed';
        } elseif ($action === 'rejected') {
            $update['status'] = 'rejected';
        } else {
            return $this->respondValidationErrors(['action' => 'Invalid action.']);
        }

        (new RfqSellerModel())->update((int) $row->id, $update);

        return $this->respondOK(null, 'RFQ status updated.');
    }
}