<?php

namespace App\Controllers\Admin;

use App\Models\AuditLogModel;
use App\Models\CategoryModel;

class Categories extends AdminBase
{
    public function index()
    {
        $db = \Config\Database::connect();

        if ($this->request->getMethod() === 'POST') {
            $name = trim((string) $this->request->getPost('name'));
            $parent = (int) ($this->request->getPost('parent_id') ?? 0);
            if ($name === '') {
                $this->flash('error', 'Category name is required.');
            } else {
                (new CategoryModel())->insert([
                    'parent_id' => $parent > 0 ? $parent : null,
                    'name'      => $name,
                    'slug'      => url_title($name, '-', true),
                    'status'    => 1,
                ]);
                $this->flash('success', 'Category created.');
            }
            return redirect()->to('admin/categories');
        }

        $categories = $db->table('categories')
            ->select('categories.*, parent.name AS parent_name, (SELECT COUNT(*) FROM products p WHERE p.category_id = categories.id) AS product_count')
            ->join('categories parent', 'parent.id = categories.parent_id', 'left')
            ->orderBy('categories.sort_order', 'ASC')
            ->orderBy('categories.name', 'ASC')
            ->get()->getResultArray();

        return $this->render('admin/categories', [
            'categories' => $categories,
            'activeNav'  => 'categories',
        ], 'Categories');
    }

    public function update($id)
    {
        $model = new CategoryModel();
        $cat = $model->find($id);
        if ($cat === null) {
            $this->flash('error', 'Category not found.');
            return redirect()->to('admin/categories');
        }

        $patch = [
            'name'     => trim((string) $this->request->getPost('name')),
            'status'   => (int) (bool) $this->request->getPost('status'),
        ];
        $parent = (int) ($this->request->getPost('parent_id') ?? 0);
        $patch['parent_id'] = $parent > 0 && $parent !== (int) $id ? $parent : null;
        if ($patch['name'] !== '') {
            $patch['slug'] = url_title($patch['name'], '-', true);
        }

        $model->update($cat['id'], $patch);
        (new AuditLogModel())->log($this->adminId(), 'category_update', 'category', (int) $cat['id']);

        $this->flash('success', 'Category updated.');

        return redirect()->to('admin/categories');
    }

    public function delete($id)
    {
        $model = new CategoryModel();
        $cat = $model->find($id);
        if ($cat === null) {
            $this->flash('error', 'Category not found.');
            return redirect()->to('admin/categories');
        }

        $used = (int) service('db')->table('products')->where('category_id', (int) $id)->countAllResults();
        if ($used > 0) {
            $this->flash('error', "Cannot delete: {$used} product(s) use this category.");
        } else {
            $model->delete((int) $id);
            $this->flash('success', 'Category deleted.');
        }

        return redirect()->to('admin/categories');
    }
}