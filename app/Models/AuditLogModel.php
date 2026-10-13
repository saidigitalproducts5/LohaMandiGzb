<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table         = 'audit_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'action', 'entity', 'entity_id', 'old_value', 'new_value', 'ip'];

    public function log(int $userId, string $action, string $entity, ?int $entityId = null, $old = null, $new = null): bool
    {
        return $this->insert([
            'user_id'   => $userId,
            'action'    => $action,
            'entity'    => $entity,
            'entity_id' => $entityId,
            'old_value' => $old !== null ? json_encode($old) : null,
            'new_value' => $new !== null ? json_encode($new) : null,
            'ip'        => service('request')->getIPAddress(),
        ]);
    }
}