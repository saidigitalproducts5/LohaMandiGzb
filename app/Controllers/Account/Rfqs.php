<?php

namespace App\Controllers\Account;

use App\Models\CategoryModel;
use App\Models\QuotationModel;
use App\Models\RfqModel;
use App\Models\RfqSellerModel;
use App\Models\SellerModel;
use App\Services\RfqMatchService;
use CodeIgniter\Files\File;

class Rfqs extends AccountBase
{
    protected $helpers = ['url'];

    /** Buyer: list my RFQs. */
    public function index()
    {
        if ($this->uRole() === 'seller') {
            return $this->sellerRfqs();
        }

        $status = trim((string) $this->request->getGet('status'));
        $db = \Config\Database::connect();

        $builder = $db->table('rfqs r')
            ->select('r.*, (SELECT COUNT(*) FROM quotations q WHERE q.rfq_id = r.id) AS quote_count')
            ->where('r.buyer_id', $this->uid());
        if ($status !== '' && $status !== 'all') {
            $builder->where('r.status', $status);
        }

        $rows = $builder->orderBy('r.created_at', 'DESC')->get()->getResultArray();

return $this->render('account/rfqs', [
            'rfqs'      => $rows,
            'isBuyer'   => true,
            'status'    => $status,
            'activeNav' => 'rfqs',
        ], 'My RFQs');
    }

    public function create()
    {
        if ($this->uRole() === 'seller') {
            $this->flash('error', 'Only buyers can post RFQs.');
            return redirect()->to('account/rfqs');
        }

        $categories = (new CategoryModel())->activeCategories();

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'product_name'      => trim((string) $this->request->getPost('product_name')),
                'category_id'       => (int) $this->request->getPost('category_id'),
                'quantity'          => trim((string) $this->request->getPost('quantity')),
                'unit'              => trim((string) $this->request->getPost('unit')),
                'specification'     => trim((string) $this->request->getPost('specification')),
                'required_date'     => trim((string) $this->request->getPost('required_date')),
                'delivery_location' => trim((string) $this->request->getPost('delivery_location')),
                'city'              => trim((string) $this->request->getPost('city')),
                'state'             => trim((string) $this->request->getPost('state')),
                'budget_min'        => trim((string) $this->request->getPost('budget_min')),
                'budget_max'        => trim((string) $this->request->getPost('budget_max')),
            ];

            $validation = \Config\Services::validation();
            $validation->setRules([
                'product_name'  => 'required|min_length[3]|max_length[190]',
                'category_id'   => 'permit_empty|is_natural_no_zero',
                'quantity'      => 'required|greater_than[0]|numeric',
                'unit'          => 'required|max_length[16]',
                'required_date' => 'permit_empty|valid_date',
                'specification' => 'permit_empty|max_length[2000]',
            ]);

            if (!$validation->run($data)) {
                return $this->render('account/rfq_form', [
                    'errors'     => $validation->getErrors(),
                    'old'        => $data,
                    'categories' => $categories,
                    'activeNav'  => 'rfqs',
                ], 'Post RFQ');
            }

            $document = $this->storeDoc('document', 'documents');
            $photo    = $this->storeDoc('photo', 'documents');

            $rfqId = (int) (new RfqModel())->insert([
                'buyer_id'          => $this->uid(),
                'product_name'      => $data['product_name'],
                'category_id'       => $data['category_id'] > 0 ? $data['category_id'] : null,
                'quantity'          => (float) $data['quantity'],
                'unit'              => $data['unit'],
                'specification'     => $data['specification'] !== '' ? $data['specification'] : null,
                'required_date'     => $data['required_date'] !== '' ? $data['required_date'] : null,
                'delivery_location' => $data['delivery_location'] !== '' ? $data['delivery_location'] : null,
                'city'              => $data['city'] !== '' ? $data['city'] : null,
                'state'             => $data['state'] !== '' ? $data['state'] : null,
                'budget_min'        => $data['budget_min'] !== '' ? (float) $data['budget_min'] : null,
                'budget_max'        => $data['budget_max'] !== '' ? (float) $data['budget_max'] : null,
                'document_path'     => $document,
                'photo_path'        => $photo,
                'status'            => 'open',
                'source'            => 'web',
            ]);

            // Auto-match sellers carrying this category/product and notify them.
            (new RfqMatchService())->matchAndNotify($rfqId, $this->uid(), $data);

            return redirect()->to('account/rfqs/' . $rfqId);
        }

        return $this->render('account/rfq_form', [
            'categories' => $categories,
            'activeNav'  => 'rfqs',
        ], 'Post RFQ');
    }

    public function show($id)
    {
        $rfq = (new RfqModel())->find($id);
        if ($rfq === null) {
            $this->flash('error', 'RFQ not found.');
            return redirect()->to('account/rfqs');
        }

        $isBuyer = $this->uRole() !== 'seller';
        $isOwner = (int) $rfq['buyer_id'] === $this->uid();

        // Buyers may only view their own RFQs. Sellers may view matching RFQs.
        if ($isBuyer) {
            if (!$isOwner) {
                $this->flash('error', 'You cannot view this RFQ.');
                return redirect()->to('account/rfqs');
            }
        } else {
            $seller = (new SellerModel())->findByUser($this->uid());
            if ($seller === null) {
                $this->flash('error', 'Seller profile not found.');
                return redirect()->to('account/rfqs');
            }
            $matched = (new RfqSellerModel())->where('rfq_id', $id)->where('seller_id', $seller['id'])->first();
            if ($matched === null) {
                $this->flash('error', 'This RFQ is not shared with you.');
                return redirect()->to('account/rfqs');
            }
            // Mark as viewed.
            if ($matched['viewed_at'] === null) {
                (new RfqSellerModel())->update($matched['id'], ['viewed_at' => date('Y-m-d H:i:s')]);
            }
        }

        $quotations = db_connect()->table('quotations q')
            ->select('q.*, s.business_name')
            ->join('sellers s', 's.id = q.seller_id', 'left')
            ->where('q.rfq_id', $id)
            ->orderBy('q.created_at', 'DESC')
            ->get()->getResultArray();

        $matchedSellers = (new RfqSellerModel())->sellersForRfq($id);

        return $this->render('account/rfq_view', [
            'rfq'            => $rfq,
            'isBuyer'        => $isBuyer,
            'isOwner'        => $isOwner,
            'quotations'     => $quotations,
            'matchedSellers' => $isBuyer ? $matchedSellers : [],
            'activeNav'      => 'rfqs',
        ], 'RFQ: ' . $rfq['product_name']);
    }

    public function cancel($id)
    {
        if ($this->uRole() === 'seller') {
            $this->flash('error', 'Only buyers can cancel RFQs.');
            return redirect()->to('account/rfqs');
        }

        $rfq = (new RfqModel())->findForBuyer($this->uid(), (int) $id);
        if ($rfq === null) {
            $this->flash('error', 'RFQ not found.');
        } elseif ($rfq['status'] !== 'open') {
            $this->flash('error', 'Only open RFQs can be cancelled.');
        } else {
            (new RfqModel())->update($rfq['id'], ['status' => 'cancelled']);
            $this->flash('success', 'RFQ cancelled.');
        }

        return redirect()->to('account/rfqs');
    }

    /* ---------------- Seller RFQ inbox ---------------- */

    private function sellerRfqs()
    {
        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found.');
            return redirect()->to('account');
        }

        $rows = (new RfqSellerModel())->rfqsForSeller((int) $seller['id']);

        return $this->render('account/rfqs', [
            'rfqs'      => $rows,
            'isBuyer'   => false,
            'activeNav' => 'rfqs',
        ], 'Buyer RFQs');
    }

    private function storeDoc($field, string $folder): ?string
    {
        $file = $this->request->getFile($field);
        if ($file === null || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/' . $folder, $newName);

        return $folder . '/' . $newName;
    }
}