<?php

namespace App\Services;

use App\Repositories\AgendaCortesRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Services\EmailService;

class AgendaCortesService
{
    protected AgendaCortesRepository $agendaCortesRepository;
    protected EmailService $emailService;

    public function __construct(AgendaCortesRepository $agendaCortesRepository, EmailService $emailService)
    {
        $this->agendaCortesRepository = $agendaCortesRepository;
        $this->emailService = $emailService;
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
            'servico_id' => 'required|exists:servicos,id',
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

        $agendamento =  $this->agendaCortesRepository->saveAgendamento($dados);

        $servico = \App\Models\Servico::find($dados['servico_id']);
        $nomeServico = $servico ? $servico->servico : 'Serviço não identificado';

        $this->emailService->enviarConfirmacaoAgendamento(
            $user->name,
            $user->email,
            $dados['data_agendamento'],
            $dados['hora_agendamento'],
            $nomeServico,
        );

        return $agendamento;
    }

    public function listarServicos()
    {
        return $this->agendaCortesRepository->listarServicos();
    }

}
