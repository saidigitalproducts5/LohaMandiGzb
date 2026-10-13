<?php

namespace App\Controllers\Admin;

use App\Models\AuditLogModel;
use App\Models\BrandModel;

class Brands extends AdminBase
{
    public function index()
    {
        if ($this->request->getMethod() === 'POST') {
            $name = trim((string) $this->request->getPost('name'));
            if ($name === '') {
                $this->flash('error', 'Brand name is required.');
            } else {
                (new BrandModel())->insert([
                    'name'   => $name,
                    'slug'   => url_title($name, '-', true),
                    'status' => 1,
                ]);
                $this->flash('success', 'Brand created.');
            }
            return redirect()->to('admin/brands');
        }

        $db = \Config\Database::connect();
        $brands = $db->table('brands')
            ->select('brands.*, (SELECT COUNT(*) FROM products p WHERE p.brand_id = brands.id) AS product_count')
            ->orderBy('brands.name', 'ASC')->get()->getResultArray();

        return $this->render('admin/brands', [
            'brands' => $brands,
            'activeNav' => 'brands',
        ], 'Brands');
    }

    public function update($id)
    {
        $model = new BrandModel();
        $brand = $model->find($id);
        if ($brand === null) {
            $this->flash('error', 'Brand not found.');
            return redirect()->to('admin/brands');
        }

        $name = trim((string) $this->request->getPost('name'));
        $patch = [
            'name'   => $name,
            'status' => (int) (bool) $this->request->getPost('status'),
        ];
        if ($name !== '') {
            $patch['slug'] = url_title($name, '-', true);
        }
        $model->update($brand['id'], $patch);
        (new AuditLogModel())->log($this->adminId(), 'brand_update', 'brand', (int) $brand['id']);

        $this->flash('success', 'Brand updated.');

        return redirect()->to('admin/brands');
    }

    public function delete($id)
    {
        $used = (int) service('db')->table('products')->where('brand_id', (int) $id)->countAllResults();
        if ($used > 0) {
            $this->flash('error', "Cannot delete: {$used} product(s) use this brand.");
        } else {
            (new BrandModel())->delete((int) $id);
            $this->flash('success', 'Brand deleted.');
        }

        return redirect()->to('admin/brands');
    }
}