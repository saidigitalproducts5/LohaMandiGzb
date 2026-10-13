<?php

namespace App\Libraries;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

/**
 * Jwt — thin wrapper around firebase/php-jwt.
 *
 * Tokens carry: sub (user id), role (role code), jti (random id),
 * iss/iat/exp. Signing secret and TTL come from .env
 * (jwt.secret / jwt.ttl seconds).
 */
class Jwt
{
    private string $secret;
    private int $ttl;

    public function __construct()
    {
        $this->secret = (string) (getenv('jwt.secret') ?: 'LOHA_MANDI_GZB_SECRET');
        $this->ttl    = (int) (getenv('jwt.ttl') ?: 2592000);
    }

    public function issue(int $userId, int $roleId, string $roleCode): string
    {
        $now = time();

        $payload = [
            'iss'  => base_url(),
            'iat'  => $now,
            'nbf'  => $now,
            'exp'  => $now + $this->ttl,
            'sub'  => (string) $userId,
            'rid'  => $roleId,
            'role' => $roleCode,
            'jti'  => bin2hex(random_bytes(8)),
        ];

        return FirebaseJWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * @return array<string, mixed>|null decoded payload or null when invalid/expired
     */
    public function decode(string $token): ?array
    {
        try {
            $decoded = FirebaseJWT::decode($token, new Key($this->secret, 'HS256'));

            return (array) $decoded;
        } catch (\Throwable $e) {
            log_message('debug', 'JWT decode failed: ' . $e->getMessage());

            return null;
        }
    }
}

