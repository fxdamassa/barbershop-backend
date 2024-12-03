<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Calendar;

class GoogleController extends Controller
{
    // Redirecionar para Google OAuth
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
            ])
            ->with(['prompt' => 'consent', 'access_type' => 'offline'])
            ->stateless()
            ->redirect();
    }

    // Callback após autenticação Google
    public function handleGoogleCallback(Request $request)
    {
        try {
            $code = $request->input('code');
            if (!$code) {
                return response()->json(['error' => 'Código de autorização não recebido do Google.'], 400);
            }

            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'google_access_token' => json_encode([
                        'access_token' => $googleUser->token,
                        'refresh_token' => $googleUser->refreshToken,
                        'expires_in' => $googleUser->expiresIn,
                    ]),
                ]);
            } else {
                $user->google_access_token = json_encode([
                    'access_token' => $googleUser->token,
                    'refresh_token' => $googleUser->refreshToken,
                    'expires_in' => $googleUser->expiresIn,
                ]);
                $user->save();
            }

            Auth::login($user);
            return redirect('http://localhost:8080/dashboard');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao autenticar com Google: ' . $e->getMessage()], 500);
        }
    }
}
