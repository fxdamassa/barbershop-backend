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
    // Redirecionar para o Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/calendar',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile'
            ])
            ->with(['prompt' => 'consent'])
            ->stateless()
            ->redirect();
    }
    public function listGoogleCalendarEvents(Request $request)
    {
        try {
            // Pegue o usuário autenticado
            $user = auth()->user(); // Supondo que você tenha autenticação ativa

            // Inicialize o Google Client
            $client = new Google_Client();

            // Carregue o token de acesso salvo no banco de dados
            $accessToken = json_decode($user->google_access_token, true); // Pegue o token salvo no banco

            // Defina o token de acesso para o cliente do Google
            $client->setAccessToken($accessToken);

            // Verifique se o token é válido ou se precisa ser renovado
            if ($client->isAccessTokenExpired()) {
                // Renove o token de acesso se estiver expirado
                if (isset($accessToken['refresh_token'])) {
                    $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);

                    // Atualize o novo token no banco de dados
                    $user->google_access_token = json_encode($client->getAccessToken());
                    $user->save();
                } else {
                    return response()->json(['error' => 'Token de acesso expirado e sem refresh token disponível.'], 401);
                }
            }

            // Inicialize o serviço do Google Calendar com o cliente
            $calendarService = new Google_Service_Calendar($client);

            // Defina o ID do calendário
            $calendarId = 'primary';

            // Liste os eventos do calendário
            $events = $calendarService->events->listEvents($calendarId);

            // Retorne os eventos
            return response()->json($events->getItems());

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar eventos: ' . $e->getMessage()], 500);
        }
    }

    // Callback do Google
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Verifica se o Google retornou o código de autorização
            $code = $request->input('code');

            if (!$code) {
                return response()->json(['error' => 'Código de autorização não recebido do Google.'], 400);
            }

            // Autentica o usuário via Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Verifica se obteve o usuário corretamente
            if (!$googleUser) {
                return response()->json(['error' => 'Usuário Google não encontrado'], 401);
            }

            // Procura o usuário no banco de dados baseado no e-mail do Google
            $user = User::where('email', $googleUser->email)->first();

            // Se não encontrar o usuário, cria um novo
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'google_access_token' => json_encode([
                        'access_token' => $googleUser->token,
                        'refresh_token' => $googleUser->refreshToken, // Pode ser nulo se o login for repetido
                        'expires_in' => $googleUser->expiresIn, // Duração do token
                    ]),
                ]);
            }else {
                // Se o usuário já existe, atualiza o token do Google
                $user->google_access_token = json_encode([
                    'access_token' => $googleUser->token,
                    'refresh_token' => $googleUser->refreshToken, // Pode ser nulo se o login for repetido
                    'expires_in' => $googleUser->expiresIn, // Duração do token
                ]);
                $user->save();
            }

            // Autentica o usuário no sistema
            Auth::login($user);

            // Retorna uma resposta de sucesso com o token do usuário
            return response()->json(['message' => 'Autenticado com sucesso', 'token' => $googleUser->token]);
        } catch (\Exception $e) {
            // Retorna um erro se algo der errado no processo de autenticação
            return response()->json(['error' => 'Falha ao autenticar com Google: ' . $e->getMessage()], 500);
        }
        Log::info('Google auth code: ' . $code);
    }

}
