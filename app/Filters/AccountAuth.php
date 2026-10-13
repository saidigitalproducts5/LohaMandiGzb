<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AccountAuth — protects the /account (buyer & seller) web area.
 * Requires an active session with a non-admin role (buyer, seller, transporter).
 */
class AccountAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = service('session');

        if (!$session->has('uid') || !$session->has('u_role')) {
            return redirect()->to('login')
                ->with('error', 'Please sign in to access your account.');
        }

        $allowed = ['buyer', 'seller', 'transporter'];

        if (!in_array($session->get('u_role'), $allowed, true)) {
            return redirect()->to('/')
                ->with('error', 'You are not authorized to view this area.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
