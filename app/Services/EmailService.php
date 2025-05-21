<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class EmailService
{
    public function enviarConfirmacaoAgendamento($nome, $email, $data, $hora, $servico)
    {
        $dados = [
            'nome' => $nome,
            'data' => Carbon::parse($data)->format('d/m/Y'),
            'hora' => Carbon::parse($hora)->format('H:i'),
            'servico' => $servico,
        ];

        Mail::send('emails.confirmacao_agendamento', $dados, function ($message) use ($email, $nome) {
           $message->to($email, $nome)->subject('Confirmação de Agendamento de Corte');
        });
    }
}
