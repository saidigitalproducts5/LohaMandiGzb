<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * AdminBase — shared logic + layout for the web admin panel.
 */
abstract class AdminBase extends Controller
{
    protected $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = service('session');
    }

    protected function adminId(): int
    {
        return (int) $this->session->get('admin_id');
    }

    protected function adminRole(): string
    {
        return (string) $this->session->get('admin_role');
    }

    protected function isSuperAdmin(): bool
    {
        return $this->adminRole() === 'super_admin';
    }

    /**
     * Render a view inside the admin layout.
     */
    protected function render(string $view, array $data = [], ?string $title = null)
    {
        $data['title'] = $title ?? 'Admin';
        $data['adminName'] = $this->session->get('admin_name');
        $data['adminRole'] = $this->adminRole();
        $data['activeNav'] = $data['activeNav'] ?? 'dashboard';

        echo view('admin/layout', [
            'title'      => $data['title'],
            'adminName'  => $data['adminName'],
            'adminRole'  => $data['adminRole'],
            'activeNav'  => $data['activeNav'],
            'content'    => view($view, $data),
        ]);
    }

    protected function flash(string $type, string $message)
    {
        $this->session->setFlashdata('flash_' . $type, $message);
    }
}
