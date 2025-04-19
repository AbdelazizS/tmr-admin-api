<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    // Login with rate limiting and strict validation
    public function login(Request $request)
    {
        $this->checkRequestRateLimit($request);

        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => [
                    'required',
                    'string',
                    'min:12',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/'
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        if (!Auth::attempt($validated)) {
            RateLimiter::hit($this->throttleKey($request));
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $request->user()->createToken(
            name: 'api-token',
            abilities: ['property:manage'],
            expiresAt: now()->addHours(8)
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $this->userResponse($request->user()),
            'expires_at' => $token->accessToken->expires_at->toIso8601String(),
            'token_type' => 'bearer'
        ]);
    }

    // Secure logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    // Get authenticated user
    public function user(Request $request)
    {
        return response()->json($this->userResponse($request->user()));
    }

    // --- Helper Methods ---
    protected function checkRequestRateLimit(Request $request): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            abort(429, 'Too many login attempts. Please try again in '.RateLimiter::availableIn($this->throttleKey($request)).' seconds.');
        }
    }

    protected function throttleKey(Request $request): string
    {
        return 'login-attempt:'.sha1($request->ip().$request->header('User-Agent'));
    }

    protected function userResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at->toIso8601String()
        ];
    }
}