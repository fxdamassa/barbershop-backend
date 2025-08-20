<?php

namespace App\Exports;

use App\Models\AgendarCorte;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AgendamentosExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private Request $request) {}

    public function query()
    {
        $qb = AgendarCorte::query()->with(['usuario:id,name', 'servico:id,servico'])
            ->orderByDesc('data_agendamento');

        // mesmos filtros do controller:
        $servicoId = $this->request->query('servico_id');
        $usuarioId = $this->request->query('usuario_id');
        $mes       = $this->request->query('mes');
        $ano       = $this->request->query('ano');
        $q         = $this->request->query('q');

        if ($servicoId) $qb->where('servico_id', $servicoId);
        if ($usuarioId) $qb->where('usuario_id', $usuarioId);
        if ($mes)       $qb->whereMonth('data_agendamento', (int)$mes);
        if ($ano)       $qb->whereYear('data_agendamento', (int)$ano);
        if ($q) {
            $qb->where(function ($inner) use ($q) {
                $inner->whereHas('usuario', fn($u) => $u->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('servico', fn($s) => $s->where('servico', 'like', "%{$q}%"));
            });
        }

        return $qb;
    }

    public function headings(): array
    {
        return ['Usuário', 'Data', 'Hora', 'Serviço'];
    }

    public function map($row): array
    {
        $usuario = $row->usuario?->name ?? '-';
        $servico = $row->servico?->servico ?? '-';
        $data    = $row->data_agendamento ? date('d/m/Y', strtotime($row->data_agendamento)) : '-';
        $hora    = $row->hora_agendamento ?: '-';

        return [$usuario, $data, $hora, $servico];
    }
}
