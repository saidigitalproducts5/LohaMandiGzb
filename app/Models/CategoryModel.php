<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'parent_id', 'name', 'slug', 'icon', 'image', 'description', 'status', 'sort_order',
    ];

    public function activeCategories(int $parentId = 0): array
    {
        return $this->where('status', 1)
            ->where('parent_id', $parentId === 0 ? null : $parentId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }
}
