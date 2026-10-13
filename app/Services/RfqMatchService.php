<?php

namespace App\Services;

use App\Models\LeadModel;
use App\Models\NotificationModel;
use App\Models\RfqSellerModel;
use App\Models\SellerModel;

/**
 * RfqMatchService — matches a newly posted RFQ to active sellers whose
 * approved products fall under the RFQ category (or name match) and notifies
 * them. Shared by the web and API RFQ flows.
 */
class RfqMatchService
{
    /**
     * @param array $data RFQ fields: product_name, category_id, quantity, unit, delivery_location
     * @return array<int, array<string, int|float>>
     */
    public function matchAndNotify(int $rfqId, int $buyerId, array $data): array
    {
        $db = \Config\Database::connect();
        $rfqSeller = new RfqSellerModel();
        $matched = [];

        $builder = $db->table('sellers s')
            ->select('s.id AS seller_id, COUNT(DISTINCT p.id) AS product_count')
            ->join('products p', 'p.seller_id = s.id AND p.status = "approved" AND p.is_active = 1', 'inner')
            ->where('s.status', 'active')
            ->groupBy('s.id');

        if (!empty($data['category_id'])) {
            $builder->where('p.category_id', (int) $data['category_id']);
        } elseif (!empty($data['product_name'])) {
            $builder->groupStart()->like('p.name', $data['product_name'])->groupEnd();
        }

        $rows = $builder->limit(100)->get()->getResultArray();

        foreach ($rows as $row) {
            $score = min(100, (float) $row['product_count'] * 10);
            $rfqSeller->insert([
                'rfq_id'     => $rfqId,
                'seller_id'  => (int) $row['seller_id'],
                'matched_by' => 'auto',
                'score'      => $score,
                'status'     => 'sent',
            ]);
            $matched[] = ['seller_id' => (int) $row['seller_id'], 'score' => $score];
        }

        $notifications = new NotificationService(new NotificationModel());
        foreach ($matched as $m) {
            $seller = (new SellerModel())->find($m['seller_id']);
            $notifications->send(
                (int) $seller['user_id'],
                'New RFQ received',
                'A buyer posted an RFQ for ' . ($data['product_name'] ?? 'your product') . '. Submit your quotation.',
                'in_app',
                'rfq',
                $rfqId
            );
            (new LeadModel())->insert([
                'seller_id' => (int) $seller['id'],
                'buyer_id'  => $buyerId,
                'rfq_id'    => $rfqId,
                'lead_type' => 'rfq',
                'status'    => 'new',
                'name'      => null,
                'mobile'    => null,
                'message'   => 'RFQ: ' . ($data['product_name'] ?? '') . ' (qty ' . ($data['quantity'] ?? '') . ' ' . ($data['unit'] ?? 'ton') . ')',
            ]);
        }

        return $matched;
    }
}
