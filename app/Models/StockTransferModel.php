<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Stock transfers between a seller's warehouses.
 */
class StockTransferModel extends Model
{
    const STATUS_PENDING  = 'pending';
    const STATUS_INTRANSIT = 'in_transit';
    const STATUS_ARRIVED  = 'arrived';
    const STATUS_CANCELLED = 'cancelled';

    protected $table         = 'stock_transfers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'seller_id', 'product_id', 'from_warehouse_id', 'to_warehouse_id',
        'quantity', 'status', 'note',
    ];

    public function forSeller(int $sellerId, ?string $status = null): array
    {
        $b = $this->builder()
            ->select('stock_transfers.*, p.name AS product_name, p.slug, p.unit,
                    fw.name AS fw_name, fw.city AS fw_city, tw.name AS tw_name, tw.city AS tw_city')
            ->join('products p', 'p.id = stock_transfers.product_id')
            ->join('warehouses fw', 'fw.id = stock_transfers.from_warehouse_id', 'left')
            ->join('warehouses tw', 'tw.id = stock_transfers.to_warehouse_id')
            ->where('stock_transfers.seller_id', $sellerId);

        if ($status !== null && $status !== '') {
            $b->where('stock_transfers.status', $status);
        }

        return $b->orderBy('stock_transfers.id', 'DESC')->get()->getResultArray();
    }

    public function findForSeller(int $sellerId, int $id): ?array
    {
        return $this->builder()
            ->select('stock_transfers.*, p.name AS product_name, p.slug, p.unit,
                    fw.name AS fw_name, fw.city AS fw_city, tw.name AS tw_name, tw.city AS tw_city')
            ->join('products p', 'p.id = stock_transfers.product_id')
            ->join('warehouses fw', 'fw.id = stock_transfers.from_warehouse_id', 'left')
            ->join('warehouses tw', 'tw.id = stock_transfers.to_warehouse_id')
            ->where('stock_transfers.seller_id', $sellerId)
            ->where('stock_transfers.id', $id)
            ->get()->getRowArray();
    }

    public function pendingCount(int $sellerId): int
    {
        return (int) $this->where('seller_id', $sellerId)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_INTRANSIT])
            ->countAllResults();
    }

    public function byStatusCount(int $sellerId): array
    {
        $rows = $this->builder()
            ->select('status, COUNT(*) AS c', true)
            ->where('seller_id', $sellerId)
            ->groupBy('status')
            ->get()->getResultArray();

        $out = [self::STATUS_PENDING => 0, self::STATUS_INTRANSIT => 0, self::STATUS_ARRIVED => 0, self::STATUS_CANCELLED => 0];
        foreach ($rows as $r) {
            if (isset($out[$r['status']])) {
                $out[$r['status']] = (int) $r['c'];
            }
        }

        return $out;
    }
}