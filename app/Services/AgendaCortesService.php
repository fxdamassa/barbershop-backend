<?php

namespace App\Services;

use App\Repositories\AgendaCortesRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AgendaCortesService
{
    protected AgendaCortesRepository $agendaCortesRepository;

    public function __construct(AgendaCortesRepository $agendaCortesRepository)
    {
        $this->agendaCortesRepository = $agendaCortesRepository;
    }

    /**
     * Obtém horários agendados para uma data específica.
     */
    public function getBookedTimes(string $data)
    {
        return $this->agendaCortesRepository->getBookedTimes($data);
    }

    /**
     * Salva um novo agendamento, garantindo que o usuário esteja autenticado e os dados sejam válidos.
     */
    public function salvarAgendamento(array $dados)
    {
        $user = Auth::user();

        if (!$user) {
            throw ValidationException::withMessages(['error' => 'Usuário não autenticado']);
        }

        // Valida os dados
        validator($dados, [
            'data_agendamento' => 'required|date|after_or_equal:today',
            'hora_agendamento' => 'required|date_format:H:i',
        ])->validate();

        $dados['usuario_id'] = $user->id;

        $agendamentoExistente = $this->agendaCortesRepository->existeAgendamento(
            $dados['data_agendamento'],
            $dados['hora_agendamento']
        );

        if($agendamentoExistente)
        {
            throw ValidationException::withMessages([
                'horario' => 'Já existe agendamento para esse horário',
            ]);
        }

        return $this->agendaCortesRepository->saveAgendamento($dados);
    }
}
