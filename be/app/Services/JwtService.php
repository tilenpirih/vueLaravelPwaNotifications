<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class JwtService
{
    public function issueToken(int $userId, ?int $ttlMinutes = null): array
    {
        $now = time();
        $ttl = ($ttlMinutes ?? (int) Config::get('jwt.ttl', 60)) * 60; // seconds
        $exp = $now + $ttl;
        $jti = (string) Str::uuid();

        $payload = [
            'iss' => Config::get('app.url'),
            'sub' => $userId,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
            'jti' => $jti,
        ];

        $secret = Config::get('jwt.secret');
        $alg = Config::get('jwt.algo', 'HS256');

        $token = JWT::encode($payload, $secret, $alg);

        return [
            'token' => $token,
            'jti' => $jti,
            'expires_at' => date('c', $exp),
        ];
    }

    public function decode(string $token): object
    {
        $secret = Config::get('jwt.secret');
        $alg = Config::get('jwt.algo', 'HS256');
        return JWT::decode($token, new Key($secret, $alg));
    }
}
