<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table         = 'notifications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'title', 'body', 'type', 'ref_type', 'ref_id', 'is_read',
    ];

    /**
     * Create an in-app notification (idempotent per ref per type).
     */
    public function push(int $userId, string $title, ?string $body = null, string $refType = '', ?int $refId = null): int
    {
        return (int) $this->insert([
            'user_id'  => $userId,
            'title'    => $title,
            'body'     => $body,
            'type'     => 'in_app',
            'ref_type' => $refType,
            'ref_id'   => $refId,
            'is_read'  => 0,
        ]);
    }

    public function forUser(int $userId, int $limit = 30): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function unreadCount(int $userId): int
    {
        return (int) $this->where('user_id', $userId)->where('is_read', 0)->countAllResults();
    }

    public function markAllRead(int $userId): void
    {
        $this->where('user_id', $userId)->set('is_read', 1)->update();
    }

    public function markRead(int $userId, int $id): void
    {
        $this->where('user_id', $userId)->where('id', $id)->set('is_read', 1)->update();
    }
}