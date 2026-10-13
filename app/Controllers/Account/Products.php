<?php

namespace App\Controllers\Account;

use App\Models\BrandModel;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\SellerModel;
use App\Services\ProductService;

class Products extends AccountBase
{
    protected $helpers = ['url'];

    private function sellerIdOrRedirect()
    {
        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found. Please contact support.');

            return 0;
        }

        return (int) $seller['id'];
    }

    public function index()
    {
        $sellerId = $this->sellerIdOrRedirect();
        if ($sellerId === 0) {
            return;
        }

        $db = \Config\Database::connect();
        $q = trim((string) $this->request->getGet('q'));
        $status = trim((string) $this->request->getGet('status'));

        $builder = $db->table('products p')
            ->select('p.*, c.name AS category_name, b.name AS brand_name')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('brands b', 'b.id = p.brand_id', 'left')
            ->where('p.seller_id', $sellerId);

        if ($q !== '') {
            $builder->groupStart()->like('p.name', $q)->orLike('p.hsn_code', $q)->groupEnd();
        }
        if ($status !== '' && $status !== 'all') {
            $builder->where('p.status', $status);
        }

        $total = (clone $builder)->countAllResults(false);
        $limit = 25;
        $page  = max((int) ($this->request->getGet('page') ?? 1), 1);
        $rows  = $builder->orderBy('p.status = "pending"', 'DESC', false)->orderBy('p.id', 'DESC')
            ->limit($limit, ($page - 1) * $limit)->get()->getResultArray();
        $rows  = (new ProductService())->enrichMany($rows);

        return $this->render('account/products', [
            'products'  => $rows,
            'total'     => $total,
            'page'      => $page,
            'limit'     => $limit,
            'filters'   => ['q' => $q, 'status' => $status],
            'activeNav' => 'products',
        ], 'My Products');
    }

    public function view($id)
    {
        $sellerId = $this->sellerIdOrRedirect();
        if ($sellerId === 0) {
            return;
        }

        $row = \Config\Database::connect()->table('products p')
            ->select('p.*, c.name AS category_name, b.name AS brand_name')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('brands b', 'b.id = p.brand_id', 'left')
            ->where('p.id', (int) $id)
            ->where('p.seller_id', $sellerId)
            ->get()->getRowArray();
        if ($row === null) {
            $this->flash('error', 'Product not found.');

            return redirect()->to('account/products');
        }

        $product = (new ProductService())->enrich($row);

        return $this->render('account/product_view', [
            'product'   => $product,
            'activeNav' => 'products',
        ], 'Product Details');
    }

    public function form($id = null)
    {
        $sellerId = $this->sellerIdOrRedirect();
        if ($sellerId === 0) {
            return;
        }

        $product = null;
        if ($id !== null) {
            $product = (new ProductModel())->findForSeller($sellerId, (int) $id);
            if ($product === null) {
                $this->flash('error', 'Product not found.');
                return redirect()->to('account/products');
            }
        }

        $categories = (new CategoryModel())->activeCategories();
        $brands = db_connect()->table('brands')->where('status', 1)->orderBy('name', 'ASC')->get()->getResultArray();

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'category_id'    => (int) $this->request->getPost('category_id'),
                'brand_id'       => (int) ($this->request->getPost('brand_id') ?? 0),
                'name'           => trim((string) $this->request->getPost('name')),
                'description'    => trim((string) $this->request->getPost('description')),
                'hsn_code'       => trim((string) $this->request->getPost('hsn_code')),
                'gst_rate'       => (float) $this->request->getPost('gst_rate'),
                'moq'            => (float) $this->request->getPost('moq'),
                'unit'           => trim((string) $this->request->getPost('unit')),
                'grade'          => trim((string) $this->request->getPost('grade')),
                'weight'         => trim((string) $this->request->getPost('weight')),
                'dimensions'     => trim((string) $this->request->getPost('dimensions')),
                'delivery_time'  => trim((string) $this->request->getPost('delivery_time')),
                'price'          => (float) $this->request->getPost('price'),
                'offer_price'    => $this->request->getPost('offer_price') !== '' ? (float) $this->request->getPost('offer_price') : null,
                'available_stock'=> (float) $this->request->getPost('available_stock'),
                'is_bulk'        => (int) (bool) $this->request->getPost('is_bulk'),
                'location'       => trim((string) $this->request->getPost('location')),
            ];

            $validation = \Config\Services::validation();
            $validation->setRules([
                'name'        => 'required|min_length[3]|max_length[190]',
                'category_id' => 'required|is_natural_no_zero',
                'unit'        => 'required|max_length[16]',
                'hsn_code'    => 'permit_empty|max_length[16]',
                'gst_rate'    => 'permit_empty|greater_than_equal_to[0]|less_than_equal_to[30]',
                'price'       => 'permit_empty|greater_than[0]',
            ]);

            if (!$validation->run($data)) {
                return $this->render('account/product_form', [
                    'errors' => $validation->getErrors(),
                    'product' => $data,
                    'categories' => $categories,
                    'brands' => $brands,
                    'isEdit' => $id !== null,
                    'activeNav' => 'products',
                ], $id !== null ? 'Edit Product' : 'Add Product');
            }

            $productModel = new ProductModel();
            $fields = [
                'seller_id'     => $sellerId,
                'category_id'   => $data['category_id'],
                'brand_id'      => $data['brand_id'] > 0 ? $data['brand_id'] : null,
                'name'          => $data['name'],
                'description'   => $data['description'] !== '' ? $data['description'] : null,
                'hsn_code'      => $data['hsn_code'] !== '' ? $data['hsn_code'] : null,
                'gst_rate'      => $data['gst_rate'],
                'moq'           => $data['moq'],
                'unit'          => $data['unit'],
                'grade'         => $data['grade'] !== '' ? $data['grade'] : null,
                'weight'        => $data['weight'] !== '' ? $data['weight'] : null,
                'dimensions'    => $data['dimensions'] !== '' ? $data['dimensions'] : null,
                'delivery_time' => $data['delivery_time'] !== '' ? $data['delivery_time'] : null,
                'price'         => $data['price'] > 0 ? $data['price'] : null,
                'offer_price'   => $data['offer_price'],
                'available_stock' => $data['available_stock'],
                'is_bulk'       => $data['is_bulk'],
                'location'      => $data['location'] !== '' ? $data['location'] : null,
            ];

            if ($id !== null) {
                $productModel->update((int) $id, $fields);
                $message = 'Product updated.';
            } else {
                $fields['seller_id'] = $sellerId;
                $fields['status'] = 'pending';
                $fields['is_active'] = 0;
                $productModel->insert($fields);
                $message = 'Product added. It is pending admin approval.';
            }

            $this->handleUploads((int) ($id ?? $productModel->getInsertID()), $productModel);

            $this->flash('success', $message);

            return redirect()->to('account/products');
        }

        return $this->render('account/product_form', [
            'product'    => $product,
            'categories' => $categories,
            'brands'     => $brands,
            'isEdit'     => $id !== null,
            'activeNav'  => 'products',
        ], $id !== null ? 'Edit Product' : 'Add Product');
    }

    public function toggleActive($id)
    {
        $sellerId = $this->sellerIdOrRedirect();
        if ($sellerId === 0) {
            return;
        }

        $product = (new ProductModel())->findForSeller($sellerId, (int) $id);
        if ($product === null) {
            $this->flash('error', 'Product not found.');
        } else {
            $new = $product['is_active'] ? 0 : 1;
            (new ProductModel())->update($product['id'], ['is_active' => $new]);
            $this->flash('success', $new ? 'Product is now live.' : 'Product has been paused.');
        }

        return redirect()->to('account/products');
    }

    public function delete($id)
    {
        $sellerId = $this->sellerIdOrRedirect();
        if ($sellerId === 0) {
            return;
        }

        $product = (new ProductModel())->findForSeller($sellerId, (int) $id);
        if ($product === null) {
            $this->flash('error', 'Product not found.');
        } else {
            (new ProductImageModel())->deleteForProduct((int) $id);
            (new ProductModel())->delete((int) $id);
            $this->flash('success', 'Product deleted.');
        }

        return redirect()->to('account/products');
    }

    private function handleUploads(int $productId, ProductModel $model)
    {
        $file = $this->request->getFile('image');
        if ($file === null || !$file->isValid() || $file->hasMoved()) {
            return;
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/products', $newName);

        $imageModel = new ProductImageModel();
        $existing = $imageModel->forProduct($productId);

        $imageModel->insert([
            'product_id' => $productId,
            'file_path'  => 'products/' . $newName,
            'is_primary' => empty($existing) ? 1 : 0,
            'sort_order' => count($existing) + 1,
        ]);
    }
}