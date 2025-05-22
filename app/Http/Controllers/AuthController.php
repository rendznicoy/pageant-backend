<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Judge;
use App\Http\Requests\AuthRequest\LoginRequest;
use App\Http\Requests\AuthRequest\RegisterRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

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
        $request->validate([
            'username' => 'required_without:email|string',
            'email' => 'required_without:username|email'
        ]);

        $user = User::when($request->username, function($query) use ($request) {
                    return $query->where('username', $request->username);
                })
                ->when($request->email, function($query) use ($request) {
                    return $query->where('email', $request->email);
                })
                ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 422);
        }

        // Generate unique token
        $token = Str::random(60);
        
        // Store the token with expiration time
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token, 
                'created_at' => now()
            ]
        );
        
        // Generate reset URL
        $resetUrl = url(config('app.frontend_url') . '/reset-password?token=' . $token);
        
        // Send email to user
        Mail::send('emails.password_reset', [
            'user' => $user,
            'resetUrl' => $resetUrl
        ], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset Request');
        });

        return response()->json([
            'message' => 'Reset link sent to your email',
            'email' => $user->email
        ]);
    }

    public function judgeLogin(Request $request)
    {
        $request->validate([
            'pin_code' => 'required|string',
        ]);

        $judge = Judge::where('pin_code', $request->pin_code)->first();
        if (!$judge) {
            Log::error("Invalid PIN code", ['pin_code' => $request->pin_code]);
            return response()->json(['message' => 'Invalid PIN code.'], 422);
        }

        $user = $judge->user;
        if (!$user) {
            Log::error("No user associated with judge", ['judge_id' => $judge->judge_id]);
            return response()->json(['message' => 'No user account found for this judge.'], 422);
        }

        // Revoke existing tokens
        $user->tokens()->delete();

        // Create new token without session login
        $token = $user->createToken('judge-token')->plainTextToken;

        Log::info("Judge login successful", [
            'user_id' => $user->user_id,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
            'judge_id' => $judge->judge_id,
            'token' => Str::limit($token, 20),
        ]);

        return response()->json([
            'message' => 'Judge login successful',
            'user' => [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
                'judge_id' => $judge->judge_id,
            ],
            'token' => $token
        ]);
    }

    public function redirectToGoogle(Request $request)
    {
        try {
            // Try/catch to catch any configuration issues
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            // Return a user-friendly error
            return redirect()->route('/login/admin')->with('error', 'Unable to connect to Google. Please try again later.');
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = strtolower($googleUser->getEmail());

            // Block non-VSU emails
            if (!str_ends_with($email, '@vsu.edu.ph')) {
                return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/login/admin?error=only_vsu_emails');
            }

            // Determine role
            $role = $email === '21-1-01027@vsu.edu.ph' ? 'admin' : 'tabulator';
            
            // Get Google profile photo URL
            $profilePhotoUrl = $googleUser->getAvatar();
            
            // Some Google avatars include size parameters - let's make sure we get a decent size
            if ($profilePhotoUrl && strpos($profilePhotoUrl, 'googleusercontent.com') !== false) {
                // Remove any existing size parameters
                $profilePhotoUrl = preg_replace('/=s\d+-c/', '', $profilePhotoUrl);
                // Add our own size parameter for a larger image
                $profilePhotoUrl .= '=s400-c';
            }

            // Check if user already exists
            $existingUser = User::where('email', $email)->first();

            if ($existingUser) {
                // Update Google ID, role, profile photo and mark email verified
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'role' => $role,
                    'email_verified_at' => now(),
                    'profile_photo' => $profilePhotoUrl,
                ]);
                Auth::login($existingUser);
            } else {
                // Create new user
                $newUser = User::create([
                    'first_name' => explode(' ', $googleUser->name)[0] ?? '',
                    'last_name' => explode(' ', $googleUser->name)[1] ?? '',
                    'username' => explode('@', $email)[0],
                    'email' => $email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(12)),
                    'role' => $role,
                    'email_verified_at' => now(),
                    'profile_photo' => $profilePhotoUrl,
                ]);
                Auth::login($newUser);
            }

            // Use a hardcoded frontend URL temporarily to test if env() is the issue
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            
            // Construct redirect path
            $redirectPath = $role === 'admin' ? '/admin/dashboard' : '/tabulator/dashboard';
            
            // Return explicit redirect
            return redirect($frontendUrl . $redirectPath);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Google authentication error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Redirect with hardcoded URL to avoid env() issues
            return redirect('http://localhost:5173/login/admin?error=google_auth_failed');
        }
    }
}
