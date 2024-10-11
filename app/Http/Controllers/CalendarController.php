<?php


namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    protected $client;
    protected $calendar;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/credentials.json'));
        $this->client->setAccessType('offline');
        $this->client->setIncludeGrantedScopes(true);
        $this->client->addScope(Calendar::CALENDAR);
    }

    public function createEvent()
    {
        if (!$this->initializeCalendarService()) {
            return response()->json(['error' => 'Google Calendar service is not initialized.'], 500);
        }

        $event = new Event([
            'summary' => 'Corte de Cabelo',
            'start' => new EventDateTime([
                'dateTime' => '2024-09-28T10:00:00',
                'timeZone' => 'America/Sao_Paulo',
            ]),
            'end' => new EventDateTime([
                'dateTime' => '2024-09-28T11:00:00',
                'timeZone' => 'America/Sao_Paulo',
            ]),
        ]);

        try {
            $this->calendar->events->insert('primary', $event);
            return response()->json(['message' => 'Evento criado com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar evento: ' . $e->getMessage()], 500);
        }
    }

    public function listEvents()
    {
        if (!$this->initializeCalendarService()) {
            return response()->json(['error' => 'Google Calendar service is not initialized.'], 500);
        }

        $optParams = [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        ];

        try {
            $events = $this->calendar->events->listEvents('primary', $optParams);
            if (!$events || !method_exists($events, 'getItems')) {
                return response()->json(['error' => 'No events found or unable to retrieve items from the Google Calendar API.'], 404);
            }
            return response()->json($events->getItems(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar eventos: ' . $e->getMessage()], 500);
        }
    }

    private function initializeCalendarService()
    {
        $token = $this->getAccessToken();

        if ($token) {
            $this->client->setAccessToken($token);

            if ($this->client->isAccessTokenExpired()) {
                Log::info('Access token expired, fetching new token.');
                $refreshToken = $this->client->getRefreshToken();

                if ($refreshToken) {
                    $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                    $this->saveAccessToken($newToken);
                } else {
                    return false;
                }
            }

            $this->calendar = new Calendar($this->client);
            return true;
        } else {
            Log::error('Access token is invalid or not available.');
            return false;
        }
    }

    private function getAccessToken()
    {
        $user = auth()->user();
        return $user ? json_decode($user->google_access_token, true) : null;
    }

    // Salva o novo token
    private function saveAccessToken($token)
    {
        $user = auth()->user();
        if ($user) {
            $user->google_access_token = json_encode($token);
            $user->save();
        }
    }
}

