<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'role_id', 'name', 'email', 'mobile', 'password', 'google_id',
        'avatar', 'email_verified', 'mobile_verified', 'status', 'last_login_at',
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $pwd = $data['data']['password'];
            if ($pwd !== null && $pwd !== '' && !password_verify($pwd, $pwd)) {
                $data['data']['password'] = password_hash($pwd, PASSWORD_DEFAULT);
            }
        }

        return $data;
    }

    public function roleCode(int $roleId): string
    {
        $role = db_connect()->table('roles')->select('code')->where('id', $roleId)->get()->getRow();

        return $role->code ?? 'buyer';
    }

    public function findByMobile(string $mobile): ?array
    {
        return $this->where('mobile', $mobile)->first();
    }

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function findByGoogleId(string $googleId): ?array
    {
        return $this->where('google_id', $googleId)->first();
    }
}
