<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;

class Dashboard extends AdminBase
{
    public function index()
    {
        $db = \Config\Database::connect();

        $stats = [
            'users'       => (int) $db->table('users')->countAllResults(),
            'sellers'     => (int) $db->table('sellers')->countAllResults(),
            'buyers'      => (int) $db->table('users')->where('role_id', 5)->countAllResults(),
            'products'    => (int) $db->table('products')->countAllResults(),
            'pending_products' => (int) $db->table('products')->where('status', 'pending')->countAllResults(),
            'pending_sellers'  => (int) $db->table('sellers')->where('status', 'pending')->countAllResults(),
            'orders'      => (int) $db->table('orders')->countAllResults(),
            'revenue'     => (float) ($db->table('payments')->selectSum('amount')->where('status', 'success')->get()->getRow()->amount ?? 0),
            'premium'     => (int) $db->table('seller_membership')->where('status', 'active')->countAllResults(),
            'leads'       => (int) $db->table('leads')->countAllResults(),
            'rfqs'        => (int) $db->table('rfqs')->countAllResults(),
            'ads'         => (int) $db->table('advertisements')->where('status', 1)->countAllResults(),
            'complaints'  => 0,
        ];

        $recentUsers = $db->table('users')
            ->select('users.*, roles.name AS role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->orderBy('users.id', 'DESC')->limit(8)->get()->getResultArray();

        $recentProducts = $db->table('products')
            ->select('products.*, sellers.business_name')
            ->join('sellers', 'sellers.id = products.seller_id', 'left')
            ->orderBy('products.id', 'DESC')->limit(8)->get()->getResultArray();

        // WhatsApp message logs with pagination
        $waStats = ['total' => 0, 'success' => 0, 'failed' => 0];
        $recentWaLogs = [];
        $waPage = max(1, (int) ($this->request->getGet('wa_page') ?? 1));
        $waPerPage = 10;
        $waTotal = 0;
        $waPages = 1;
        try {
            $waStats = [
                'total'   => (int) $db->table('whatsapp_message_logs')->countAllResults(),
                'success' => (int) $db->table('whatsapp_message_logs')->where('send_status', 'success')->countAllResults(),
                'failed'  => (int) $db->table('whatsapp_message_logs')->where('send_status', 'failed')->countAllResults(),
            ];
            $waTotal = $waStats['total'];
            $waPages = max(1, (int) ceil($waTotal / $waPerPage));
            $waPage = min($waPage, $waPages);
            $waOffset = ($waPage - 1) * $waPerPage;
            $recentWaLogs = $db->table('whatsapp_message_logs')
                ->orderBy('id', 'DESC')
                ->limit($waPerPage, $waOffset)
                ->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard WhatsApp stats query failed: ' . $e->getMessage());
        }

        return $this->render('admin/dashboard', [
            'stats'          => $stats,
            'recentUsers'    => $recentUsers,
            'recentProducts' => $recentProducts,
            'waStats'        => $waStats,
            'recentWaLogs'   => $recentWaLogs,
            'waPage'         => $waPage,
            'waPages'        => $waPages,
            'waTotal'        => $waTotal,
            'activeNav'      => 'dashboard',
        ], 'Dashboard');
    }
}
