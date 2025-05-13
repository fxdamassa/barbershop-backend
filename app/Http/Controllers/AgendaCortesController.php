<?php

namespace App\Http\Controllers;

use App\Services\AgendaCortesService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\AgendarCorte;

class AgendaCortesController extends Controller
{
    protected AgendaCortesService $agendaCortesService;

    public function __construct(AgendaCortesService $agendaCortesService)
    {
        $this->agendaCortesService = $agendaCortesService;
    }

    /**
     * Obtém os horários já agendados para uma data específica.
     */
    public function getBookedTimes($data): JsonResponse
    {
        $bookedTimes = AgendarCorte::where('data_agendamento', $data)
            ->pluck('hora_agendamento')
            ->toArray(); // Retorna apenas os horários ocupados como array

        return response()->json(['bookedTimes' => $bookedTimes]);
    }

    /**
     * Salva um novo agendamento.
     */
    public function salvarAgendamento(Request $request): JsonResponse
    {
        try {
            $agendamento = $this->agendaCortesService->salvarAgendamento($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Agendamento salvo com sucesso!',
                'agendamento' => $agendamento
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao salvar agendamento: ' . $e->getMessage()
            ], 500);
        }
    }
}
