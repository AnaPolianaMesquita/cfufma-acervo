@extends('layouts.app')

@section('title', $isolado['codigo'].' - Micoteca')
@section('page-title', 'Detalhes do isolado')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('acervo.index') }}" class="text-sm text-muted hover:text-ink flex items-center gap-1 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                Voltar ao acervo
            </a>
            <h1 class="text-2xl font-bold text-ink">{{ $isolado['codigo'] }}</h1>
            <p class="text-muted mt-1 italic">{{ $isolado->especieCompleta() }}</p>
        </div>

        <div class="flex items-center gap-3">
            <x-button variant="secondary" :href="route('acervo.edit', $isolado['id'])">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                Editar
            </x-button>
            <x-button variant="danger" x-data x-on:click="$dispatch('open-modal', 'delete-{{ $isolado['id'] }}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                Excluir
            </x-button>
        </div>
    </div>

    <x-card>
        <div class="flex flex-col sm:flex-row gap-6 mb-6">
            @if ($isolado->imagemUrl())
                <img src="{{ $isolado->imagemUrl() }}" alt="Foto de {{ $isolado->especieCompleta() }}" class="w-full sm:w-48 h-48 object-cover rounded-xl border border-slate-200">
            @else
                <div class="w-full sm:w-48 h-48 rounded-xl border border-dashed border-slate-200 flex items-center justify-center text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
                </div>
            @endif

            @if ($isolado['descricao'])
                <div class="flex-1">
                    <dt class="text-xs font-medium text-muted uppercase tracking-wide">Descrição</dt>
                    <dd class="mt-1.5 text-sm text-ink leading-relaxed">{{ $isolado['descricao'] }}</dd>
                </div>
            @endif
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
            @foreach ([
                ['label' => 'Código', 'value' => $isolado['codigo']],
                ['label' => 'Gênero', 'value' => $isolado['genero']],
                ['label' => 'Espécie', 'value' => $isolado['especie'], 'italic' => true],
                ['label' => 'Origem', 'value' => $isolado['origem'] ?: '—'],
                ['label' => 'Meio de cultivo', 'value' => $isolado['meio_cultivo'] ?: '—'],
                ['label' => 'Data de coleta', 'value' => $isolado['data']?->format('d/m/Y') ?? '—'],
                ['label' => 'Conservação', 'value' => $isolado['conservacao'] ?: '—'],
                ['label' => 'Local', 'value' => $isolado['local'] ?: '—'],
                ['label' => 'Armazenamento', 'value' => $isolado['armazenamento'] ?: '—'],
                ['label' => 'Autor', 'value' => $isolado['autor'] ?: '—'],
            ] as $field)
                <div>
                    <dt class="text-xs font-medium text-muted uppercase tracking-wide">{{ $field['label'] }}</dt>
                    <dd class="mt-1.5 text-sm font-medium text-ink {{ $field['italic'] ?? false ? 'italic' : '' }}">{{ $field['value'] }}</dd>
                </div>
            @endforeach
        </dl>
    </x-card>
</div>

<x-modal name="delete-{{ $isolado['id'] }}" max-width="sm">
    <div class="p-6">
        <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center text-red-600 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-ink">Excluir isolado {{ $isolado['codigo'] }}?</h3>
        <p class="text-sm text-muted mt-1.5">Essa ação não poderá ser desfeita. O registro será removido permanentemente do acervo.</p>

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'delete-{{ $isolado['id'] }}')">Cancelar</x-button>
            <form method="POST" action="{{ route('acervo.destroy', $isolado['id']) }}">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger-solid">Excluir</x-button>
            </form>
        </div>
    </div>
</x-modal>

@endsection
