<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cancelamento de Agendamento</title>
</head>
<body>
<p>Olá, <strong>{{ $nome }}</strong>!</p>

<p>Seu agendamento de corte foi <strong>cancelado</strong> com sucesso.</p>

<p><strong>Data:</strong> {{ $data }}<br>
    <strong>Horário:</strong> {{ $hora }}<br>
    <strong>Serviço:</strong> {{ $servico }}</p>

<p>Se precisar, sinta-se à vontade para reagendar a qualquer momento.</p>

<p>Atenciosamente,<br>Equipe BarberApp</p>
</body>
</html>
