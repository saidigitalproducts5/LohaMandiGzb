<?php

namespace App\Models;

use CodeIgniter\Model;

class VehicleModel extends Model
{
    protected $table         = 'vehicles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $allowedFields = [
        'transporter_id', 'vehicle_number', 'driver_name', 'driver_phone', 'vehicle_type', 'is_active',
    ];

    public const TYPES = ['Mini Truck', 'Pickup', 'Container', 'Flatbed Trailer', 'Tata Ace', 'Eicher 14ft', 'Truck 32ft'];

    public function forTransporter(int $transporterId, bool $activeOnly = false): array
    {
        $b = $this->where('transporter_id', $transporterId);
        if ($activeOnly) {
            $b->where('is_active', 1);
        }

        return $b->orderBy('id', 'DESC')->findAll();
    }

    public function countForTransporter(int $transporterId): int
    {
        try {
            return (int) $this->where('transporter_id', $transporterId)->countAllResults();
        } catch (\Throwable $e) {
            // vehicles table not migrated yet — don't break the dashboard.
            return 0;
        }
    }

    /**
     * Keep transporters.vehicle_count in sync with real vehicle rows.
     */
    public function syncFleetCount(int $transporterId): void
    {
        (new TransporterModel())->update($transporterId, [
            'vehicle_count' => $this->countForTransporter($transporterId),
        ]);
    }
}
