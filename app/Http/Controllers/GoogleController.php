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
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/calendar',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile'
            ])
            ->with(['prompt' => 'consent', 'access_type' => 'offline'])
            ->stateless()
            ->redirect();
    }
    public function listGoogleCalendarEvents(Request $request)
    {
        try {
            $user = auth()->user();
            $client = new Google_Client();

            $accessToken = json_decode($user->google_access_token, true);
            $client->setAccessToken($accessToken);

            if ($client->isAccessTokenExpired()) {
                if (isset($accessToken['refresh_token'])) {
                    $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);

                    $user->google_access_token = json_encode($client->getAccessToken());
                    $user->save();
                } else {
                    return response()->json(['error' => 'Token de acesso expirado e sem refresh token disponível.'], 401);
                }
            }

            $calendarService = new Google_Service_Calendar($client);
            $calendarId = 'primary';

            $events = $calendarService->events->listEvents($calendarId);

            return response()->json($events->getItems());

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar eventos: ' . $e->getMessage()], 500);
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $code = $request->input('code');

            if (!$code) {
                return response()->json(['error' => 'Código de autorização não recebido do Google.'], 400);
            }

            $googleUser = Socialite::driver('google')->stateless()->user();

            if (!$googleUser) {
                return response()->json(['error' => 'Usuário Google não encontrado'], 401);
            }

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
            }else {
                $user->google_access_token = json_encode([
                    'access_token' => $googleUser->token,
                    'refresh_token' => $googleUser->refreshToken,
                    'expires_in' => $googleUser->expiresIn,
                ]);
                $user->save();
            }

            Auth::login($user);

            return response()->json(['message' => 'Autenticado com sucesso', 'token' => $googleUser->token]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao autenticar com Google: ' . $e->getMessage()], 500);
        }
    }

}
