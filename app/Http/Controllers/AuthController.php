<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Http\Requests\AuthRequest\LoginRequest;
use App\Http\Requests\AuthRequest\RegisterRequest;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validated = $request->only('username', 'password');

        $remember = $validated['remember'] ?? false;

        // Remove `remember` from the credentials before passing to attempt
        $credentials = collect($validated)->except('remember')->toArray();

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return response()->json([
                'message' => 'Login successful.',
                'user' => Auth::user(),
            ]);
        }

        throw ValidationException::withMessages([
            'username' => ['The provided credentials do not match our records.'],
        ]);
    }

    public function register(RegisterRequest $request)
    {
       $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        Auth::login($user); // <- Logs in the user and issues session

        return response()->json([
            'message' => 'Registration successful.',
            'user' => $user,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);/* ->withCookie(
        cookie()->forget(config('session.cookie'))
        ) */
        /* Log::info('Logout triggered', [
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
        ]); */
    }
}
