<?php

namespace App\Models;

use CodeIgniter\Model;

class TransportBookingModel extends Model
{
    protected $table         = 'transport_bookings';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $allowedFields = [
        'order_id', 'created_by', 'transporter_id', 'pickup_address', 'drop_address',
        'load_tonnage', 'vehicle_type', 'vehicle_id', 'freight_charge', 'status', 'tracking',
    ];

    public const STATUSES = ['requested', 'confirmed', 'in_transit', 'delivered', 'cancelled'];

    protected $beforeInsert = ['normalizeTracking'];
    protected $beforeUpdate = ['normalizeTracking'];

    protected function normalizeTracking(array $data): array
    {
        if (isset($data['data']['tracking']) && is_array($data['data']['tracking'])) {
            $data['data']['tracking'] = json_encode($data['data']['tracking']);
        }

        return $data;
    }

    public function forTransporter(int $transporterId, ?string $status = null): array
    {
        try {
            $b = $this->select('transport_bookings.*, o.order_number, v.vehicle_number, v.driver_name, v.driver_phone')
                ->join('orders o', 'o.id = transport_bookings.order_id', 'left')
                ->join('vehicles v', 'v.id = transport_bookings.vehicle_id', 'left')
                ->where('transport_bookings.transporter_id', $transporterId);
            if ($status !== null) {
                $b->where('transport_bookings.status', $status);
            }

            return $b->orderBy('transport_bookings.created_at', 'DESC')->findAll();
        } catch (\Throwable $e) {
            // Schema not migrated yet (vehicles/vehicle_id missing) — degrade gracefully.
            // Use a FRESH builder — the shared one may still hold the failed joins.
            try {
                return db_connect()->table('transport_bookings')
                    ->where('transport_bookings.transporter_id', $transporterId)
                    ->orderBy('transport_bookings.created_at', 'DESC')
                    ->get()->getResultArray();
            } catch (\Throwable $e2) {
                return [];
            }
        }
    }

    /**
     * Bookings for orders a buyer is party to (buyer side "Track transport").
     */
    public function forBuyer(int $buyerId): array
    {
        try {
            return $this->select('transport_bookings.*, o.order_number, o.total AS order_total,
                              s.business_name AS seller_name, t.fleet_name, v.vehicle_number, v.driver_name, v.driver_phone')
                ->join('orders o', 'o.id = transport_bookings.order_id', 'left')
                ->join('sellers s', 's.id = o.seller_id', 'left')
                ->join('transporters t', 't.id = transport_bookings.transporter_id', 'left')
                ->join('vehicles v', 'v.id = transport_bookings.vehicle_id', 'left')
                ->where('o.buyer_id', $buyerId)
                ->orWhere('transport_bookings.created_by', $buyerId)
                ->orderBy('transport_bookings.created_at', 'DESC')
                ->findAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Bookings for orders owned by a seller (plus ones they created directly).
     */
    public function forSeller(int $sellerId, int $userId = 0): array
    {
        try {
            return $this->select('transport_bookings.*, o.order_number, o.total AS order_total,
                              s.business_name AS seller_name, t.fleet_name, v.vehicle_number, v.driver_name, v.driver_phone')
                ->join('orders o', 'o.id = transport_bookings.order_id', 'left')
                ->join('sellers s', 's.id = o.seller_id', 'left')
                ->join('transporters t', 't.id = transport_bookings.transporter_id', 'left')
                ->join('vehicles v', 'v.id = transport_bookings.vehicle_id', 'left')
                ->groupStart()
                ->where('o.seller_id', $sellerId)
                ->orWhere('transport_bookings.created_by', $userId)
                ->groupEnd()
                ->orderBy('transport_bookings.created_at', 'DESC')
                ->findAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function addTrackingPoint(int $id, array $point): void
    {
        $booking = $this->find($id);
        $tracking = [];
        if ($booking !== null && !empty($booking['tracking'])) {
            $decoded = json_decode($booking['tracking'], true);
            $tracking = is_array($decoded) ? $decoded : [];
        }
        $tracking[] = $point;

        $this->set('tracking', $tracking)->where('id', $id)->update();
    }
}