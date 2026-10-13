<?php

namespace App\Controllers\Account;

use App\Models\OrderModel;
use App\Models\SellerModel;
use App\Models\TransportBookingModel;
use App\Models\TransporterModel;
use App\Models\VehicleModel;

/**
 * Transport — bookings & fleet discovery.
 * Transporters manage their own bookings; sellers browse fleets and book freight.
 */
class Transport extends AccountBase
{
    public function index()
    {
        $role = $this->uRole();

        if ($role === 'transporter') {
            $transporter = (new TransporterModel())->findByUser($this->uid());
            if ($transporter === null) {
                $this->flash('error', 'No fleet profile found. Contact support.');

                return redirect()->to('account');
            }

            $bookings = (new TransportBookingModel())->forTransporter((int) $transporter['id']);

            return $this->render('account/transport_bookings', [
                'bookings'    => $bookings,
                'transporter' => $transporter,
                'isBuyerSide' => false,
                'activeNav'   => 'transport',
                'title2'      => 'Transport Bookings',
            ], 'Transport Bookings');
        }

        // Seller / buyer side: bookings on orders they are party to.
        $bookings = [];
        if ($role === 'seller') {
            $seller = (new SellerModel())->findByUser($this->uid());
            if ($seller !== null) {
                $bookings = (new TransportBookingModel())->forSeller((int) $seller['id'], $this->uid());
            }
        } else {
            $bookings = (new TransportBookingModel())->forBuyer($this->uid());
        }

        return $this->render('account/transport_bookings', [
            'bookings'    => $bookings,
            'transporter' => null,
            'isBuyerSide' => true,
            'role'        => $role,
            'activeNav'   => 'transport',
            'title2'      => 'My Transport',
        ], 'Transport');
    }

    /**
     * Fleet directory — sellers/buyers pick a transporter and book.
     */
    public function fleets()
    {
        if ($this->uRole() === 'transporter') {
            return redirect()->to('account/transport');
        }

        $db = \Config\Database::connect();
        $transporters = $db->table('transporters t')
            ->select('t.*, u.name AS owner_name, u.mobile AS owner_mobile,
                      (SELECT COUNT(*) FROM vehicles v WHERE v.transporter_id = t.id AND v.is_active = 1) AS vehicles_total')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->where('u.status', 'active')
            ->orderBy('t.verified', 'DESC')
            ->orderBy('vehicles_total', 'DESC')
            ->get()->getResultArray();

        return $this->render('account/transport_fleets', [
            'transporters' => $transporters,
            'activeNav'    => 'transport',
        ], 'Transporters');
    }

    /**
     * Create a booking — transporter for own freight, seller/buyer pick a fleet.
     */
    public function create(?int $orderId = null)
    {
        $role = $this->uRole();

        $transporter = null;
        if ($role === 'transporter') {
            $transporter = (new TransporterModel())->findByUser($this->uid());
            if ($transporter === null) {
                $this->flash('error', 'No fleet profile found.');

                return redirect()->to('account');
            }
        }

        $order = null;
        if ($orderId !== null) {
            $order = (new OrderModel())->find($orderId);
            if ($order === null || !$this->canSeeOrder($order)) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Order not found.');
            }
        }

        // Seller/buyer must choose a fleet (preselectable via ?t=ID).
        $chosenId = (int) ($this->request->GetGet('t') ?? $this->request->getPost('transporter_id') ?? 0);
        $fleets = $role === 'transporter' ? [] : $this->fleetChoices();

        if ($role !== 'transporter') {
            if ($chosenId > 0) {
                foreach ($fleets as $f) {
                    if ((int) $f['id'] === $chosenId) {
                        $transporter = $f;
                        break;
                    }
                }
            }
            $transporter = $transporter ?? null;
        }

        $vehicles = [];
        if ($role === 'transporter' && $transporter !== null) {
            $vehicles = (new VehicleModel())->forTransporter((int) $transporter['id'], true);
        } elseif ($role !== 'transporter') {
            // All vehicles across active fleets (client-side filtered by chosen fleet).
            $vehicles = \Config\Database::connect()->table('vehicles v')
                ->select('v.*, t.fleet_name')
                ->join('transporters t', 't.id = v.transporter_id', 'left')
                ->join('users u', 'u.id = t.user_id', 'left')
                ->where('v.is_active', 1)
                ->where('u.status', 'active')
                ->orderBy('v.transporter_id', 'ASC')
                ->get()->getResultArray();
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'pickup_address' => trim((string) $this->request->getPost('pickup_address')),
                'drop_address'   => trim((string) $this->request->getPost('drop_address')),
                'load_tonnage'   => (float) $this->request->getPost('load_tonnage'),
                'vehicle_type'   => trim((string) $this->request->getPost('vehicle_type')),
                'freight_charge' => (float) ($this->request->getPost('freight_charge') ?: 0),
                'vehicle_id'     => (int) $this->request->getPost('vehicle_id'),
            ];

            $rules = [
                'pickup_address' => 'required|min_length[3]',
                'drop_address'   => 'required|min_length[3]',
                'load_tonnage'   => 'required|greater_than[0]',
            ];
            $messages = [];
            if ($role !== 'transporter') {
                $rules['transporter_id'] = 'required|greater_than[0]';
                $messages['transporter_id'] = ['required' => 'Please choose a transporter.'];
            }

            $validation = \Config\Services::validation();
            $validation->setRules($rules, $messages);

            if (!$validation->run($data + ['transporter_id' => $chosenId])) {
                return $this->render('account/transport_form', [
                    'errors'      => $validation->getErrors(),
                    'old'         => $data + ['transporter_id' => $chosenId],
                    'order'       => $order,
                    'fleets'      => $fleets,
                    'vehicles'    => $vehicles,
                    'transporter' => $transporter,
                    'isCreate'    => true,
                    'activeNav'   => 'transport',
                ], 'New Transport Booking');
            }

            if ($transporter === null) {
                $this->flash('error', 'Selected transporter not found. Please choose again.');

                return redirect()->to('account/transport/new');
            }

            // Validate the chosen vehicle belongs to the chosen fleet.
            $vehicleId = null;
            if ($data['vehicle_id'] > 0) {
                foreach ($vehicles as $v) {
                    if ((int) $v['id'] === $data['vehicle_id']) {
                        $vehicleId = (int) $v['id'];
                        if ($data['vehicle_type'] === '') {
                            $data['vehicle_type'] = $v['vehicle_type'];
                        }
                        break;
                    }
                }
            }

            $booking = new TransportBookingModel();
            $bookingId = (int) $booking->insert([
                'order_id'       => $orderId,
                'created_by'     => $this->uid(),
                'transporter_id' => (int) $transporter['id'],
                'pickup_address' => $data['pickup_address'],
                'drop_address'   => $data['drop_address'],
                'load_tonnage'   => $data['load_tonnage'],
                'vehicle_type'   => $data['vehicle_type'] !== '' ? $data['vehicle_type'] : null,
                'vehicle_id'     => $vehicleId,
                'freight_charge' => $data['freight_charge'] > 0 ? $data['freight_charge'] : null,
                'status'         => 'requested',
            ]);

            // Notify the transporter + order parties.
            $notify = new \App\Models\NotificationModel();
            $notify->push((int) $transporter['user_id'], 'New transport booking #' . $bookingId, 'A new freight booking needs your confirmation.', 'transport', $bookingId);

            if ($orderId !== null && $order !== null) {
                $seller = (new SellerModel())->find((int) $order['seller_id']);
                if ($seller !== null) {
                    $notify->push((int) $seller['user_id'], 'Transport booked for ' . $order['order_number'], 'A transport booking was created for your order.', 'transport', $bookingId);
                }
                $notify->push((int) $order['buyer_id'], 'Transport booked for ' . $order['order_number'], 'Freight for your order has been arranged.', 'transport', $bookingId);
            }

            $this->flash('success', 'Transport booking created.');

            return redirect()->to('account/transport/' . $bookingId);
        }

        return $this->render('account/transport_form', [
            'errors'      => [],
            'old'         => [
                'pickup_address' => $order ? ($order['shipping_address'] ?: '') : '',
                'drop_address'   => $order ? ($order['shipping_address'] ?: '') : '',
                'load_tonnage'   => $order ? (float) $order['total'] / 1000 : 1,
                'vehicle_type'   => '',
                'freight_charge' => '',
                'transporter_id' => $chosenId,
                'vehicle_id'     => 0,
            ],
            'order'       => $order,
            'fleets'      => $fleets,
            'vehicles'    => $vehicles,
            'transporter' => $transporter,
            'isCreate'    => true,
            'activeNav'   => 'transport',
        ], 'New Transport Booking');
    }

    public function show(int $id)
    {
        $booking = (new TransportBookingModel())->find($id);
        if ($booking === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Booking not found.');
        }

        $role = $this->uRole();
        $transporter = null;
        if ($role === 'transporter') {
            $transporter = (new TransporterModel())->findByUser($this->uid());
            if ($transporter === null || (int) $booking['transporter_id'] !== (int) $transporter['id']) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Not your booking.');
            }
        } else {
            // Buyer/seller may view if they created it or are party to the linked order.
            if ((int) ($booking['created_by'] ?? 0) === $this->uid()) {
                // ok — creator
            } else {
                $order = (int) $booking['order_id'] > 0 ? (new OrderModel())->find((int) $booking['order_id']) : null;
                if ($order === null || !$this->canSeeOrder($order)) {
                    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Not your booking.');
                }
            }
        }

        $order = null;
        if ($booking['order_id']) {
            $order = (new OrderModel())->find((int) $booking['order_id']);
        }

        $tracking = [];
        if (!empty($booking['tracking'])) {
            $decoded = json_decode($booking['tracking'], true);
            $tracking = is_array($decoded) ? $decoded : [];
        }

        $sellerName = null;
        if ($order !== null) {
            $seller = (new SellerModel())->find((int) $order['seller_id']);
            $sellerName = $seller['business_name'] ?? null;
        }

        $fleet = (new TransporterModel())->find((int) $booking['transporter_id']);
        $vehicle = !empty($booking['vehicle_id']) ? (new VehicleModel())->find((int) $booking['vehicle_id']) : null;

        return $this->render('account/transport_view', [
            'booking'       => $booking,
            'order'         => $order,
            'sellerName'    => $sellerName,
            'tracking'      => $tracking,
            'transporter'   => $transporter,
            'fleet'         => $fleet,
            'vehicle'       => $vehicle,
            'isTransporter' => $role === 'transporter',
            'statuses'      => TransportBookingModel::STATUSES,
            'activeNav'     => 'transport',
        ], 'Booking #' . $id);
    }

    public function updateStatus(int $id)
    {
        $booking = (new TransportBookingModel())->find($id);
        if ($booking === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Booking not found.');
        }

        if ($this->uRole() !== 'transporter') {
            $this->flash('error', 'Only transporters update booking status.');

            return redirect()->to('account/transport/' . $id);
        }

        $transporter = (new TransporterModel())->findByUser($this->uid());
        if ($transporter === null || (int) $booking['transporter_id'] !== (int) $transporter['id']) {
            $this->flash('error', 'Not your booking.');

            return redirect()->to('account/transport');
        }

        $status = trim((string) $this->request->getPost('status'));
        if (!in_array($status, TransportBookingModel::STATUSES, true)) {
            $this->flash('error', 'Invalid status.');

            return redirect()->to('account/transport/' . $id);
        }

        $model = new TransportBookingModel();
        $model->where('id', $id)->set([
            'status' => $status,
        ])->update();

        $point = [
            'status' => $status,
            'note'   => trim((string) $this->request->getPost('note')),
            'at'     => date('Y-m-d H:i:s'),
            'by'     => $this->uName(),
        ];
        if ($point['note'] !== '') {
            $model->addTrackingPoint($id, $point);
        }

        // Notify order parties.
        if ($booking['order_id']) {
            $order = (new OrderModel())->find((int) $booking['order_id']);
            if ($order !== null) {
                $notify = new \App\Models\NotificationModel();
                $notify->push((int) $order['buyer_id'], 'Booking #' . $id . ' is ' . str_replace('_', ' ', $status), 'Transport status update on order ' . $order['order_number'] . '.', 'transport', $id);
                $seller = (new SellerModel())->find((int) $order['seller_id']);
                if ($seller !== null) {
                    $notify->push((int) $seller['user_id'], 'Booking #' . $id . ' is ' . str_replace('_', ' ', $status), 'Transport status update on order ' . $order['order_number'] . '.', 'transport', $id);
                }
            }
        }

        $this->flash('success', 'Booking status updated to ' . ucfirst(str_replace('_', ' ', $status)) . '.');

        return redirect()->to('account/transport/' . $id);
    }

    private function fleetChoices(): array
    {
        $db = \Config\Database::connect();

        return $db->table('transporters t')
            ->select('t.*, u.name AS owner_name,
                      (SELECT COUNT(*) FROM vehicles v WHERE v.transporter_id = t.id AND v.is_active = 1) AS vehicles_total')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->where('u.status', 'active')
            ->orderBy('t.verified', 'DESC')
            ->orderBy('vehicles_total', 'DESC')
            ->get()->getResultArray();
    }

    private function canSeeOrder(array $order): bool
    {
        $uid = $this->uid();

        if ((int) $order['buyer_id'] === $uid) {
            return true;
        }

        // orders.seller_id references sellers.id — compare against the caller's seller row.
        $seller = (new SellerModel())->findByUser($uid);

        return $seller !== null && (int) $order['seller_id'] === (int) $seller['id'];
    }
}
