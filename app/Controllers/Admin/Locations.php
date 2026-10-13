<?php

namespace App\Controllers\Admin;

use App\Models\AuditLogModel;
use App\Models\LocationModel;

class Locations extends AdminBase
{
    public function index()
    {
        $db = \Config\Database::connect();

        if ($this->request->getMethod() === 'POST') {
            $city = trim((string) $this->request->getPost('city'));
            $state = trim((string) $this->request->getPost('state'));
            $pincode = trim((string) $this->request->getPost('pincode'));

            if ($city === '' || $state === '') {
                $this->flash('error', 'City and state are required.');
            } else {
                (new LocationModel())->insert([
                    'city'   => $city,
                    'state'  => $state,
                    'pincode' => $pincode !== '' ? $pincode : null,
                    'is_active' => 1,
                ]);
                $this->flash('success', 'Location added.');
            }
            return redirect()->to('admin/locations');
        }

        $locations = $db->table('locations')
            ->select('locations.*, (SELECT COUNT(*) FROM sellers s WHERE s.city = locations.city AND s.state = locations.state) AS seller_count')
            ->orderBy('locations.state', 'ASC')->orderBy('locations.city', 'ASC')->get()->getResultArray();

        return $this->render('admin/locations', [
            'locations' => $locations,
            'activeNav' => 'locations',
        ], 'Locations');
    }

    public function update($id)
    {
        $model = new LocationModel();
        $loc = $model->find($id);
        if ($loc === null) {
            $this->flash('error', 'Location not found.');
            return redirect()->to('admin/locations');
        }

        $pincode = trim((string) $this->request->getPost('pincode'));
        $model->update($loc['id'], [
            'pincode'   => $pincode !== '' ? $pincode : null,
            'is_active' => (int) (bool) $this->request->getPost('is_active'),
        ]);
        (new AuditLogModel())->log($this->adminId(), 'location_update', 'location', (int) $loc['id']);

        $this->flash('success', 'Location updated.');

        return redirect()->to('admin/locations');
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $loc = (new LocationModel())->find($id);
        if ($loc === null) {
            $this->flash('error', 'Location not found.');
            return redirect()->to('admin/locations');
        }

        $used = (int) $db->table('sellers')->where('city', $loc['city'])->where('state', $loc['state'])->countAllResults();
        if ($used > 0) {
            $this->flash('error', "Cannot delete: {$used} seller(s) located in {$loc['city']}.");
        } else {
            (new LocationModel())->delete((int) $id);
            $this->flash('success', 'Location deleted.');
        }

        return redirect()->to('admin/locations');
    }
}