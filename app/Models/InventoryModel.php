<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Inventory — per-seller stock records, joined with products and warehouses.
 */
class InventoryModel extends Model
{
    protected $table         = 'inventory';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'seller_id', 'product_id', 'warehouse_id', 'warehouse', 'stock_qty',
        'low_stock_threshold', 'batch_no', 'barcode', 'qr_code',
    ];

    /**
     * Inventory rows joined with product + warehouse info.
     */
    public function forSeller(int $sellerId, ?int $warehouseId = null, ?string $q = null): array
    {
        $b = $this->builder()
            ->select('inventory.*, p.name AS product_name, p.slug, p.unit, p.available_stock AS product_available, w.name AS warehouse_name, w.city AS warehouse_city')
            ->join('products p', 'p.id = inventory.product_id')
            ->join('warehouses w', 'w.id = inventory.warehouse_id', 'left')
            ->where('inventory.seller_id', $sellerId);

        if ($warehouseId !== null && $warehouseId > 0) {
            $b->where('inventory.warehouse_id', $warehouseId);
        }
        if ($q !== null && $q !== '') {
            $b->groupStart()
                ->like('p.name', $q)
                ->orLike('inventory.batch_no', $q)
                ->orLike('inventory.barcode', $q)
                ->groupEnd();
        }

        return $b->orderBy('inventory.id', 'DESC')->get()->getResultArray();
    }

    public function findForSeller(int $sellerId, int $id): ?array
    {
        return $this->builder()
            ->select('inventory.*, p.name AS product_name, p.slug, p.unit, w.name AS warehouse_name, w.city AS warehouse_city')
            ->join('products p', 'p.id = inventory.product_id')
            ->join('warehouses w', 'w.id = inventory.warehouse_id', 'left')
            ->where('inventory.seller_id', $sellerId)
            ->where('inventory.id', $id)
            ->get()->getRowArray();
    }

    /**
     * Rows for a specific product (all warehouses).
     */
    public function rowsForProduct(int $sellerId, int $productId): array
    {
        return $this->where('seller_id', $sellerId)->where('product_id', $productId)->findAll();
    }

    /**
     * Total qty of a product across all warehouses.
     */
    public function qtyForProduct(int $sellerId, int $productId): float
    {
        $row = $this->builder()
            ->select('COALESCE(SUM(stock_qty),0) AS total', true)
            ->where('seller_id', $sellerId)
            ->where('product_id', $productId)
            ->get()->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Low-stock rows (stock below threshold and threshold > 0).
     */
    public function lowStock(int $sellerId): array
    {
        return $this->builder()
            ->select('inventory.*, p.name AS product_name, p.slug, w.name AS warehouse_name')
            ->join('products p', 'p.id = inventory.product_id')
            ->join('warehouses w', 'w.id = inventory.warehouse_id', 'left')
            ->where('inventory.seller_id', $sellerId)
            ->where('inventory.low_stock_threshold >', 0)
            ->where('inventory.stock_qty <= inventory.low_stock_threshold', null, false)
            ->orderBy('inventory.id', 'DESC')
            ->get()->getResultArray();
    }

    public function lowStockCount(int $sellerId): int
    {
        $row = $this->builder()
            ->select('COUNT(*) AS c', true)
            ->where('seller_id', $sellerId)
            ->where('low_stock_threshold >', 0)
            ->where('stock_qty <= low_stock_threshold', null, false)
            ->get()->getRowArray();

        return (int) ($row['c'] ?? 0);
    }

    public function totalQty(int $sellerId): float
    {
        $row = $this->builder()
            ->select('COALESCE(SUM(stock_qty),0) AS t', true)
            ->where('seller_id', $sellerId)
            ->get()->getRowArray();

        return (float) ($row['t'] ?? 0);
    }

    public function rowCount(int $sellerId): int
    {
        return (int) $this->where('seller_id', $sellerId)->countAllResults();
    }

    public function uniqueProducts(int $sellerId): int
    {
        $row = $this->builder()
            ->select('COUNT(DISTINCT product_id) AS c', true)
            ->where('seller_id', $sellerId)
            ->get()->getRowArray();

        return (int) ($row['c'] ?? 0);
    }

    /**
     * Add stock (raises stock_qty). Syncs product.available_stock.
     */
    public function addStock(int $id, float $qty): bool
    {
        $this->builder()->where('id', $id)->set('stock_qty', 'stock_qty + ' . (float) $qty, false)->update();
        $this->syncProductStock($id);

        return true;
    }

    /**
     * Reduce stock after write-off / sell (never below zero check is up to the caller).
     */
    public function reduceStock(int $id, float $qty): bool
    {
        $this->builder()->where('id', $id)->set('stock_qty', 'GREATEST(stock_qty - ' . (float) $qty . ', 0)', false)->update();
        $this->syncProductStock($id);

        return true;
    }

    /**
     * Reflect the summed inventory across warehouses onto products.available_stock.
     */
    public function syncProductStock(int $inventoryId): void
    {
        $inv = $this->find($inventoryId);
        if ($inv === null) {
            return;
        }
        $this->syncForProduct((int) $inv['seller_id'], (int) $inv['product_id']);
    }

    /**
     * Sum inventory across warehouses for a product and write it to products.available_stock.
     */
    public function syncForProduct(int $sellerId, int $productId): void
    {
        $total = $this->qtyForProduct($sellerId, $productId);

        db_connect()->table('products')->where('id', $productId)->update(['available_stock' => $total]);
    }
}