<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Models\LeadModel;
use App\Models\NotificationModel;
use App\Models\QuotationModel;
use App\Models\RfqModel;
use App\Models\RfqSellerModel;
use App\Models\SellerModel;
use App\Models\UserModel;
use App\Services\NotificationService;

class QuotationController extends ApiController
{
    /**
     * Seller creates a quotation for an RFQ.
     * POST /api/quotations
     */
    public function create()
    {
        if ($this->userRole() !== 'seller') {
            return $this->respondForbidden('Only sellers can create quotations.');
        }

        $seller = (new SellerModel())->findByUser($this->userId());
        if ($seller === null) {
            return $this->respondNotFound('Business profile not found.');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'rfq_id'      => 'required|integer|is_not_unique[rfqs.id]',
            'quantity'    => 'permit_empty|greater_than[0]|decimal',
            'unit_price'  => 'required|greater_than[0]|decimal',
            'gst_rate'    => 'permit_empty|decimal',
            'discount'    => 'permit_empty|decimal',
            'transport_charge' => 'permit_empty|decimal',
            'valid_until' => 'permit_empty|valid_date',
        ]);
        if (!$validation->run($data)) {
            return $this->respondValidationErrors($validation->getErrors());
        }

        $rfq = (new RfqModel())->find((int) $data['rfq_id']);
        if ($rfq === null) {
            return $this->respondNotFound('RFQ not found.');
        }

        // Price math.
        $quantity = $data['quantity'] ?? $rfq['quantity'];
        $unitPrice = (float) $data['unit_price'];
        $gstRate   = (float) ($data['gst_rate'] ?? 18.00);
        $discount  = (float) ($data['discount'] ?? 0);
        $transport = (float) ($data['transport_charge'] ?? 0);
        $other     = (float) ($data['other_charges'] ?? 0);

        $base     = $quantity * $unitPrice;
        $gst      = $base * ($gstRate / 100);
        $total    = $base + $gst + $transport + $other - $discount;

        $quotationModel = new QuotationModel();
        $quoteId = (int) $quotationModel->insert([
            'rfq_id'           => (int) $rfq['id'],
            'seller_id'        => (int) $seller['id'],
            'buyer_id'         => (int) $rfq['buyer_id'],
            'quote_number'     => $quotationModel->nextQuoteNumber(),
            'product_name'     => $data['product_name'] ?? $rfq['product_name'],
            'quantity'         => $quantity,
            'unit'             => $data['unit'] ?? ($rfq['unit'] ?? 'ton'),
            'unit_price'       => $unitPrice,
            'base_amount'      => round($base, 2),
            'gst_rate'         => $gstRate,
            'gst_amount'       => round($gst, 2),
            'discount'         => $discount,
            'transport_charge' => $transport,
            'other_charges'    => $other,
            'total_amount'     => round($total, 2),
            'payment_terms'    => $data['payment_terms'] ?? null,
            'valid_until'      => $data['valid_until'] ?? date('Y-m-d', strtotime('+7 days')),
            'notes'            => $data['notes'] ?? null,
            'status'           => 'sent',
        ]);

        // Update RFQ + match status.
        (new RfqModel())->update($rfq['id'], ['status' => 'quoted']);
        if (!empty($data['rfq_id'])) {
            $rfqSeller = (new RfqSellerModel())->where('rfq_id', $rfq['id'])->where('seller_id', $seller['id'])->first();
            if ($rfqSeller !== null) {
                (new RfqSellerModel())->update($rfqSeller['id'], ['status' => 'quoted']);
            }
        }

        // Notify the buyer.
        (new NotificationService(new NotificationModel()))->send(
            (int) $rfq['buyer_id'],
            'Quotation received',
            $seller['business_name'] . ' quoted ' . $quoteId . ' for ' . $rfq['product_name'] . '.',
            'in_app',
            'quotation',
            $quoteId
        );

        return $this->respondCreated(['quotation_id' => $quoteId, 'total_amount' => round($total, 2)], 'Quotation sent.');
    }

    /**
     * Seller's quotations. GET /api/quotations/mine
     */
    public function mine()
    {
        $seller = (new SellerModel())->findByUser($this->userId());
        if ($seller === null) {
            return $this->respondNotFound('Business profile not found.');
        }

        return $this->respondOK((new QuotationModel())->sellerQuotations((int) $seller['id']));
    }

    /**
     * Buyer accepts/rejects a quotation.
     * POST /api/quotations/{id}/status  {status: accepted|rejected}
     */
    public function respondToQuote($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $status = $data['status'] ?? null;

        if (!in_array($status, ['accepted', 'rejected'], true)) {
            return $this->respondValidationErrors(['status' => 'Must be accepted or rejected.']);
        }

        $quotationModel = new QuotationModel();
        $quote = $quotationModel->find($id);
        if ($quote === null) {
            return $this->respondNotFound('Quotation not found.');
        }
        if ((int) $quote['buyer_id'] !== (int) $this->userId()) {
            return $this->respondForbidden('You cannot act on this quotation.');
        }

        $quotationModel->update($quote['id'], ['status' => $status]);

        if ($status === 'accepted' && !empty($quote['rfq_id'])) {
            (new RfqModel())->update($quote['rfq_id'], ['status' => 'accepted']);
        }

        return $this->respondOK(['quotation_id' => (int) $quote['id']], 'Quotation ' . $status . '.');
    }
}