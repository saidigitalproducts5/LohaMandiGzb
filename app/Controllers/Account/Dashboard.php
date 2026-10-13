<?php

namespace App\Controllers\Account;

use App\Models\LeadModel;
use App\Models\ProductModel;
use App\Models\QuotationModel;
use App\Models\RfqModel;
use App\Models\RfqSellerModel;
use App\Models\SellerModel;
use App\Models\TransporterModel;
use App\Models\TransportBookingModel;

class Dashboard extends AccountBase
{
    public function index()
    {
        $db = \Config\Database::connect();
        $uid = $this->uid();
        $role = $this->uRole();

        if ($role === 'seller') {
            $seller = (new SellerModel())->findByUser($uid);
            $sellerId = $seller ? (int) $seller['id'] : 0;

            $productCount   = $sellerId ? (int) $db->table('products')->where('seller_id', $sellerId)->countAllResults() : 0;
            $pendingCount   = $sellerId ? (int) $db->table('products')->where('seller_id', $sellerId)->where('status', 'pending')->countAllResults() : 0;
            $activeCount    = $sellerId ? (int) $db->table('products')->where('seller_id', $sellerId)->where('status', 'approved')->where('is_active', 1)->countAllResults() : 0;
            $rfqCount       = $sellerId ? (int) $db->table('rfq_sellers')->where('seller_id', $sellerId)->countAllResults() : 0;
            $quoteSentCount = $sellerId ? (int) $db->table('quotations')->where('seller_id', $sellerId)->countAllResults() : 0;

            $recentRfqs = $sellerId ? (new RfqSellerModel())->rfqsForSeller($sellerId) : [];
            $recentRfqs = array_slice($recentRfqs, 0, 8);
            $recentQuotes = $sellerId ? (new QuotationModel())->sellerQuotations($sellerId) : [];
            $recentQuotes = array_slice($recentQuotes, 0, 8);
            $recentLeads = $sellerId ? (new LeadModel())->sellerLeads($sellerId) : [];
            $recentLeads = array_slice($recentLeads, 0, 8);

            return $this->render('account/dashboard_seller', [
                'seller'         => $seller,
                'stats'          => [
                    'products'   => $productCount,
                    'pending'    => $pendingCount,
                    'active'     => $activeCount,
                    'rfqs'       => $rfqCount,
                    'quotes'     => $quoteSentCount,
                    'leads'      => (int) $db->table('leads')->where('seller_id', $sellerId)->where('status', 'new')->countAllResults(),
                ],
                'recentRfqs'     => $recentRfqs,
                'recentQuotes'   => $recentQuotes,
                'recentLeads'    => $recentLeads,
                'activeNav'      => 'dashboard',
            ], 'Dashboard');
        }

        if ($role === 'transporter') {
            $transporter = (new TransporterModel())->findByUser($uid);
            $tid = $transporter ? (int) $transporter['id'] : 0;

            $bookings = $tid ? (new TransportBookingModel())->forTransporter($tid) : [];
            $inTransit = count(array_filter($bookings, fn ($b) => $b['status'] === 'in_transit'));
            $delivered = count(array_filter($bookings, fn ($b) => $b['status'] === 'delivered'));
            $revenue = array_sum(array_map(fn ($b) => (float) $b['freight_charge'], array_filter($bookings, fn ($b) => $b['status'] === 'delivered')));
            $vehicleTotal = $tid ? (new \App\Models\VehicleModel())->countForTransporter($tid) : 0;

            return $this->render('account/dashboard_transporter', [
                'transporter' => $transporter,
                'stats'       => [
                    'bookings'   => count($bookings),
                    'in_transit' => $inTransit,
                    'delivered'  => $delivered,
                    'revenue'    => $revenue,
                    'vehicles'   => $vehicleTotal,
                ],
                'recentBookings' => array_slice($bookings, 0, 6),
                'activeNav'      => 'dashboard',
            ], 'Dashboard');
        }

        // Buyer / transporter
        $rfqCount       = (int) $db->table('rfqs')->where('buyer_id', $uid)->countAllResults();
        $openCount      = (int) $db->table('rfqs')->where('buyer_id', $uid)->where('status', 'open')->countAllResults();
        $acceptedCount  = (int) $db->table('quotations')->where('buyer_id', $uid)->where('status', 'accepted')->countAllResults();

        $recentRfqs = (new RfqModel())->buyerRfqs($uid);
        $recentRfqs = array_slice($recentRfqs, 0, 8);
        $recentQuotes = $db->table('quotations q')
            ->select('q.*, s.business_name')
            ->join('sellers s', 's.id = q.seller_id', 'left')
            ->where('q.buyer_id', $uid)
            ->orderBy('q.created_at', 'DESC')
            ->limit(8)
            ->get()->getResultArray();

        return $this->render('account/dashboard_buyer', [
            'stats'          => [
                'rfqs'      => $rfqCount,
                'open'      => $openCount,
                'quotes'    => (int) $db->table('quotations')->where('buyer_id', $uid)->countAllResults(),
                'accepted'  => $acceptedCount,
            ],
            'recentRfqs'     => $recentRfqs,
            'recentQuotes'   => $recentQuotes,
            'activeNav'      => 'dashboard',
        ], 'Dashboard');
    }
}
