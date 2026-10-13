<?php

namespace App\Controllers\Account;

use App\Models\QuotationModel;
use App\Models\RfqModel;
use App\Models\SellerModel;

class Quotations extends AccountBase
{
    public function index()
    {
        $isSeller = $this->uRole() === 'seller';
        $db = \Config\Database::connect();

        if ($isSeller) {
            $seller = (new SellerModel())->findByUser($this->uid());
            if ($seller === null) {
                $this->flash('error', 'Seller profile not found.');
                return redirect()->to('account');
            }
            $rows = $db->table('quotations q')
                ->select('q.*, r.product_name AS rfq_product')
                ->join('rfqs r', 'r.id = q.rfq_id', 'left')
                ->where('q.seller_id', $seller['id'])
                ->orderBy('q.created_at', 'DESC')
                ->get()->getResultArray();

            return $this->render('account/quotations', [
                'rows'      => $rows,
                'isSeller'  => true,
                'activeNav' => 'quotations',
            ], 'Quotations');
        }

        $rows = $db->table('quotations q')
            ->select('q.*, s.business_name')
            ->join('sellers s', 's.id = q.seller_id', 'left')
            ->where('q.buyer_id', $this->uid())
            ->orderBy('q.created_at', 'DESC')
            ->get()->getResultArray();

        return $this->render('account/quotations', [
            'rows'      => $rows,
            'isSeller'  => false,
            'activeNav' => 'quotations',
        ], 'Quotations');
    }

    public function create($rfqId)
    {
        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found.');
            return redirect()->to('account');
        }

        $rfq = (new RfqModel())->find((int) $rfqId);
        if ($rfq === null) {
            $this->flash('error', 'RFQ not found.');
            return redirect()->to('account/rfqs');
        }

// Must be a matched seller.
        $matched = db_connect()->table('rfq_sellers')
            ->where('rfq_id', (int) $rfqId)->where('seller_id', $seller['id'])->countAllResults();
        if ($matched === 0) {
            $this->flash('error', 'This RFQ is not shared with you.');
            return redirect()->to('account/rfqs');
        }

        $gstRate = (float) ($this->request->getPost('gst_rate') ?? 18);

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'unit_price'   => (float) $this->request->getPost('unit_price'),
                'gst_rate'     => $gstRate,
                'discount'     => (float) ($this->request->getPost('discount') ?? 0),
                'transport'    => (float) ($this->request->getPost('transport_charge') ?? 0),
                'other'        => (float) ($this->request->getPost('other_charges') ?? 0),
                'payment_terms'=> trim((string) $this->request->getPost('payment_terms')),
                'valid_until'  => trim((string) $this->request->getPost('valid_until')),
                'notes'        => trim((string) $this->request->getPost('notes')),
            ];

            $validation = \Config\Services::validation();
            $validation->setRules(['unit_price' => 'required|greater_than[0]|numeric']);

            if (!$validation->run($data)) {
                $this->flash('error', 'Unit price must be greater than 0.');
                return redirect()->back();
            }

            $quantity  = (float) $rfq['quantity'];
            $baseAmt   = $data['unit_price'] * $quantity;
            $gstAmt    = $baseAmt * $data['gst_rate'] / 100;
            $total     = $baseAmt - $data['discount'] + $data['transport'] + $data['other'] + $gstAmt;

            $quoteId = (int) (new QuotationModel())->insert([
                'rfq_id'        => (int) $rfqId,
                'seller_id'     => (int) $seller['id'],
                'buyer_id'      => (int) $rfq['buyer_id'],
                'quote_number'  => (new QuotationModel())->nextQuoteNumber(),
                'product_name'  => $rfq['product_name'],
                'quantity'      => $quantity,
                'unit'          => $rfq['unit'],
                'unit_price'    => $data['unit_price'],
                'base_amount'   => $baseAmt,
                'gst_rate'      => $data['gst_rate'],
                'gst_amount'    => $gstAmt,
                'discount'      => $data['discount'],
                'transport_charge' => $data['transport'],
                'other_charges' => $data['other'],
                'total_amount'  => $total,
                'payment_terms' => $data['payment_terms'] !== '' ? $data['payment_terms'] : null,
                'valid_until'   => $data['valid_until'] !== '' ? $data['valid_until'] : null,
                'notes'         => $data['notes'] !== '' ? $data['notes'] : null,
                'status'        => 'sent',
            ]);

            // RFQ becomes "quoted".
            if ($rfq['status'] === 'open') {
                (new RfqModel())->update((int) $rfqId, ['status' => 'quoted']);
            }

            // Notify the buyer.
            (new \App\Models\NotificationModel())->push(
                (int) $rfq['buyer_id'],
                'Quotation received for ' . $rfq['product_name'],
                $seller['business_name'] . ' quoted ₹' . number_format((float) $total, 2) . '.',
                'quotation',
                (int) $quoteId
            );

            $this->flash('success', 'Quotation sent to the buyer.');

            return redirect()->to('account/quotations');
        }

        return $this->render('account/quotation_form', [
            'rfq'      => $rfq,
            'gstRate'  => $gstRate,
            'activeNav'=> 'quotations',
        ], 'Send Quotation');
    }
}