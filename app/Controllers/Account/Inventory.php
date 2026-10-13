<?php

namespace App\Controllers\Account;

use App\Models\InventoryModel;
use App\Models\ProductModel;
use App\Models\SellerModel;
use App\Models\StockTransferModel;
use App\Models\WarehouseModel;

/**
 * Inventory — seller stock management.
 * Stock records, warehouses, low-stock alerts, stock transfers, barcodes/QR.
 */
class Inventory extends AccountBase
{
    private function requireSeller(): ?array
    {
        if ($this->uRole() !== 'seller') {
            $this->flash('error', 'Inventory is available for sellers.');
            return null;
        }

        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found.');
            return null;
        }

        return $seller;
    }

    private function notFound(string $message)
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound($message);
    }

    // ------------------------------------------------------------------
    // Dashboard
    // ------------------------------------------------------------------

    public function index()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $invModel = new InventoryModel();
        $low      = $invModel->lowStock($sid);
        $recent   = array_slice((new StockTransferModel())->forSeller($sid), 0, 8);

        return $this->render('account/inventory_dashboard', [
            'productCount'    => $invModel->uniqueProducts($sid),
            'totalQty'        => $invModel->totalQty($sid),
            'rowCount'        => $invModel->rowCount($sid),
            'warehouseCount'  => count((new WarehouseModel())->forSeller($sid)),
            'lowStock'        => $low,
            'lowStockCount'   => $invModel->lowStockCount($sid),
            'pendingTfr'      => (new StockTransferModel())->pendingCount($sid),
            'tfrCounts'       => (new StockTransferModel())->byStatusCount($sid),
            'recentTransfers' => $recent,
            'activeNav'       => 'inventory',
        ], 'Inventory Dashboard');
    }

    // ------------------------------------------------------------------
    // Stock ledger
    // ------------------------------------------------------------------

    public function stock()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid    = (int) $seller['id'];
        $warehouseId = (int) $this->request->getGet('warehouse_id');
        $q      = trim((string) $this->request->getGet('q'));

        $rows    = (new InventoryModel())->forSeller($sid, $warehouseId > 0 ? $warehouseId : null, $q !== '' ? $q : null);

        return $this->render('account/inventory_stock', [
            'rows'       => $this->aggregate($rows),
            'rawRows'    => $rows,
            'warehouses' => (new WarehouseModel())->forSeller($sid),
            'warehouseId'=> $warehouseId,
            'q'          => $q,
            'products'   => (new ProductModel())->sellerProducts($sid, 500),
            'activeNav'  => 'inventory',
        ], 'Stock Levels');
    }

    private function aggregate(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $pid = (int) $r['product_id'];
            if (!isset($out[$pid])) {
                $out[$pid] = [
                    'product_id'   => $pid,
                    'product_name' => $r['product_name'],
                    'slug'         => $r['slug'],
                    'unit'         => $r['unit'] ?? 'ton',
                    'total'        => 0,
                    'warehouses'   => [],
                    'low'          => false,
                ];
            }
            $out[$pid]['total'] += (float) $r['stock_qty'];
            $out[$pid]['warehouses'][] = [
                'id'                   => (int) $r['id'],
                'warehouse_name'       => $r['warehouse_name'] ?? ($r['warehouse'] ?: 'Unassigned'),
                'city'                 => $r['warehouse_city'] ?? null,
                'stock_qty'            => (float) $r['stock_qty'],
                'low_stock_threshold'  => (float) $r['low_stock_threshold'],
                'batch_no'             => $r['batch_no'],
                'barcode'              => $r['barcode'],
                'qr_code'              => $r['qr_code'],
            ];
            if ((float) $r['stock_qty'] <= (float) $r['low_stock_threshold'] && (float) $r['low_stock_threshold'] > 0) {
                $out[$pid]['low'] = true;
            }
        }

        return array_values($out);
    }

    /**
     * GET/POST /account/inventory/stock/new — add stock for a product in a warehouse.
     */
    public function stockCreate()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];
        $invModel = new InventoryModel();

        if ($this->request->getMethod() === 'POST') {
            $productId    = (int) $this->request->getPost('product_id');
            $warehouseId  = (int) $this->request->getPost('warehouse_id');
            $qty          = (float) $this->request->getPost('stock_qty');
            $threshold    = (float) $this->request->getPost('low_stock_threshold');
            $batch        = trim((string) $this->request->getPost('batch_no')) ?: null;

            $product = (new ProductModel())->findForSeller($sid, $productId);
            if ($product === null) {
                $this->flash('error', 'Invalid product.');
                return redirect()->to('account/inventory/stock/new');
            }

            $warehouse = (new WarehouseModel())->findForSeller($sid, $warehouseId);
            if ($warehouse === null) {
                $this->flash('error', 'Invalid warehouse.');
                return redirect()->to('account/inventory/stock/new');
            }

            // Avoid duplicate row for same product+warehouse; update instead.
            $existing = $invModel->where('seller_id', $sid)->where('product_id', $productId)->where('warehouse_id', $warehouseId)->first();
            if ($existing !== null) {
                $invModel->update($existing['id'], [
                    'stock_qty'          => $qty,
                    'low_stock_threshold'=> $threshold,
                    'batch_no'           => $batch,
                ]);
                $invModel->syncProductStock((int) $existing['id']);
                $this->flash('success', 'Stock record updated for ' . $product['name'] . '.');
            } else {
                $invModel->insert([
                    'seller_id'          => $sid,
                    'product_id'         => $productId,
                    'warehouse_id'       => $warehouseId,
                    'warehouse'          => $warehouse['name'],
                    'stock_qty'          => $qty,
                    'low_stock_threshold'=> $threshold,
                    'batch_no'           => $batch,
                    'barcode'            => $this->genCode('BAR', $sid, $productId),
                    'qr_code'            => $this->genCode('QR', $sid, $productId),
                ]);
                $invModel->syncProductStock((int) $invModel->getInsertID());
                $this->flash('success', 'Stock record created.');
            }

            return redirect()->to('account/inventory/stock');
        }

        return $this->render('account/inventory_stock_form', [
            'product'    => null,
            'row'        => null,
            'warehouses' => (new WarehouseModel())->forSeller($sid),
            'products'   => (new ProductModel())->sellerProducts($sid, 500),
            'editing'    => false,
            'activeNav'  => 'inventory',
        ], 'Add Stock');
    }

    /**
     * GET/POST /account/inventory/stock/{id}/edit
     */
    public function stockEdit(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];
        $invModel = new InventoryModel();

        $row = $invModel->findForSeller($sid, $id);
        if ($row === null) {
            return $this->notFound('Stock record not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            $warehouseId = (int) $this->request->getPost('warehouse_id');
            $warehouse = (new WarehouseModel())->findForSeller($sid, $warehouseId);
            if ($warehouse === null) {
                $this->flash('error', 'Invalid warehouse.');
                return redirect()->to('account/inventory/stock/' . $id . '/edit');
            }

            $invModel->update($id, [
                'warehouse_id'       => $warehouseId,
                'warehouse'          => $warehouse['name'],
                'stock_qty'          => (float) $this->request->getPost('stock_qty'),
                'low_stock_threshold'=> (float) $this->request->getPost('low_stock_threshold'),
                'batch_no'           => trim((string) $this->request->getPost('batch_no')) ?: null,
            ]);
            $invModel->syncProductStock($id);
            $this->flash('success', 'Stock record updated.');
            return redirect()->to('account/inventory/stock');
        }

        $row = $invModel->findForSeller($sid, $id);

        return $this->render('account/inventory_stock_form', [
            'product'    => $row['product_name'] ?? null,
            'row'        => $row,
            'warehouses' => (new WarehouseModel())->forSeller($sid),
            'products'   => [],
            'isNav'      => false,
            'activeNav'  => 'inventory',
        ], 'Edit Stock');
    }

    /**
     * GET /account/inventory/stock/{id}/delete
     */
    public function stockDelete(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid      = (int) $seller['id'];
        $invModel = new InventoryModel();
        $row      = $invModel->findForSeller($sid, $id);
        if ($row !== null) {
            $invModel->where('id', $id)->delete();
            $invModel->syncForProduct($sid, (int) $row['product_id']);
            $this->flash('success', 'Stock record deleted.');
        }

        return redirect()->to('account/inventory/stock');
    }

    // ------------------------------------------------------------------
    // Warehouses
    // ------------------------------------------------------------------

    public function warehouses()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        return $this->render('account/inventory_warehouses', [
            'warehouses' => (new WarehouseModel())->forSeller($sid),
            'activeNav'  => 'inventory',
        ], 'Warehouses');
    }

    public function warehouseCreate()
    {
        file_put_contents(WRITEPATH . 'tmp_inv_debug.txt', print_r(['uri'=>$this->request->getUri()->getPath(), 'method'=>$this->request->getMethod(), 'post'=>$this->request->getPost()], true));
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];
        $model = new WarehouseModel();

        if ($this->request->getMethod() === 'POST') {
            $name = trim((string) $this->request->getPost('name'));
            if ($name === '') {
                $this->flash('error', 'Warehouse name is required.');
                return redirect()->to('account/inventory/warehouses/new');
            }

            $model->insert([
                'seller_id'     => $sid,
                'name'          => $name,
                'address'       => trim((string) $this->request->getPost('address')) ?: null,
                'city'          => trim((string) $this->request->getPost('city')) ?: null,
                'state'         => trim((string) $this->request->getPost('state')) ?: null,
                'pincode'       => trim((string) $this->request->getPost('pincode')) ?: null,
                'contact_phone' => trim((string) $this->request->getPost('contact_phone')) ?: null,
                'is_default'    => $model->forSeller($sid) === [] ? 1 : 0,
            ]);
            $this->flash('success', 'Warehouse created.');
            return redirect()->to('account/inventory/warehouses');
        }

        return $this->render('account/inventory_warehouse_form', [
            'warehouse' => null,
            'isEdit'    => false,
            'activeNav' => 'inventory',
        ], 'Add Warehouse');
    }

    public function warehouseEdit(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid   = (int) $seller['id'];
        $model = new WarehouseModel();
        $wh    = $model->findForSeller($sid, $id);
        if ($wh === null) {
            return $this->notFound('Warehouse not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            $name = trim((string) $this->request->getPost('name'));
            if ($name === '') {
                $this->flash('error', 'Warehouse name is required.');
                return redirect()->to('account/inventory/warehouses/' . $id . '/edit');
            }
            $model->update($id, [
                'name'          => $name,
                'address'       => trim((string) $this->request->getPost('address')) ?: null,
                'city'          => trim((string) $this->request->getPost('city')) ?: null,
                'state'         => trim((string) $this->request->getPost('state')) ?: null,
                'pincode'       => trim((string) $this->request->getPost('pincode')) ?: null,
                'contact_phone' => trim((string) $this->request->getPost('contact_phone')) ?: null,
            ]);
            $this->flash('success', 'Warehouse updated.');
            return redirect()->to('account/inventory/warehouses');
        }

        return $this->render('account/inventory_warehouse_form', [
            'warehouse' => $wh,
            'isEdit'    => true,
            'activeNav' => 'inventory',
        ], 'Edit Warehouse');
    }

    public function warehouseDelete(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];
        (new WarehouseModel())->where('seller_id', $sid)->where('id', $id)->delete();
        $this->flash('success', 'Warehouse deleted (stock records kept).');
        return redirect()->to('account/inventory/warehouses');
    }

    public function warehouseDefault(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid   = (int) $seller['id'];
        $model = new WarehouseModel();
        $wh    = $model->findForSeller($sid, $id);
        if ($wh !== null) {
            db_connect()->table('warehouses')->where('seller_id', $sid)->update(['is_default' => 0]);
            $model->update($id, ['is_default' => 1]);
            $this->flash('success', 'Default warehouse set.');
        }
        return redirect()->to('account/inventory/warehouses');
    }

    // ------------------------------------------------------------------
    // Stock transfers
    // ------------------------------------------------------------------

    public function transfers()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $status = trim((string) $this->request->getGet('status'));

        return $this->render('account/inventory_transfers', [
            'transfers' => (new StockTransferModel())->forSeller($sid, $status !== '' ? $status : null),
            'status'    => $status,
            'statuses'  => ['pending', 'in_transit', 'arrived', 'cancelled'],
            'activeNav' => 'inventory',
        ], 'Stock Transfers');
    }

    public function transferCreate()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        if ($this->request->getMethod() === 'POST') {
            $fromWh = (int) $this->request->getPost('from_warehouse_id');
            $toWh   = (int) $this->request->getPost('to_warehouse_id');
            $pid    = (int) $this->request->getPost('product_id');
            $qty    = (float) $this->request->getPost('quantity');

            if ($fromWh === $toWh) {
                $this->flash('error', 'Source and destination warehouses must differ.');
                return redirect()->to('account/inventory/transfers/new');
            }
            if ($qty <= 0) {
                $this->flash('error', 'Quantity must be greater than zero.');
                return redirect()->to('account/inventory/transfers/new');
            }

            $invModel = new InventoryModel();
            $source = $invModel->where('seller_id', $sid)->where('product_id', $pid)->where('warehouse_id', $fromWh)->first();
            if ($source === null || (float) $source['stock_qty'] < $qty) {
                $this->flash('error', 'Insufficient stock in source warehouse.');
                return redirect()->to('account/inventory/transfers/new');
            }

            $product      = (new ProductModel())->findForSeller($sid, $pid);
            $toWarehouse  = (new WarehouseModel())->findForSeller($sid, $toWh);
            if ($product === null || $toWarehouse === null) {
                $this->flash('error', 'Invalid product or destination warehouse.');
                return redirect()->to('account/inventory/transfers/new');
            }

            // Reserve: reduce source immediately, confirm on arrival.
            $dest = $invModel->where('seller_id', $sid)->where('product_id', $pid)->where('warehouse_id', $toWh)->first();
            if ($dest === null) {
                $invModel->insert([
                    'seller_id'          => $sid,
                    'product_id'         => $pid,
                    'warehouse_id'       => $toWh,
                    'warehouse'          => $toWarehouse['name'],
                    'stock_qty'          => 0,
                    'low_stock_threshold'=> $source['low_stock_threshold'],
                    'barcode'            => $this->genCode('BAR', $sid, $pid),
                    'qr_code'            => $this->genCode('QR', $sid, $pid),
                ]);
            }

            $dest = $invModel->where('seller_id', $sid)->where('product_id', $pid)->where('warehouse_id', $toWh)->first();
            if ($dest === null) {
                $this->flash('error', 'Destination warehouse missing.');
                return redirect()->to('account/inventory/transfers/new');
            }
            if ((int) $dest['id'] === (int) $source['id']) {
                $this->flash('error', 'Source and destination stock records conflict.');
                return redirect()->to('account/inventory/transfers/new');
            }

            $tfr = new StockTransferModel();
            $tfr->insert([
                'seller_id'         => $sid,
                'product_id'        => $pid,
                'from_warehouse_id' => $fromWh,
                'to_warehouse_id'   => $toWh,
                'quantity'          => $qty,
                'status'            => StockTransferModel::STATUS_PENDING,
                'note'              => trim((string) $this->request->getPost('note')) ?: null,
            ]);
            // Reduce source stock now (reserved).
            $invModel->reduceStock((int) $source['id'], $qty);

            $this->flash('success', 'Transfer created (source stock reserved).');
            return redirect()->to('account/inventory/transfers');
        }

        return $this->render('account/inventory_transfer_form', [
            'warehouses' => (new WarehouseModel())->forSeller($sid),
            'products'   => (new ProductModel())->sellerProducts($sid, 500),
            'activeNav'  => 'inventory',
        ], 'New Stock Transfer');
    }

    /**
     * UPDATE /account/inventory/transfers/{id}/status
     * arrived: post stock to destination; else cancel/none.
     */
    public function transferStatus(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid   = (int) $seller['id'];
        $tfrModel = new StockTransferModel();
        $tfr = $tfrModel->findForSeller($sid, $id);
        if ($tfr === null) {
            return $this->notFound('Transfer not found.');
        }

        $to = (string) $this->request->getPost('status');
        if ($to === StockTransferModel::STATUS_ARRIVED && $tfr['status'] !== StockTransferModel::STATUS_ARRIVED) {
            $invModel = new InventoryModel();
            $dest = $invModel->where('seller_id', $sid)->where('product_id', $tfr['product_id'])->where('warehouse_id', $tfr['to_warehouse_id'])->first();
            if ($dest !== null) {
                $invModel->addStock((int) $dest['id'], (float) $tfr['quantity']);
                $this->flash('success', 'Transfer marked arrived; stock credited to destination.');
            } else {
                $this->flash('error', 'Destination stock record missing.');
                return redirect()->to('account/inventory/transfers');
            }
        } elseif ($to === StockTransferModel::STATUS_CANCELLED && $tfr['status'] !== StockTransferModel::STATUS_ARRIVED) {
            // Give back the reserved quantity to source.
            $invModel = new InventoryModel();
            $source = $invModel->where('seller_id', $sid)->where('product_id', $tfr['product_id'])->where('warehouse_id', $tfr['from_warehouse_id'])->first();
            if ($source !== null) {
                $invModel->addStock((int) $source['id'], (float) $tfr['quantity']);
            }
            $this->flash('success', 'Transfer cancelled; source stock restored.');
        }

        $tfrModel->update($id, ['status' => $to]);

        return redirect()->to('account/inventory/transfers');
    }

    // ------------------------------------------------------------------

    private function genCode(string $prefix, int $sellerId, int $productId): string
    {
        return $prefix . '-' . $sellerId . '-' . $productId . '-' . substr(strtoupper(bin2hex(random_bytes(3))), 0, 6);
    }
}