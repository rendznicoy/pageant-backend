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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function forgotPassword(Request $request)
    {
        $data = $request->only('username', 'email');

        $user = null;
        if (!empty($data['username'])) {
            $user = User::where('username', $data['username'])->first();
        } elseif (!empty($data['email'])) {
            $user = User::where('email', $data['email'])->first();
        }

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Generate unique token
        $token = Str::random(60);
        
        // Store the token with expiration time
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now()
        ]);
        
        // Generate reset URL
        $resetUrl = url(config('app.url') . '/reset-password/' . $token);
        
        // Send email to user
        Mail::send('emails.password_reset', [
            'user' => $user,
            'resetUrl' => $resetUrl,
            'token' => $token,
        ], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('VSU Elearning Notification: Visayas State University E-Learning Environment: Password reset request');
        });

        return response()->json([
            'message' => 'User found. Reset instructions have been sent to your email.',
            'email' => $user->email, // This will be used by the frontend to display a message
        ]);
    }
}
