<?php

namespace App\Controllers\Account;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * AccountBase — shared logic + layout for the buyer/seller web area.
 */
abstract class AccountBase extends Controller
{
    protected $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = service('session');
    }

    protected function uid(): int
    {
        return (int) $this->session->get('uid');
    }

    protected function uRole(): string
    {
        return (string) $this->session->get('u_role');
    }

    protected function uName(): string
    {
        return (string) $this->session->get('u_name');
    }

    /**
     * Render a view inside the account layout.
     */
    protected function render(string $view, array $data = [], ?string $title = null)
    {
        $data['title']     = $title ?? 'My Account';
        $data['uName']     = $this->uName();
        $data['uRole']     = $this->uRole();
        $data['activeNav'] = $data['activeNav'] ?? 'dashboard';
        $data['unreadNotif'] = (int) (new \App\Models\NotificationModel())->unreadCount($this->uid());

        echo view('account/layout', [
            'title'     => $data['title'],
            'uName'     => $data['uName'],
            'uRole'     => $data['uRole'],
            'activeNav' => $data['activeNav'],
            'unreadNotif' => $data['unreadNotif'],
            'content'   => view($view, $data),
        ]);
    }

    protected function flash(string $type, string $message)
    {
        $this->session->setFlashdata('flash_' . $type, $message);
    }

    /**
     * Seller subscription gate — called right after a seller logs in.
     * Returns a redirect to /account/subscribe when the current user is a
     * seller with no paid, unexpired plan; null otherwise (do nothing).
     */
    protected function sellerGate(): ?ResponseInterface
    {
        if ($this->uRole() !== 'seller') {
            return null;
        }

        $seller = (new \App\Models\SellerModel())->findByUser($this->uid());
        if ($seller !== null && (new \App\Models\SellerMembershipModel())->isSubscribed((int) $seller['id'])) {
            return null;
        }

        $this->flash('error', 'Your seller subscription is not active. Please subscribe to continue.');

        return redirect()->to('account/subscribe');
    }
}
