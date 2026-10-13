<?php

namespace App\Controllers\Admin;

class Reports extends AdminBase
{
    public function index()
    {
        $db = \Config\Database::connect();
        $tab = trim((string) $this->request->getGet('tab'));

        switch ($tab) {
            case 'monthly':
                $data = ['monthly' => $this->monthly()];
                break;
            case 'leads':
                $data = ['leadReport' => $this->leadReport()];
                break;
            case 'sellers':
                $data = ['sellerReport' => $this->sellerReport()];
                break;
            case 'categories':
                $data = ['categoryReport' => $this->categoryReport()];
                break;
            case 'gst':
                $data = ['gstReport' => $this->gstReport()];
                break;
            default:
                $tab = 'revenue';
                $data = ['revenue' => $this->revenue()];
        }
        $data['tab'] = $tab;

        return $this->render('admin/reports', $data, 'Reports');
    }

    private function revenue(): array
    {
        $db = \Config\Database::connect();

        $byMethod = $db->table('payments')
            ->select('method, COUNT(*) AS cnt, COALESCE(SUM(amount),0) AS total')
            ->where('status', 'success')
            ->groupBy('method')->get()->getResultArray();

        $total = 0.0;
        foreach ($byMethod as $m) {
            $total += (float) $m['total'];
        }

        return ['total' => $total, 'by_method' => $byMethod, 'orders' => (int) $db->table('orders')->countAllResults()];
    }

    private function monthly(): array
    {
        $db = \Config\Database::connect();

        $rows = $db->table('users')
            ->select("DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS users, 
                      SUM(role_id = 6) AS sellers, SUM(role_id = 5) AS buyers")
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('month', 'DESC')->limit(12)->get()->getResultArray();

        $rfqs = $db->table('rfqs')
            ->select("DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS rfqs")
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')")->get()->getResultArray();

        $rfqMap = array_column($rfqs, 'rfqs', 'month');
        foreach ($rows as &$r) {
            $r['rfqs'] = (int) ($rfqMap[$r['month']] ?? 0);
        }

        return $rows;
    }

    private function leadReport(): array
    {
        $db = \Config\Database::connect();
        $total = (int) $db->table('leads')->countAllResults();

        $byStatus = $db->table('leads')->select('status, COUNT(*) AS cnt')->groupBy('status')->get()->getResultArray();
        $byType   = $db->table('leads')->select('lead_type, COUNT(*) AS cnt')->groupBy('lead_type')->get()->getResultArray();

        return ['total' => $total, 'by_status' => $byStatus, 'by_type' => $byType];
    }

    private function sellerReport(): array
    {
        $db = \Config\Database::connect();

        $byCity = $db->table('sellers')
            ->select('city, COUNT(*) AS cnt')->where('city IS NOT NULL')->groupBy('city')->orderBy('cnt', 'DESC')->limit(10)->get()->getResultArray();

        $byState = $db->table('sellers')
            ->select('state, COUNT(*) AS cnt')->where('state IS NOT NULL')->groupBy('state')->orderBy('cnt', 'DESC')->get()->getResultArray();

        return ['total' => (int) $db->table('sellers')->countAllResults(), 'by_city' => $byCity, 'by_state' => $byState];
    }

    private function categoryReport(): array
    {
        $db = \Config\Database::connect();

        return $db->table('products p')
            ->select('c.name AS category, COUNT(p.id) AS products')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->groupBy('p.category_id')->orderBy('products', 'DESC')->limit(15)->get()->getResultArray();
    }

    private function gstReport(): array
    {
        $db = \Config\Database::connect();

        $verified = (int) $db->table('sellers')->where('gst_verified', 1)->countAllResults();
        $unverified = (int) $db->table('sellers')->where('gst_verified', 0)->countAllResults();
        $withGstin = (int) $db->table('sellers')->where('gstin IS NOT NULL')->where('gstin !=', '')->countAllResults();

        $gstRates = $db->table('products')
            ->select('gst_rate, COUNT(*) AS products')
            ->groupBy('gst_rate')->orderBy('products', 'DESC')->get()->getResultArray();

        return ['verified' => $verified, 'unverified' => $unverified, 'with_gstin' => $withGstin, 'gst_rates' => $gstRates];
    }
}