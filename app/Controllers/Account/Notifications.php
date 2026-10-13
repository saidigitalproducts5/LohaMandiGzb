<?php

namespace App\Controllers\Account;

use App\Models\NotificationModel;

class Notifications extends AccountBase
{
    public function index()
    {
        $rows = (new NotificationModel())->forUser($this->uid());

        return $this->render('account/notifications', [
            'rows'      => $rows,
            'activeNav' => 'notifications',
        ], 'Notifications');
    }

    public function markRead($id)
    {
        (new NotificationModel())->markRead($this->uid(), (int) $id);

        return redirect()->to('account/notifications');
    }

    public function markAllRead()
    {
        (new NotificationModel())->markAllRead($this->uid());

        return redirect()->to('account/notifications');
    }

    /**
     * Lightweight endpoint for the header bell badge (returns JSON unread count).
     */
    public function unread()
    {
        $this->response->setContentType('application/json');
        $this->response->setBody(json_encode([
            'unread' => (new NotificationModel())->unreadCount($this->uid()),
        ]));

        return $this->response;
    }
}