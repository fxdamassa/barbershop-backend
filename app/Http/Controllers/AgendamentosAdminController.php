<?php

namespace App\Http\Controllers;

use App\Models\AgendarCorte;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AgendamentosExport;
use Barryvdh\DomPDF\Facade\Pdf;

class AgendamentosAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int)($request->query('per_page', 10));

        $qb = AgendarCorte::with(['usuario:id,name', 'servico:id,servico'])
            ->orderByDesc('data_agendamento');

        $this->applyFilters($qb, $request);

        return response()->json($qb->paginate($perPage));
    }

    public function exportXlsx(Request $request)
    {
        $file = 'agendamentos.xlsx';
        return Excel::download(new AgendamentosExport($request), $file);
    }

    public function exportPdf(Request $request)
    {
        $qb = AgendarCorte::with(['usuario:id,name', 'servico:id,servico'])
            ->orderByDesc('data_agendamento');

        $this->applyFilters($qb, $request);

        $rows = $qb->get();

        $pdf = Pdf::loadView('exports.agendamentos', [
            'rows' => $rows,
            'filtros' => [
                'servico_id' => $request->query('servico_id'),
                'usuario_id' => $request->query('usuario_id'),
                'mes' => $request->query('mes'),
                'ano' => $request->query('ano'),
                'q' => $request->query('q'),
            ],
        ])->setPaper('a4', 'portrait');

        return $pdf->download('agendamentos.pdf');
    }

    private function applyFilters($qb, Request $request): void
    {
        $servicoId = $request->query('servico_id');
        $usuarioId = $request->query('usuario_id');
        $mes       = $request->query('mes'); // 1..12
        $ano       = $request->query('ano'); // 4 dígitos
        $q         = $request->query('q');

        if ($servicoId) {
            $qb->where('servico_id', $servicoId);
        }
        if ($usuarioId) {
            $qb->where('usuario_id', $usuarioId);
        }
        if ($mes) {
            $qb->whereMonth('data_agendamento', (int)$mes);
        }
        if ($ano) {
            $qb->whereYear('data_agendamento', (int)$ano);
        }
        if ($q) {
            $qb->where(function ($inner) use ($q) {
                $inner->whereHas('usuario', fn($u) => $u->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('servico', fn($s) => $s->where('servico', 'like', "%{$q}%"));
            });
        }
    }
}
