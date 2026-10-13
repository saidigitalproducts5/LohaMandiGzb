<?php

namespace App\Controllers\Admin;

use App\Models\AuditLogModel;
use App\Models\ProductModel;
use App\Services\ProductService;

class Products extends AdminBase
{
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('products p')
            ->select('p.*, c.name AS category_name, b.name AS brand_name, s.business_name')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('brands b', 'b.id = p.brand_id', 'left')
            ->join('sellers s', 's.id = p.seller_id', 'left');

        $q = trim((string) $this->request->getGet('q'));
        $status = trim((string) $this->request->getGet('status'));
        $category = (int) ($this->request->getGet('category') ?? 0);
        $seller = (int) ($this->request->getGet('seller') ?? 0);

        if ($q !== '') {
            $builder->groupStart()->like('p.name', $q)->orLike('p.hsn_code', $q)->orLike('s.business_name', $q)->groupEnd();
        }
        if ($status !== '' && $status !== 'all') {
            $builder->where('p.status', $status);
        }
        if ($category > 0) {
            $builder->where('p.category_id', $category);
        }
        if ($seller > 0) {
            $builder->where('p.seller_id', $seller);
        }

        $total = (clone $builder)->countAllResults(false);
        $limit = 25;
        $page  = max((int) ($this->request->getGet('page') ?? 1), 1);
        $rows = $builder->orderBy('p.status = "pending"', 'DESC', false)->orderBy('p.id', 'DESC')->limit($limit, ($page - 1) * $limit)->get()->getResultArray();

        $categories = $db->table('categories')->where('status', 1)->orderBy('name', 'ASC')->get()->getResultArray();

        return $this->render('admin/products', [
            'products'   => $rows,
            'categories' => $categories,
            'total'      => $total,
            'page'       => $page,
            'limit'      => $limit,
            'filters'    => ['q' => $q, 'status' => $status, 'category' => $category],
            'activeNav'  => 'products',
        ], 'Products');
    }

    public function moderate($id)
    {
        $status = trim((string) $this->request->getPost('status'));
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $this->flash('error', 'Invalid status.');
            return redirect()->back();
        }

        $productModel = new ProductModel();
        $product = $productModel->find($id);
        if ($product === null) {
            $this->flash('error', 'Product not found.');
            return redirect()->to('admin/products');
        }

        $productModel->update($product['id'], [
            'status'    => $status,
            'is_active' => $status === 'approved' ? 1 : 0,
        ]);
        (new AuditLogModel())->log($this->adminId(), 'product_moderate', 'product', (int) $product['id'], $product['status'], $status);

        $this->flash('success', 'Product ' . $status . '.');

        return redirect()->to('admin/products');
    }

    public function toggleFeatured($id)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($id);
        if ($product === null) {
            $this->flash('error', 'Product not found.');
        } else {
            $new = $product['is_featured'] ? 0 : 1;
            $productModel->update($product['id'], ['is_featured' => $new]);
            $this->flash('success', $new ? 'Product featured.' : 'Product unfeatured.');
        }

        return redirect()->to('admin/products');
    }

    public function show($id)
    {
        $product = (new ProductModel())->find($id);
        if ($product === null) {
            $this->flash('error', 'Product not found.');
            return redirect()->to('admin/products');
        }

        return $this->render('admin/product_view', [
            'product' => (new ProductService())->enrich($product),
            'activeNav' => 'products',
        ], 'Product: ' . $product['name']);
    }
}
