<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\Jwt;

/**
 * JwtAuth — validates the Bearer token on protected endpoints and
 * injects the authenticated payload into the request attributes.
 */
class JwtAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = $request->getHeaderLine('Authorization');

        if ($auth && preg_match('/Bearer\s+(\S+)/i', $auth, $m)) {
            $token = $m[1];
        } else {
            $token = $request->getGetPost('token') ?? $request->getHeaderLine('X-Api-Token');
        }

        if (!$token) {
            return service('response')
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['status' => 'error', 'code' => 401, 'message' => 'Authentication required.']);
        }

        $payload = (new Jwt())->decode((string) $token);

        if ($payload === null) {
            return service('response')
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['status' => 'error', 'code' => 401, 'message' => 'Invalid or expired token.']);
        }

        $request->jwt = $payload;

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
