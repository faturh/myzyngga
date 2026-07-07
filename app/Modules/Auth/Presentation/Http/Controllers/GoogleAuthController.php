<?php

namespace App\Modules\Auth\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Obtain the user information from Google and login/register them.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Check if user with this google_id already exists
            $user = User::where('google_id', $googleUser->id)->first();
            
            if (!$user) {
                // Alternatively, check if a user with the same email exists
                $user = User::where('email', $googleUser->email)->first();
                
                if ($user) {
                    // Update user's google_id and token
                    $user->update([
                        'google_id' => $googleUser->id,
                        'google_token' => $googleUser->token,
                    ]);
                } else {
                    // Create a new user
                    $user = User::create([
                        'name' => $googleUser->name ?? $googleUser->nickname ?? 'Google User',
                        'username' => 'google_' . Str::random(10),
                        'slug' => Str::slug('google_' . Str::random(10)),
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'google_token' => $googleUser->token,
                        'password' => Hash::make(Str::random(24)), // Random dummy password
                        'role' => 'customer',
                    ]);
                    
                    $user->assignRole('customer');
                }
            } else {
                // Update Google token
                $user->update([
                    'google_token' => $googleUser->token,
                ]);
            }
            
            // Log in the user in the Web session guard if session is available
            if (request()->hasSession()) {
                auth()->login($user);
                request()->session()->regenerate();
            }

            if (request()->expectsJson()) {
                // Generate token via Sanctum
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login via Google berhasil',
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'data' => $user
                ], 200);
            }

            return redirect()->route('dashboard')->with('success', 'Selamat Datang! Login via Google berhasil.');
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Autentikasi Google gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
