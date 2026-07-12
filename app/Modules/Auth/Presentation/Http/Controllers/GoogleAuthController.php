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
                    // Redirect to phone input page with Google data in session
                    session([
                        'pending_google_registration' => [
                            'name' => $googleUser->name ?? $googleUser->nickname ?? 'Google User',
                            'email' => $googleUser->email,
                            'google_id' => $googleUser->id,
                            'google_token' => $googleUser->token,
                        ]
                    ]);
                    
                    return redirect()->route('auth.google.phone');
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

    public function showPhoneForm()
    {
        if (!session()->has('pending_google_registration')) {
            return redirect()->route('login');
        }
        
        return view('pelanggan.auth.google-phone');
    }

    public function submitPhone(Request $request)
    {
        if (!session()->has('pending_google_registration')) {
            return redirect()->route('login');
        }

        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $pendingData = session('pending_google_registration');
        $phone = $request->input('phone');

        $user = User::create([
            'name' => $pendingData['name'],
            'username' => 'google_' . Str::random(10),
            'slug' => Str::slug('google_' . Str::random(10)),
            'email' => $pendingData['email'],
            'google_id' => $pendingData['google_id'],
            'google_token' => $pendingData['google_token'],
            'password' => Hash::make(Str::random(24)),
            'role' => 'customer',
        ]);
        
        $user->assignRole('customer');

        \App\Models\Pelanggan::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'jenis_kelamin' => 'L',
            'telepon' => $phone,
        ]);

        session()->forget('pending_google_registration');

        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Selamat Datang! Akun Anda berhasil dibuat.');
    }
}
