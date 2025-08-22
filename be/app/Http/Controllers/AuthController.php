<?php

namespace App\Http\Controllers;

use App\Models\AuthSession;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $jwt = app(JwtService::class)->issueToken($user->id);

        $session = AuthSession::create([
            'user_id' => $user->id,
            'access_token' => $jwt['token'],
            'jti' => $jwt['jti'],
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->header('User-Agent'),
            'expires_at' => $jwt['expires_at'],
        ]);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'access_token' => $jwt['token'],
            'token_type' => 'Bearer',
            'expires_at' => $jwt['expires_at'],
            'session_id' => $session->id,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $jwt = app(JwtService::class)->issueToken($user->id);

        $session = AuthSession::create([
            'user_id' => $user->id,
            'access_token' => $jwt['token'],
            'jti' => $jwt['jti'],
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->header('User-Agent'),
            'expires_at' => $jwt['expires_at'],
        ]);

        return response()->json([
            'access_token' => $jwt['token'],
            'token_type' => 'Bearer',
            'expires_at' => $jwt['expires_at'],
            'session_id' => $session->id,
        ]);
    }

    public function logout(Request $request)
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = substr($authHeader, 7);
        AuthSession::where('access_token', $token)->whereNull('revoked_at')->update([
            'revoked_at' => now(),
        ]);
        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        $userId = (int) $request->attributes->get('auth_user_id', 0);
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }
}
