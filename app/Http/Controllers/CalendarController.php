<?php

namespace App\Http\Controllers;

use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar;
use Google\Client; // Certifique-se de importar o Client
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    protected $calendar;
    protected $client;

    public function __construct(Calendar $calendar)
    {
        $this->calendar = $calendar;

        $this->client = new Client();
        $this->client->setApplicationName('Sua Aplicação');
        $this->client->setScopes(Calendar::CALENDAR_READONLY);
        $token = $this->getAccessToken();
        $this->client->setAccessToken($token);
    }

    public function createEvent()
    {
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

        $this->calendar->events->insert('primary', $event);

        return redirect()->back()->with('message', 'Evento criado com sucesso!');
    }

    public function listEvents()
    {
        $optParams = [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        ];

        // Tenta listar os eventos
        try {
            $events = $this->calendar->events->listEvents('primary', $optParams);
            return response()->json($events->getItems(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar eventos: ' . $e->getMessage()], 500);
        }
    }

    private function getAccessToken()
    {
        // Implemente a lógica para recuperar o token de acesso
        // Isso pode ser uma busca no banco de dados ou em um serviço de cache
        return 'SEU_TOKEN_DE_ACESSO'; // Substitua por sua lógica de recuperação do token
    }
}
