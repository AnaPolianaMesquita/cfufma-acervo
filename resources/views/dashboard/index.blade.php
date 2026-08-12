@extends('layouts.app')

@section('title', 'Dashboard - Micoteca')
@section('page-title', 'Dashboard')

@section('content')

<div class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-ink">Bem-vinda, Ana</h1>
            <p class="text-muted mt-1">Visão geral do acervo micológico.</p>
        </div>

        <div class="flex items-center gap-3">
            <x-button variant="secondary" :href="route('acervo.index')" size="md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                Consultar acervo
            </x-button>
            <x-button :href="route('importacao.index')" size="md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>
                Importar planilha
            </x-button>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6">
        @foreach ([
            ['label' => 'Isolados', 'value' => $stats['isolados']],
            ['label' => 'Espécies', 'value' => $stats['especies']],
            ['label' => 'Gêneros', 'value' => $stats['generos']],
            ['label' => 'Autores', 'value' => $stats['autores']],
            ['label' => 'Importações', 'value' => $stats['importacoes']],
        ] as $card)
            <x-card>
                <p class="text-muted text-sm">{{ $card['label'] }}</p>
                <h2 class="text-3xl font-bold text-brand-dark mt-3">{{ $card['value'] }}</h2>
            </x-card>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <x-card class="xl:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-semibold text-ink">Isolados por gênero</h3>
                <x-badge variant="success">{{ $porGenero->sum() }} registros</x-badge>
            </div>

            <div class="space-y-3">
                @foreach ($porGenero as $genero => $qtd)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-slate-600">{{ $genero }}</span>
                            <span class="font-medium text-ink">{{ $qtd }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-brand rounded-full" style="width: {{ $porGenero->max() ? ($qtd / $porGenero->max() * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-card>
            <h3 class="font-semibold text-ink mb-1">Última importação</h3>

            @if ($ultimaImportacao)
                <p class="text-xs text-muted mb-5">{{ $ultimaImportacao->created_at->format('d/m/Y \à\s H:i') }}</p>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Arquivo</span>
                        <span class="font-medium text-ink truncate max-w-[180px]" title="{{ $ultimaImportacao->arquivo }}">{{ $ultimaImportacao->arquivo }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Responsável</span>
                        <span class="font-medium text-ink">{{ $ultimaImportacao->usuario?->name ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Importados</span>
                        <span class="font-medium text-ink">{{ $ultimaImportacao->importados }} / {{ $ultimaImportacao->total_linhas }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Status</span>
                        <x-badge :variant="$ultimaImportacao->status === 'concluida' ? 'success' : 'danger'">
                            {{ $ultimaImportacao->status === 'concluida' ? 'Concluída' : 'Erro' }}
                        </x-badge>
                    </div>
                </div>

                <x-button :href="route('importacao.show', $ultimaImportacao->id)" variant="secondary" size="sm" class="w-full mt-6">
                    Ver detalhes
                </x-button>
            @else
                <p class="text-sm text-muted">Nenhuma importação realizada ainda.</p>
                <x-button :href="route('importacao.index')" variant="secondary" size="sm" class="w-full mt-4">
                    Importar planilha
                </x-button>
            @endif
        </x-card>
    </div>

    <x-card :padded="false">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-ink">Últimos isolados cadastrados</h3>
            <a href="{{ route('acervo.index') }}" class="text-sm text-brand hover:underline font-medium">Ver todos</a>
        </div>

        @if ($ultimosRegistros->isEmpty())
            <x-empty-state
                title="Nenhum isolado cadastrado ainda"
                description="Importe uma planilha ou cadastre um isolado manualmente para começar."
            />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-muted border-b border-slate-100">
                            <th class="p-4 font-medium">Código</th>
                            <th class="p-4 font-medium">Espécie</th>
                            <th class="p-4 font-medium">Origem</th>
                            <th class="p-4 font-medium">Autor</th>
                            <th class="p-4 font-medium">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ultimosRegistros as $registro)
                            <tr class="border-t border-slate-50 hover:bg-slate-50/60 transition-colors">
                                <td class="p-4 font-semibold text-ink">
                                    <a href="{{ route('acervo.show', $registro->id) }}" class="hover:text-brand">{{ $registro->codigo }}</a>
                                </td>
                                <td class="p-4 italic text-slate-600">{{ $registro->especieCompleta() }}</td>
                                <td class="p-4 text-slate-600">{{ $registro->origem }}</td>
                                <td class="p-4 text-slate-600">{{ $registro->autor }}</td>
                                <td class="p-4 text-slate-600">{{ $registro->data?->format('d/m/Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

</div>

@endsection
