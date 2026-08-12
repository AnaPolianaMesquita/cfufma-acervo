@extends('layouts.app')

@section('title', 'Detalhes da Importação - Micoteca')
@section('page-title', 'Detalhes da importação')

@section('content')

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('importacao.historico') }}" class="text-sm text-muted hover:text-ink flex items-center gap-1 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                Voltar ao histórico
            </a>
            <h1 class="text-2xl font-bold text-ink">{{ $importacao->arquivo }}</h1>
            <p class="text-muted mt-1">
                Enviado por {{ $importacao->usuario?->name ?? 'Usuário não identificado' }}
                em {{ $importacao->created_at->format('d/m/Y \à\s H:i') }}
            </p>
        </div>
        <x-badge :variant="$importacao->status === 'concluida' ? 'success' : 'danger'">
            {{ $importacao->status === 'concluida' ? 'Concluída' : 'Erro no processamento' }}
        </x-badge>
    </div>

    @if ($importacao->status === 'erro')
        <x-alert variant="danger" title="Falha ao processar o arquivo" :dismissible="false">
            {{ $importacao->erro_mensagem ?? 'O arquivo enviado não pôde ser lido. Verifique se o formato é .xls ou .xlsx e tente novamente.' }}
        </x-alert>
    @else
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card>
                <p class="text-xs text-muted">Linhas no arquivo</p>
                <p class="text-2xl font-bold text-ink mt-2">{{ $importacao->total_linhas }}</p>
            </x-card>
            <x-card>
                <p class="text-xs text-muted">Importados</p>
                <p class="text-2xl font-bold text-brand-dark mt-2">{{ $importacao->importados }}</p>
            </x-card>
            <x-card>
                <p class="text-xs text-muted">Inválidos</p>
                <p class="text-2xl font-bold text-red-600 mt-2">{{ $importacao->invalidos }}</p>
            </x-card>
            <x-card>
                <p class="text-xs text-muted">Duplicados</p>
                <p class="text-2xl font-bold text-amber-600 mt-2">{{ $importacao->duplicados }}</p>
            </x-card>
        </div>

        @if (! empty($importacao->colunas_ausentes))
            <x-alert variant="warning" title="Colunas ausentes na planilha" :dismissible="false">
                {{ implode(', ', $importacao->colunas_ausentes) }} não {{ count($importacao->colunas_ausentes) > 1 ? 'foram encontradas' : 'foi encontrada' }} e ficaram em branco nos registros importados.
            </x-alert>
        @endif

        @if (! empty($importacao->linhas_invalidas))
            <x-card :padded="false">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-semibold text-ink">Linhas ignoradas por dados obrigatórios ausentes</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-muted border-b border-slate-100">
                                <th class="p-4 font-medium">Linha na planilha</th>
                                <th class="p-4 font-medium">Campo(s) ausente(s)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($importacao->linhas_invalidas as $linha)
                                <tr class="border-t border-slate-50">
                                    <td class="p-4 font-medium text-ink">{{ $linha['linha'] }}</td>
                                    <td class="p-4 text-red-600">{{ $linha['motivo'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        @endif

        <x-card :padded="false">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-ink">Registros importados</h3>
                <a href="{{ route('acervo.index') }}" class="text-sm text-brand hover:underline font-medium">Ver acervo</a>
            </div>

            @if ($importacao->isolados->isEmpty())
                <x-empty-state title="Nenhum registro foi importado" description="Todas as linhas foram ignoradas por dados ausentes ou duplicidade." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-muted border-b border-slate-100">
                                <th class="p-4 font-medium">Código</th>
                                <th class="p-4 font-medium">Gênero</th>
                                <th class="p-4 font-medium">Espécie</th>
                                <th class="p-4 font-medium">Origem</th>
                                <th class="p-4 font-medium">Autor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($importacao->isolados as $registro)
                                <tr class="border-t border-slate-50">
                                    <td class="p-4 font-medium text-ink">
                                        <a href="{{ route('acervo.show', $registro->id) }}" class="hover:text-brand">{{ $registro->codigo }}</a>
                                    </td>
                                    <td class="p-4 text-slate-600">{{ $registro->genero }}</td>
                                    <td class="p-4 italic text-slate-600">{{ $registro->especie }}</td>
                                    <td class="p-4 text-slate-600">{{ $registro->origem }}</td>
                                    <td class="p-4 text-slate-600">{{ $registro->autor }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    @endif
</div>

@endsection
