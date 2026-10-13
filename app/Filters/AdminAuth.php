<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AdminAuth — protects the /admin web panel. Requires an active admin
 * session with an admin-level role (super_admin, admin, moderator).
 */
class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = service('session');

        if (!$session->has('admin_id') || !$session->has('admin_role')) {
            return redirect()->to('admin/login')
                ->with('error', 'Please sign in to access the admin panel.');
        }

        if (!in_array($session->get('admin_role'), ['super_admin', 'admin', 'moderator'], true)) {
            return redirect()->to('admin/login')
                ->with('error', 'You do not have admin access.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}