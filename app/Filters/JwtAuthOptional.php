<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\Jwt;

/**
 * JwtAuthOptional — decodes a Bearer token when present but does NOT
 * reject anonymous requests. Used on public detail endpoints so they can
 * enrich the payload (e.g. show matched sellers to the RFQ owner).
 */
class JwtAuthOptional implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = $request->getHeaderLine('Authorization');

        if ($auth && preg_match('/Bearer\s+(\S+)/i', $auth, $m)) {
            $token = $m[1];
        } else {
            $token = $request->getGetPost('token') ?? $request->getHeaderLine('X-Api-Token');
        }

        if ($token) {
            $payload = (new Jwt())->decode((string) $token);
            if ($payload !== null) {
                $request->jwt = $payload;
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
