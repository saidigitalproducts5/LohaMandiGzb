<?php

namespace App\Controllers\Admin;

class Oversight extends AdminBase
{
    public function index()
    {
        $tab = trim((string) $this->request->getGet('tab'));
        $status = trim((string) $this->request->getGet('status'));
        $q = trim((string) $this->request->getGet('q'));

        $db = \Config\Database::connect();
        $data = ['tab' => $tab, 'status' => $status, 'q' => $q];

        if ($tab === 'orders') {
            $data['orders'] = $this->orders($q, $status);
        } elseif ($tab === 'leads') {
            $data['leads'] = $this->leads($q, $status);
        } elseif ($tab === 'reviews') {
            $data['reviews'] = (new \App\Models\ReviewModel())->allForAdmin($status !== 'all' ? $status : '');
        } else {
            $tab = 'rfqs';
            $data['rfqs'] = $this->rfqs($q, $status);
        }
        $data['tab'] = $tab;

        return $this->render('admin/oversight', $data, 'RFQs · Leads · Orders · Reviews');
    }

    /**
     * Moderate a review (approve/reject).
     */
    public function moderateReview($id)
    {
        $review = (new \App\Models\ReviewModel())->find((int) $id);
        if ($review === null) {
            session()->setFlashdata('flash_error', 'Review not found.');
            return redirect()->to('admin/oversight?tab=reviews');
        }

        $action = trim((string) $this->request->getPost('action'));
        if (in_array($action, ['approved', 'rejected'], true)) {
            (new \App\Models\ReviewModel())->update((int) $review['id'], ['status' => $action]);
            (new \App\Models\ReviewModel())->recomputeSellerRating((int) $review['seller_id']);

            // Notify the buyer of moderation result.
            $buyerUserId = (int) $review['buyer_id'];
            (new \App\Models\NotificationModel())->push(
                $buyerUserId,
                $action === 'approved' ? 'Your review is now live' : 'Your review was not approved',
                'Status: ' . $action,
                'review',
                (int) $review['id']
            );
        }

        session()->setFlashdata('flash_success', 'Review ' . $action . '.');
        return redirect()->to('admin/oversight?tab=reviews');
    }

    private function rfqs(string $q, string $status): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('rfqs r')
            ->select('r.*, u.name AS buyer_name, u.mobile AS buyer_mobile, c.name AS category_name,
                      (SELECT COUNT(*) FROM rfq_sellers rs WHERE rs.rfq_id = r.id) AS matched_sellers,
                      (SELECT COUNT(*) FROM quotations qt WHERE qt.rfq_id = r.id) AS quotes')
            ->join('users u', 'u.id = r.buyer_id', 'left')
            ->join('categories c', 'c.id = r.category_id', 'left');
        if ($status !== '' && $status !== 'all') {
            $builder->where('r.status', $status);
        }
        if ($q !== '') {
            $builder->groupStart()->like('r.product_name', $q)->orLike('u.name', $q)->orLike('r.city', $q)->groupEnd();
        }

        return $builder->orderBy('r.id', 'DESC')->limit(100)->get()->getResultArray();
    }

    private function leads(string $q, string $status): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('leads l')
            ->select('l.*, s.business_name, u.name AS buyer_name, u.mobile AS buyer_mobile, p.name AS product_name')
            ->join('sellers s', 's.id = l.seller_id', 'left')
            ->join('users u', 'u.id = l.buyer_id', 'left')
            ->join('products p', 'p.id = l.product_id', 'left');
        if ($status !== '' && $status !== 'all') {
            $builder->where('l.status', $status);
        }
        if ($q !== '') {
            $builder->groupStart()->like('s.business_name', $q)->orLike('l.name', $q)->orLike('l.mobile', $q)->groupEnd();
        }

        return $builder->orderBy('l.id', 'DESC')->limit(100)->get()->getResultArray();
    }

    private function orders(string $q, string $status): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('orders o')
            ->select('o.*, u.name AS buyer_name, s.business_name')
            ->join('users u', 'u.id = o.buyer_id', 'left')
            ->join('sellers s', 's.id = o.seller_id', 'left');
        if ($status !== '' && $status !== 'all') {
            $builder->where('o.order_status', $status);
        }
        if ($q !== '') {
            $builder->groupStart()->like('o.order_number', $q)->orLike('s.business_name', $q)->groupEnd();
        }

        return $builder->orderBy('o.id', 'DESC')->limit(100)->get()->getResultArray();
    }
}