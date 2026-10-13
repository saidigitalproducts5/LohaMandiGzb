<?php

namespace App\Controllers\Admin;

use App\Models\AdvertisementModel;
use App\Models\AuditLogModel;

class Ads extends AdminBase
{
    public function index()
    {
        $model = new AdvertisementModel();
        $db = db_connect();

        if ($this->request->getMethod() === 'POST') {
            $data = $this->collect();
            if ($data['title'] === '') {
                $this->flash('error', 'Ad title is required.');
            } else {
                $model->insert($data);
                (new AuditLogModel())->log($this->adminId(), 'ad_create', 'ad', (int) db_connect()->insertID());
                $this->flash('success', 'Ad created.');
            }
            return redirect()->to('admin/ads');
        }

        $ads = $db->table('advertisements a')
            ->select('a.*, s.business_name AS seller_name, p.name AS product_name, c.name AS category_name')
            ->join('sellers s', 's.id = a.seller_id', 'left')
            ->join('products p', 'p.id = a.product_id', 'left')
            ->join('categories c', 'c.id = a.category_id', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(100)
            ->get()->getResultArray();

        return $this->render('admin/ads', [
            'ads'        => $ads,
            'sellers'    => $db->table('sellers')->select('id, business_name')->where('status', 'active')->orderBy('business_name', 'ASC')->get()->getResultArray(),
            'products'   => $db->table('products')->select('id, name')->where('status', 'approved')->orderBy('name', 'ASC')->limit(500)->get()->getResultArray(),
            'categories' => $db->table('categories')->select('id, name')->orderBy('name', 'ASC')->get()->getResultArray(),
            'activeNav'  => 'ads',
        ], 'Ads');
    }

    public function update($id)
    {
        $model = new AdvertisementModel();
        $ad = $model->find($id);
        if ($ad === null) {
            $this->flash('error', 'Ad not found.');
            return redirect()->to('admin/ads');
        }

        $data = $this->collect();
        if ($data['title'] === '') {
            $this->flash('error', 'Ad title is required.');
            return redirect()->to('admin/ads');
        }

        if ($data['image'] === null) {
            unset($data['image']);
        }

        $model->update($ad['id'], $data);
        (new AuditLogModel())->log($this->adminId(), 'ad_update', 'ad', (int) $ad['id']);

        $this->flash('success', 'Ad updated.');
        return redirect()->to('admin/ads');
    }

    public function toggle($id)
    {
        $model = new AdvertisementModel();
        $ad = $model->find($id);
        if ($ad === null) {
            $this->flash('error', 'Ad not found.');
            return redirect()->to('admin/ads');
        }

        $model->update($ad['id'], ['status' => $ad['status'] ? 0 : 1]);
        $this->flash('success', $ad['status'] ? 'Ad hidden.' : 'Ad published.');
        return redirect()->to('admin/ads');
    }

    public function delete($id)
    {
        $model = new AdvertisementModel();
        $ad = $model->find($id);
        if ($ad === null) {
            $this->flash('error', 'Ad not found.');
            return redirect()->to('admin/ads');
        }

        $model->delete((int) $ad['id']);
        (new AuditLogModel())->log($this->adminId(), 'ad_delete', 'ad', (int) $ad['id']);
        $this->flash('success', 'Ad deleted.');
        return redirect()->to('admin/ads');
    }

    private function collect(): array
    {
        $req = $this->request;

        $type = (string) $req->getPost('type');
        if (!array_key_exists($type, AdvertisementModel::TYPES)) {
            $type = 'homepage_banner';
        }

        $image = null;
        $file = $req->getFile('image');
        if ($file !== null && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/ads', $newName);
            $image = 'ads/' . $newName;
        }

        $sellerId    = (int) $req->getPost('seller_id');
        $categoryId  = (int) $req->getPost('category_id');
        $productId   = (int) $req->getPost('product_id');
        $startDate   = trim((string) $req->getPost('start_date'));
        $endDate     = trim((string) $req->getPost('end_date'));

        return [
            'seller_id'   => $sellerId > 0 ? $sellerId : null,
            'title'       => trim((string) $req->getPost('title')),
            'type'        => $type,
            'image'       => $image,
            'link'        => trim((string) $req->getPost('link')) ?: null,
            'category_id' => $categoryId > 0 ? $categoryId : null,
            'product_id'  => $productId > 0 ? $productId : null,
            'start_date'  => $startDate !== '' ? date('Y-m-d H:i:s', strtotime($startDate)) : null,
            'end_date'    => $endDate !== '' ? date('Y-m-d H:i:s', strtotime($endDate)) : null,
            'status'      => (int) (bool) $req->getPost('status'),
        ];
    }
}
