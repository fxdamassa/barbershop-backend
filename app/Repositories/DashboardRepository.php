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

    public function getAgendamentosDetalhados($ano)
    {
        return AgendarCorte::with('servico')
            ->whereYear('data_agendamento', $ano)
            ->where('usuario_id', auth()->id())
            ->orderByDesc('data_agendamento')
            ->orderByDesc('hora_agendamento')
            ->get()
            ->map(function ($agendamento) {
                return [
                    'data_agendamento' => $agendamento->data_agendamento,
                    'hora_agendamento' => $agendamento->hora_agendamento,
                    'servico' => $agendamento->servico->servico ?? 'Não informado',
                ];
            });
    }

    public function getAgendamentosDetalhadosPorAno(int $userId, int $ano, int $perPage = 10)
    {
        return AgendarCorte::where('usuario_id', $userId)
            ->whereYear('data_agendamento', $ano)
            ->orderBy('data_agendamento')
            ->orderBy('hora_agendamento')
            ->paginate($perPage);
    }
}

