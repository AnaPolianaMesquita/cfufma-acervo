<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório do acervo - MicoNIBA</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; color: #1e293b; font-size: 11px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        p.subtitulo { color: #64748b; margin: 0 0 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: 600; }
        tr:nth-child(even) td { background-color: #f8fafc; }
        .rodape { margin-top: 16px; color: #94a3b8; font-size: 9px; }
    </style>
</head>
<body>
    <h1>Relatório do acervo micológico</h1>
    <p class="subtitulo">{{ $total }} registro(s) correspondem aos filtros aplicados &middot; Gerado em {{ now()->format('d/m/Y \à\s H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Gênero</th>
                <th>Espécie</th>
                <th>Origem</th>
                <th>Meio de cultivo</th>
                <th>Data de coleta</th>
                <th>Conservação</th>
                <th>Local</th>
                <th>Armazenamento</th>
                <th>Autor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registros as $registro)
                <tr>
                    <td>{{ $registro->codigo }}</td>
                    <td>{{ $registro->genero }}</td>
                    <td>{{ $registro->especie }}</td>
                    <td>{{ $registro->origem ?: '-' }}</td>
                    <td>{{ $registro->meio_cultivo ?: '-' }}</td>
                    <td>{{ $registro->data?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $registro->conservacao ?: '-' }}</td>
                    <td>{{ $registro->local ?: '-' }}</td>
                    <td>{{ $registro->armazenamento ?: '-' }}</td>
                    <td>{{ $registro->autor ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">Nenhum registro encontrado para os filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="rodape">MicoNIBA &middot; Gestão de Acervo Micológico</p>
</body>
</html>
