<?php

namespace App\Services;

use App\Models\NotificationModel;

/**
 * NotificationService — in-app notification creation used across modules.
 */
class NotificationService
{
    public function __construct(private NotificationModel $model)
    {
    }

    public function send(int $userId, string $title, string $body, string $type = 'in_app', ?string $refType = null, ?int $refId = null): int
    {
        return (int) $this->model->insert([
            'user_id'  => $userId,
            'title'    => $title,
            'body'     => $body,
            'type'     => $type,
            'ref_type' => $refType,
            'ref_id'   => $refId,
            'is_read'  => 0,
        ]);
    }

    public function sendBulk(array $userIds, string $title, string $body, string $type = 'in_app', ?string $refType = null, ?int $refId = null): void
    {
        foreach ($userIds as $userId) {
            $this->send((int) $userId, $title, $body, $type, $refType, $refId);
        }
    }
}