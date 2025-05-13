<?php

namespace App\Http\Controllers;

use App\Models\AgendarCorte;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function salvarAgendamento(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            \Log::error('Usuário não autenticado ao tentar salvar o agendamento.');
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        \Log::info('Dados recebidos para agendamento:', $request->all());

        $validatedData = $request->validate([
            'data_agendamento' => 'required|date|after_or_equal:today',
            'hora_agendamento' => 'required|date_format:H:i',
            'observacao' => 'nullable|string|max:255',
        ]);

        try {
            $agendamento = new AgendarCorte();
            $agendamento->usuario_id = $user->id;
            $agendamento->data_agendamento = $validatedData['data_agendamento'];
            $agendamento->hora_agendamento = $validatedData['hora_agendamento'];
            $agendamento->save();

            \Log::info('Agendamento criado com sucesso.', ['agendamento' => $agendamento]);

            return response()->json(['success' => true, 'message' => 'Agendamento salvo com sucesso!'], 201);
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar agendamento:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro ao salvar agendamento: ' . $e->getMessage()], 500);
        }
    }

    public function getBookedTimes($data)
    {
        $bookedTimes = AgendarCorte::where('data_agendamento', $data)->pluck('hora_agendamento');
        return response()->json(['bookedTimes' => $bookedTimes] );
    }

}
