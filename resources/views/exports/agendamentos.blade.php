<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Agendamentos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; }
        .muted { color: #666; font-size: 11px; }
    </style>
</head>
<body>
<h2>Agendamentos</h2>
<div class="muted">
    @php
        $labels = [];
        if(!empty($filtros['ano'])) $labels[] = 'Ano: '.$filtros['ano'];
        if(!empty($filtros['mes'])) $labels[] = 'Mês: '.$filtros['mes'];
        if(!empty($filtros['usuario_id'])) $labels[] = 'Cliente ID: '.$filtros['usuario_id'];
        if(!empty($filtros['servico_id'])) $labels[] = 'Serviço ID: '.$filtros['servico_id'];
        if(!empty($filtros['q'])) $labels[] = 'Busca: '.$filtros['q'];
    @endphp
    @if(count($labels)) <strong>Filtros:</strong> {{ implode(' | ', $labels) }} @endif
</div>

<table>
    <thead>
    <tr>
        <th>Usuário</th>
        <th>Data</th>
        <th>Hora</th>
        <th>Serviço</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $r)
        <tr>
            <td>{{ optional($r->usuario)->name ?? '—' }}</td>
            <td>{{ $r->data_agendamento ? \Carbon\Carbon::parse($r->data_agendamento)->format('d/m/Y') : '—' }}</td>
            <td>{{ $r->hora_agendamento ?? '—' }}</td>
            <td>{{ optional($r->servico)->servico ?? '—' }}</td>
        </tr>
    @endforeach
    @if(!count($rows))
        <tr><td colspan="4" style="text-align:center;color:#666;">Sem dados</td></tr>
    @endif
    </tbody>
</table>
</body>
</html>
