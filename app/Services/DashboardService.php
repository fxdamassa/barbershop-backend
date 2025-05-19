<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

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

    public function getEstatisticas(int $ano): array
    {
        return [
            'agendamentosPorMes' => $this->repository->getAgendamentosPorMes($ano),
            'agendamentosDetalhados' => $this->repository->getAgendamentosDetalhados($ano),
        ];
    }

}

