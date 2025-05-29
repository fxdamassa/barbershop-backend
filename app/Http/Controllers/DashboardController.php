<?php

namespace App\Http\Controllers;

use App\Models\AgendarCorte;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function estatisticas(Request $request)
    {
        $ano = $request->query('ano', date('Y'));
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $userId = auth()->id();

        return response()->json(
            $this->dashboardService->getEstatisticas($userId, $ano, $perPage, $page)
        );
    }

    public function todosAgendamentos(): JsonResponse
    {
        $agendamentos = AgendarCorte::with(['usuario', 'servico'])
            ->orderByDesc('data_agendamento')
            ->get()
            ->map(function ($item) {
                return [
                    'usuario' => $item->usuario->name ?? 'Desconhecido',
                    'data_agendamento' => $item->data_agendamento,
                    'hora_agendamento' => $item->hora_agendamento,
                    'servico' => $item->servico->servico ?? 'N/A'
                ];
            });

        return response()->json($agendamentos);
    }
}
