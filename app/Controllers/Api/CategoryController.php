<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Models\CategoryModel;

class CategoryController extends ApiController
{
    public function index()
    {
        $categoryModel = new CategoryModel();

        $parentId = (int) ($this->request->getGet('parent_id') ?? 0);
        $categories = $categoryModel->activeCategories($parentId);

        return $this->respondOK($categories);
    }

    public function tree()
    {
        $categoryModel = new CategoryModel();
        $all = $categoryModel->where('status', 1)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->findAll();

        $tree = [];
        foreach ($all as $c) {
            $c['children'] = [];
            $tree[$c['id']] = $c;
        }
        foreach ($tree as $id => &$node) {
            if (!empty($node['parent_id']) && isset($tree[$node['parent_id']])) {
                $tree[$node['parent_id']]['children'][] = &$node;
            }
        }
        unset($node);

        $roots = array_values(array_filter($tree, fn($n) => empty($n['parent_id'])));

        return $this->respondOK($roots);
    }

    public function show($id = null)
    {
        $category = (new CategoryModel())->find($id);
        if ($category === null) {
            return $this->respondNotFound('Category not found.');
        }

        return $this->respondOK($category);
    }
}