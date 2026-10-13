<?php

namespace App\Models;

use CodeIgniter\Model;

class TransporterModel extends Model
{
    protected $table         = 'transporters';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $allowedFields = [
        'user_id', 'fleet_name', 'vehicle_count', 'route_from', 'route_to', 'verified',
    ];

    public function findByUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    public function activeTransporters(): array
    {
        return $this->select('transporters.*, u.name AS owner_name, u.mobile AS owner_mobile')
            ->join('users u', 'u.id = transporters.user_id', 'left')
            ->where('u.status', 'active')
            ->where('transporters.verified', 1)
            ->orderBy('transporters.vehicle_count', 'DESC')
            ->findAll();
    }

    public function fleetStats(int $id): array
    {
        $db = db_connect();
        return [
            'bookings'   => (int) $db->table('transport_bookings')->where('transporter_id', $id)->countAllResults(),
            'in_transit' => (int) $db->table('transport_bookings')->where('transporter_id', $id)->where('status', 'in_transit')->countAllResults(),
            'delivered'  => (int) $db->table('transport_bookings')->where('transporter_id', $id)->where('status', 'delivered')->countAllResults(),
            'revenue'    => (float) ($db->table('transport_bookings')->where('transporter_id', $id)->where('status', 'delivered')->selectSum('freight_charge')->get()->getRow()->freight_charge ?? 0),
        ];
    }
}