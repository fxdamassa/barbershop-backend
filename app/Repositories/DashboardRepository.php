<?php

namespace App\Repositories;

use App\Models\AgendarCorte;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class DashboardRepository
{
    public function getAgendamentosPorMes(int $ano): Collection
    {
        return AgendarCorte::selectRaw('MONTH(data_agendamento) as mes, COUNT(*) as total')
            ->whereYear('data_agendamento', $ano)
            ->groupByRaw('MONTH(data_agendamento)')
            ->orderByRaw('MONTH(data_agendamento)')
            ->get();
    }

    public function getAgendamentosDetalhados(int $ano): Collection
    {
        return AgendarCorte::whereYear('data_agendamento', $ano)
            ->orderBy('data_agendamento', 'desc')
            ->get(['data_agendamento', 'hora_agendamento']);
    }

    public function getAgendamentosDetalhadosPorAno(int $userId, int $ano, int $perPage = 10)
    {
        return AgendarCorte::where('usuario_id', $userId)
            ->whereYear('data_agendamento', $ano)
            ->orderBy('data_agendamento', 'desc')
            ->paginate($perPage);
    }
}

