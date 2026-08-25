@extends('layouts.app')

@section('title', 'Histórico de Importações - MicoNIBA')
@section('page-title', 'Histórico de importações')

@section('content')

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-ink">Histórico de importações</h1>
            <p class="text-muted mt-1">Todas as planilhas enviadas ao sistema.</p>
        </div>
        <x-button :href="route('importacao.index')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14m-7-7h14"/></svg>
            Nova importação
        </x-button>
    </div>

    @if ($importacoes->isEmpty())
        <x-card>
            <x-empty-state
                title="Nenhuma importação registrada"
                description="Quando você enviar uma planilha, o histórico aparecerá aqui."
            />
        </x-card>
    @else
        <x-card :padded="false">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-muted border-b border-slate-100">
                            <th class="p-4 font-medium">Arquivo</th>
                            <th class="p-4 font-medium">Responsável</th>
                            <th class="p-4 font-medium">Data</th>
                            <th class="p-4 font-medium">Registros</th>
                            <th class="p-4 font-medium">Status</th>
                            <th class="p-4 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($importacoes as $importacao)
                            <tr class="border-t border-slate-50 hover:bg-slate-50/60 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-brand-light flex items-center justify-center text-brand-dark shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-ink truncate max-w-[220px]">{{ $importacao->arquivo }}</p>
                                            <p class="text-xs text-muted">{{ number_format($importacao->tamanho / 1024, 0) }} KB</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-slate-600">{{ $importacao->usuario?->name ?? '—' }}</td>
                                <td class="p-4 text-slate-600">{{ $importacao->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-4 text-slate-600">
                                    @if ($importacao->status === 'concluida')
                                        {{ $importacao->importados }} / {{ $importacao->total_linhas }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="p-4">
                                    <x-badge :variant="$importacao->status === 'concluida' ? 'success' : 'danger'">
                                        {{ $importacao->status === 'concluida' ? 'Concluída' : 'Erro' }}
                                    </x-badge>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('importacao.show', $importacao->id) }}" class="text-brand hover:underline font-medium text-sm">Detalhes</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
</div>

@endsection
