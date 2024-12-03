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
            ->scopes(['openid', 'email', 'profile'])
            ->with(['prompt' => 'select_account'])
            ->stateless()
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            if(!$googleUser || !$googleUser->email){
                return response()->json(['error' => 'Erro ao autenticar com o Google.'], 401);
            }

            $user = User::firstOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'password' => bcrypt('google-login'), // Senha fictícia (não será usada)
                ]
            );
            Auth::login($user);
            return redirect('http://localhost:8080/dashboard');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao autenticar com Google: ' . $e->getMessage()], 500);
        }
    }
}
