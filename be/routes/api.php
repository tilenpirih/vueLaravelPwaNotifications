<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'server is running']);
});

use App\Http\Controllers\PushController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
Route::get('/push/public-key', [PushController::class, 'publicKey']);
Route::post('/push/subscribe', [PushController::class, 'subscribe']);
Route::post('/push/notify', [PushController::class, 'notify']);

Route::post('/auth/register', function(Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string',
    ]);
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);
    Auth::login($user);
    return response()->json(['user' => $user]);
});


Route::post('/auth/login', function(Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    if (!Auth::attempt($data)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
    $user = Auth::user();
    return response()->json(['user' => $user]);
});

// Private endpoint to get authenticated user info
Route::middleware('auth')->get('/auth/me', function(Request $request) {
    return response()->json(['user' => $request->user()]);
});
