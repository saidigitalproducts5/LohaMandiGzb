<?php

namespace App\Controllers\Account;

use App\Models\TransporterModel;
use App\Models\VehicleModel;

/**
 * Vehicles — transporters manage fleet vehicles (number, driver, type).
 */
class Vehicles extends AccountBase
{
    private function transporterOrRedirect(): ?array
    {
        $transporter = (new TransporterModel())->findByUser($this->uid());
        if ($transporter === null) {
            $this->flash('error', 'No fleet profile found. Please register as a transporter.');
            return null;
        }

        return $transporter;
    }

    public function index()
    {
        if ($this->uRole() !== 'transporter') {
            $this->flash('error', 'Only transporter accounts manage vehicles.');
            return redirect()->to('account');
        }

        $transporter = $this->transporterOrRedirect();
        if ($transporter === null) {
            return redirect()->to('account');
        }

        $vehicles = (new VehicleModel())->forTransporter((int) $transporter['id']);

        return $this->render('account/vehicles', [
            'transporter' => $transporter,
            'vehicles'    => $vehicles,
            'types'       => VehicleModel::TYPES,
            'errors'      => [],
            'old'         => [],
            'activeNav'   => 'vehicles',
        ], 'My Vehicles');
    }

    public function add()
    {
        if ($this->uRole() !== 'transporter') {
            $this->flash('error', 'Only transporter accounts manage vehicles.');
            return redirect()->to('account');
        }

        $transporter = $this->transporterOrRedirect();
        if ($transporter === null) {
            return redirect()->to('account');
        }

        $data = [
            'vehicle_number' => strtoupper(trim((string) $this->request->getPost('vehicle_number'))),
            'driver_name'    => trim((string) $this->request->getPost('driver_name')),
            'driver_phone'   => trim((string) $this->request->getPost('driver_phone')),
            'vehicle_type'   => trim((string) $this->request->getPost('vehicle_type')),
        ];

        $validation = \Config\Services::validation();
        $validation->setRules([
            'vehicle_number' => 'required|min_length[4]|max_length[32]',
            'driver_name'    => 'required|min_length[2]|max_length[120]',
            'driver_phone'   => 'required|regex_match[/^[6-9][0-9]{9}$/]',
            'vehicle_type'   => 'required|max_length[64]',
        ], [
            'vehicle_number' => ['required' => 'Vehicle number is required.', 'min_length' => 'Enter a valid vehicle number (e.g. GA01AB1234).'],
            'driver_name'    => ['required' => 'Driver name is required.'],
            'driver_phone'   => ['required' => 'Driver phone number is required.', 'regex_match' => 'Enter a valid 10-digit mobile number.'],
            'vehicle_type'   => ['required' => 'Vehicle type is required.'],
        ]);

        if (!$validation->run($data)) {
            $vehicles = (new VehicleModel())->forTransporter((int) $transporter['id']);

            return $this->render('account/vehicles', [
                'transporter' => $transporter,
                'vehicles'    => $vehicles,
                'types'       => VehicleModel::TYPES,
                'errors'      => $validation->getErrors(),
                'old'         => $data,
                'activeNav'   => 'vehicles',
            ], 'My Vehicles');
        }

        $vehicleModel = new VehicleModel();

        // Duplicate number within the same fleet?
        foreach ($vehicleModel->forTransporter((int) $transporter['id']) as $v) {
            if (strcasecmp((string) $v['vehicle_number'], $data['vehicle_number']) === 0) {
                $this->flash('error', 'This vehicle number is already added to your fleet.');

                return redirect()->to('account/vehicles');
            }
        }

        $vehicleModel->insert([
            'transporter_id' => (int) $transporter['id'],
            'vehicle_number' => $data['vehicle_number'],
            'driver_name'    => $data['driver_name'],
            'driver_phone'   => $data['driver_phone'],
            'vehicle_type'   => $data['vehicle_type'],
        ]);
        $vehicleModel->syncFleetCount((int) $transporter['id']);

        $this->flash('success', 'Vehicle added to your fleet.');

        return redirect()->to('account/vehicles');
    }

    public function delete(int $id)
    {
        if ($this->uRole() !== 'transporter') {
            $this->flash('error', 'Only transporter accounts manage vehicles.');
            return redirect()->to('account');
        }

        $transporter = $this->transporterOrRedirect();
        if ($transporter === null) {
            return redirect()->to('account');
        }

        $vehicle = (new VehicleModel())->find($id);
        if ($vehicle === null || (int) $vehicle['transporter_id'] !== (int) $transporter['id']) {
            $this->flash('error', 'Vehicle not found.');
            return redirect()->to('account/vehicles');
        }

        (new VehicleModel())->delete($id);
        (new VehicleModel())->syncFleetCount((int) $transporter['id']);

        $this->flash('success', 'Vehicle removed.');

        return redirect()->to('account/vehicles');
    }
}
