<?php

namespace App\Controllers\Admin;

use App\Models\AuditLogModel;
use App\Models\UserModel;

class Users extends AdminBase
{
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users')
            ->select('users.*, roles.name AS role_name')
            ->join('roles', 'roles.id = users.role_id', 'left');

        $q = trim((string) $this->request->getGet('q'));
        $role = (int) ($this->request->getGet('role') ?? 0);
        $status = trim((string) $this->request->getGet('status'));

        if ($q !== '') {
            $builder->groupStart()
                ->like('users.name', $q)->orLike('users.email', $q)
                ->orLike('users.mobile', $q)->groupEnd();
        }
        if ($role > 0) {
            $builder->where('users.role_id', $role);
        }
        if ($status !== '' && $status !== 'all') {
            $builder->where('users.status', $status);
        }

        $total = (clone $builder)->countAllResults(false);
        $limit = 25;
        $page  = max((int) ($this->request->getGet('page') ?? 1), 1);
        $offset = ($page - 1) * $limit;
        $rows = $builder->orderBy('users.id', 'DESC')->limit($limit, $offset)->get()->getResultArray();

        $roles = $db->table('roles')->orderBy('id', 'ASC')->get()->getResultArray();

        return $this->render('admin/users', [
            'users'    => $rows,
            'roles'    => $roles,
            'total'    => $total,
            'page'     => $page,
            'limit'    => $limit,
            'filters'  => ['q' => $q, 'role' => $role, 'status' => $status],
            'activeNav' => 'users',
        ], 'Users');
    }

    public function show($id)
    {
        $db = \Config\Database::connect();
        $user = $db->table('users')
            ->select('users.*, roles.name AS role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.id', (int) $id)->get()->getRowArray();

        if ($user === null) {
            $this->flash('error', 'User not found.');
            return redirect()->to('admin/users');
        }

        $seller = $db->table('sellers')->where('user_id', (int) $id)->get()->getRowArray();
        $stats = [
            'rfqs'    => (int) $db->table('rfqs')->where('buyer_id', (int) $id)->countAllResults(),
            'leads'   => $seller ? (int) $db->table('leads')->where('seller_id', $seller['id'])->countAllResults() : 0,
            'products'=> $seller ? (int) $db->table('products')->where('seller_id', $seller['id'])->countAllResults() : 0,
            'orders'  => (int) $db->table('orders')->where('buyer_id', (int) $id)->countAllResults(),
        ];

        return $this->render('admin/user_view', [
            'user'   => $user,
            'seller' => $seller,
            'stats'  => $stats,
            'activeNav' => 'users',
        ], 'User: ' . $user['name']);
    }

    /**
     * Toggle status: active <-> suspended.
     */
    public function toggleStatus($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        if ($user === null) {
            $this->flash('error', 'User not found.');
        } else {
            $new = $user['status'] === 'active' ? 'suspended' : 'active';
            $userModel->update($user['id'], ['status' => $new]);
            (new AuditLogModel())->log($this->adminId(), 'user_status', 'user', (int) $user['id'], $user['status'], $new);
            $this->flash('success', "User " . ($new === 'active' ? 'activated' : 'suspended') . ".");
        }

        return redirect()->to('admin/users');
    }

    public function delete($id)
    {
        if (!$this->isSuperAdmin()) {
            $this->flash('error', 'Only Super Admin can delete users.');
            return redirect()->to('admin/users');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if ($user === null || $this->adminId() === (int) $user['id']) {
            $this->flash('error', 'Cannot delete this user.');
        } else {
            $userModel->update($user['id'], ['status' => 'deleted']);
            (new AuditLogModel())->log($this->adminId(), 'user_delete', 'user', (int) $user['id']);
            $this->flash('success', 'User marked as deleted.');
        }

        return redirect()->to('admin/users');
    }
}
