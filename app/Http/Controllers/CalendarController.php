<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class CalendarController extends Controller
{
    public function createEvent(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        try {
            $accessToken = json_decode($user->google_access_token, true);
            $client = new Google_Client();
            $client->setAccessToken($accessToken);

            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);
                $user->google_access_token = json_encode($client->getAccessToken());
                $user->save();
            }

            $calendarService = new Google_Service_Calendar($client);

            $event = new Google_Service_Calendar_Event([
                'summary' => $request->input('summary'),
                'start' => [
                    'dateTime' => $request->input('start.dateTime'),
                    'timeZone' => $request->input('start.timeZone'),
                ],
                'end' => [
                    'dateTime' => $request->input('end.dateTime'),
                    'timeZone' => $request->input('end.timeZone'),
                ],
            ]);

            $calendarService->events->insert('primary', $event);

            return response()->json(['message' => 'Evento criado com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar evento: ' . $e->getMessage()], 500);
        }
    }
}
