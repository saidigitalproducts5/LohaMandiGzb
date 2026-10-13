<?php

namespace App\Models;

use CodeIgniter\Model;

class OtpModel extends Model
{
    protected $table         = 'otps';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['mobile', 'email', 'otp', 'purpose', 'expires_at', 'used', 'attempts'];

    public function validFor($mobile = null, $email = null, string $otp, string $purpose): ?array
    {
        $builder = $this->where('otp', $otp)
            ->where('purpose', $purpose)
            ->where('used', 0)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->orderBy('id', 'DESC');

        if ($mobile !== null) {
            $builder->where('mobile', $mobile);
        }
        if ($email !== null) {
            $builder->where('email', $email);
        }

        return $builder->first();
    }
}