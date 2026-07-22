<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Exportação concluída</title>
</head>

<body>

    <h2>Olá, {{ $export->user->name }}!</h2>

    <p>Sua exportação foi concluída com sucesso.</p>

    <p>
        Formato:
        <strong>{{ strtoupper($export->formato) }}</strong>
    </p>

    <p>
        Clique no link abaixo para baixar:
    </p>

    <a href="{{ route('exports.download', ['id' => $export->id]) }}" target="_blank">
    Baixar Exportação
</a>

    <br><br>

    <p>Obrigado por utilizar nossa Agenda de Contatos.</p>

</body>

</html>