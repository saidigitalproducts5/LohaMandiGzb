<?php

namespace App\Controllers\Account;

use App\Models\LeadModel;
use App\Models\SellerModel;

class Leads extends AccountBase
{
    public function index()
    {
        if ($this->uRole() !== 'seller') {
            $this->flash('error', 'Only sellers can access leads.');
            return redirect()->to('account');
        }

        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found.');
            return redirect()->to('account');
        }

        $status = trim((string) $this->request->getGet('status'));
        $leads = (new LeadModel())->sellerLeads((int) $seller['id'], $status !== '' ? $status : null);

        $db = \Config\Database::connect();
        $counts = [
            'new'       => (int) $db->table('leads')->where('seller_id', $seller['id'])->where('status', 'new')->countAllResults(),
            'contacted' => (int) $db->table('leads')->where('seller_id', $seller['id'])->where('status', 'contacted')->countAllResults(),
            'won'       => (int) $db->table('leads')->where('seller_id', $seller['id'])->where('status', 'won')->countAllResults(),
            'lost'      => (int) $db->table('leads')->where('seller_id', $seller['id'])->where('status', 'lost')->countAllResults(),
        ];

        return $this->render('account/leads', [
            'leads'     => $leads,
            'status'    => $status,
            'counts'    => $counts,
            'activeNav' => 'leads',
        ], 'Leads');
    }

    public function updateStatus($id)
    {
        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found.');
            return redirect()->to('account');
        }

        $lead = (new LeadModel())->findForSeller((int) $seller['id'], (int) $id);
        if ($lead === null) {
            $this->flash('error', 'Lead not found.');
        } else {
            $new = trim((string) $this->request->getPost('status'));
            if (in_array($new, ['new', 'contacted', 'won', 'lost'], true)) {
                (new LeadModel())->update($lead['id'], ['status' => $new]);
                $this->flash('success', 'Lead status updated.');
            } else {
                $this->flash('error', 'Invalid status.');
            }
        }

        return redirect()->to('account/leads');
    }
}