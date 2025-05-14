<?php

namespace App\Repositories;

use App\Models\AgendarCorte;
use Illuminate\Support\Collection;

class AgendaCortesRepository
{
    /**
     * Obtém os horários agendados para uma determinada data.
     */
    public function getBookedTimes(string $data): Collection
    {
        return AgendarCorte::where('data_agendamento', $data)
            ->pluck('hora_agendamento')
            ->map(function ($hora) {
                return substr($hora, 0, 5);
            });
    }

    /**
     * Salva um novo agendamento no banco de dados.
     */
    public function saveAgendamento(array $dados): AgendarCorte
    {
        return AgendarCorte::create($dados);
    }

    public function existeAgendamento($data, $hora) : bool
    {
        return AgendarCorte::where('data_agendamento', $data)
            ->where('hora_agendamento', $hora)
            ->exists();
    }
}
