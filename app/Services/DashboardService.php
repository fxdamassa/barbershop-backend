<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    protected DashboardRepository $repository;

    public function __construct(DashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAgendamentosPorMes($usuarioId)
    {
        return $this->repository->countAgendamentosPorMes($usuarioId);
    }

    public function getAgendamentosDetalhados($usuarioId)
    {
        return $this->repository->getAgendamentosDetalhados($usuarioId);
    }

    public function getEstatisticas(int $userId, int $ano, int $perPage, int $page)
    {
        $agendamentos = $this->repository->getAgendamentosDetalhadosPorAno($userId, $ano, $perPage, $page);

        return [
            'agendamentosPorMes' => $this->repository->getAgendamentosPorMes($ano),
            'agendamentosDetalhados' => $agendamentos->items(),
            'pagination' => [
                'current_page' => $agendamentos->currentPage(),
                'last_page' => $agendamentos->lastPage(),
                'per_page' => $agendamentos->perPage(),
                'total' => $agendamentos->total(),
            ],
        ];
    }


    public function getEstatisticasPaginadas(int $ano, int $perPage = 10)
    {
        $userId = Auth::id();

        return $this->repository->getAgendamentosDetalhadosPorAno($userId, $ano, $perPage);
    }

}

