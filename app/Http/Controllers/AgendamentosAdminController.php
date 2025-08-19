<?php

namespace App\Http\Controllers;


use App\Models\AgendarCorte;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class AgendamentosAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int)($request->query('per_page', 10));
        $q = $request->query('q');
        $data = AgendarCorte::with(['usuario:id,name', 'servico:id,servico'])
            ->when($q, function ($qb) use ($q) {
                $qb->whereHas('usuario', fn($u) => $u->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('servico', fn($s) => $s->where('servico', 'like', "%{$q}%"));
            })
            ->orderByDesc('data_agendamento')
            ->paginate($perPage);
        return response()->json($data);
    }


    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'data_agendamento' => 'required|date',
            'hora_agendamento' => 'required',
            'servico_id' => 'required|exists:servicos,id',
        ]);


// (Opcional) validar conflito de horário
        $existeConflito = AgendarCorte::where('data_agendamento', $data['data_agendamento'])
            ->where('hora_agendamento', $data['hora_agendamento'])
            ->exists();
        if ($existeConflito) {
            return response()->json(['error' => 'Horário selecionado conflita com outro agendamento'], 422);
        }


        $ag = AgendarCorte::create($data);
        return response()->json($ag, 201);
    }


    public function cancelar(int $id): JsonResponse
    {
        $ag = AgendarCorte::findOrFail($id);
        $ag->delete();
        return response()->json(['message' => 'Agendamento cancelado']);
    }
}
