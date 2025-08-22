<?php

namespace App\Http\Middleware;

use App\Models\AuthSession;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;

class JwtAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($authHeader, 7);
        try {
            $payload = app(JwtService::class)->decode($token);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $session = AuthSession::where('access_token', $token)
            ->whereNull('revoked_at')
            ->first();
        if (!$session) {
            return response()->json(['message' => 'Token revoked'], 401);
        }
        if ($session->expires_at && now()->greaterThan($session->expires_at)) {
            return response()->json(['message' => 'Token expired'], 401);
        }

        // Optionally you could attach user to request
        $request->attributes->set('auth_user_id', (int) ($payload->sub ?? 0));
        $request->attributes->set('auth_session_id', $session->id);

        return $next($request);
    }
}
