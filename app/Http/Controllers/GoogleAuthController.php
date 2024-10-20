<?php
namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class GoogleAuthController extends Controller
{
    // Redirecionar para o Google
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    // Callback do Google
    public function handleGoogleCallback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Verificar se o usuário já existe
        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            // Criar novo usuário se ele não existir
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'google_token' => $googleUser->token,
            ]);
        }

        // Autenticar o usuário no Laravel
        Auth::login($user);

        // Redirecionar para o dashboard ou onde desejar
        return redirect('/dashboard');
    }
}

