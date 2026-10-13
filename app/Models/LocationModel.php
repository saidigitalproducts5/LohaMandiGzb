<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table         = 'locations';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $allowedFields = ['city', 'state', 'pincode', 'is_active'];

    public function states(): array
    {
        return $this->distinct()->select('state')->orderBy('state', 'ASC')->findAll();
    }

    public function citiesByState(string $state): array
    {
        return $this->where('state', $state)->orderBy('city', 'ASC')->findAll();
    }

    public function findByCityState(string $city, string $state): ?array
    {
        return $this->where('city', $city)->where('state', $state)->first();
    }
}
