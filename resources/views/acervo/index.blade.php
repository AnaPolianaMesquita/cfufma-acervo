@extends('layouts.app')

@section('title', 'Acervo - Micoteca')
@section('page-title', 'Acervo')

@section('content')

<div
    x-data="{ selected: [] }"
    class="space-y-6"
>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-ink">Acervo de isolados</h1>
            <p class="text-muted mt-1">{{ $meta['total'] }} registros no total.</p>
        </div>
        <x-button :href="route('acervo.create')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14m-7-7h14"/></svg>
            Cadastrar isolado
        </x-button>
    </div>

    <!-- Filtros -->
    <x-card>
        <form method="GET" action="{{ route('acervo.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-ink mb-1.5">Pesquisar</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </span>
                    <input type="text" name="busca" value="{{ $filtros['busca'] }}" placeholder="Código, espécie ou autor..."
                        class="w-full text-sm pl-9 pr-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-colors">
                </div>
            </div>

            <x-select name="genero" label="Gênero" :selected="$filtros['genero']" placeholder="Todos">
                @foreach ($generos as $g)
                    <option value="{{ $g }}" @selected($filtros['genero'] === $g)>{{ $g }}</option>
                @endforeach
            </x-select>

            <x-select name="conservacao" label="Conservação" :selected="$filtros['conservacao']" placeholder="Todas">
                @foreach ($conservacoes as $c)
                    <option value="{{ $c }}" @selected($filtros['conservacao'] === $c)>{{ $c }}</option>
                @endforeach
            </x-select>

            <div class="flex gap-2">
                <x-button type="submit" variant="secondary" class="flex-1">Filtrar</x-button>
                @if ($filtros['busca'] || $filtros['genero'] || $filtros['conservacao'])
                    <x-button :href="route('acervo.index')" variant="ghost">Limpar</x-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Barra de seleção em massa -->
    <div x-show="selected.length > 0" x-cloak class="flex items-center justify-between bg-ink text-white rounded-xl px-5 py-3">
        <p class="text-sm"><span x-text="selected.length"></span> selecionado(s)</p>
        <div class="flex items-center gap-2">
            <button type="button" x-on:click="$dispatch('open-modal', 'confirm-bulk-delete')" class="text-sm font-medium text-red-300 hover:text-red-200">Excluir selecionados</button>
            <div class="h-4 w-px bg-white/20"></div>
            <button type="button" x-on:click="selected = []" class="text-sm text-slate-300 hover:text-white">Cancelar</button>
        </div>
    </div>

    <x-card :padded="false">
        @if ($itens->isEmpty())
            <x-empty-state
                :title="$filtros['busca'] || $filtros['genero'] || $filtros['conservacao'] ? 'Nenhum resultado encontrado' : 'Nenhum isolado cadastrado'"
                :description="$filtros['busca'] || $filtros['genero'] || $filtros['conservacao'] ? 'Ajuste os filtros ou tente outra pesquisa.' : 'Cadastre um isolado ou importe uma planilha para começar.'"
            >
                <x-slot:action>
                    <x-button :href="route('acervo.create')">Cadastrar isolado</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-muted border-b border-slate-100">
                            <th class="p-4 w-10">
                                <input type="checkbox"
                                    x-on:change="selected = $event.target.checked ? [{{ $itens->pluck('id')->implode(',') }}] : []"
                                    :checked="selected.length === {{ $itens->count() }}"
                                    class="rounded border-slate-300 text-brand focus:ring-brand">
                            </th>
                            @foreach ([
                                ['key' => 'codigo', 'label' => 'Código'],
                                ['key' => 'genero', 'label' => 'Gênero'],
                                ['key' => 'especie', 'label' => 'Espécie'],
                                ['key' => 'origem', 'label' => 'Origem'],
                                ['key' => 'meio_cultivo', 'label' => 'Meio de cultivo'],
                                ['key' => 'data', 'label' => 'Data'],
                                ['key' => 'conservacao', 'label' => 'Conservação'],
                                ['key' => 'local', 'label' => 'Local'],
                                ['key' => 'armazenamento', 'label' => 'Armazenamento'],
                                ['key' => 'autor', 'label' => 'Autor'],
                            ] as $col)
                                <th class="p-4 font-medium whitespace-nowrap">
                                    <a href="{{ request()->fullUrlWithQuery(['ordenar' => $col['key'], 'direcao' => ($filtros['ordenar'] === $col['key'] && $filtros['direcao'] === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-1 hover:text-ink">
                                        {{ $col['label'] }}
                                        @if ($filtros['ordenar'] === $col['key'])
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                @if ($filtros['direcao'] === 'asc')
                                                    <path d="m18 15-6-6-6 6"/>
                                                @else
                                                    <path d="m6 9 6 6 6-6"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                            @endforeach
                            <th class="p-4 font-medium text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($itens as $item)
                            <tr class="border-t border-slate-50 hover:bg-slate-50/60 transition-colors">
                                <td class="p-4">
                                    <input type="checkbox" value="{{ $item['id'] }}" x-model="selected" class="rounded border-slate-300 text-brand focus:ring-brand">
                                </td>
                                <td class="p-4 font-semibold text-ink whitespace-nowrap">
                                    <a href="{{ route('acervo.show', $item['id']) }}" class="hover:text-brand">{{ $item['codigo'] }}</a>
                                </td>
                                <td class="p-4 text-slate-600 whitespace-nowrap">{{ $item['genero'] }}</td>
                                <td class="p-4 italic text-slate-600 whitespace-nowrap">{{ $item['especie'] }}</td>
                                <td class="p-4 text-slate-600 whitespace-nowrap">{{ $item['origem'] }}</td>
                                <td class="p-4 text-slate-600 whitespace-nowrap">{{ $item['meio_cultivo'] }}</td>
                                <td class="p-4 text-slate-600 whitespace-nowrap">{{ $item['data']?->format('d/m/Y') ?? '—' }}</td>
                                <td class="p-4 text-slate-600 whitespace-nowrap">{{ $item['conservacao'] }}</td>
                                <td class="p-4 text-slate-600 whitespace-nowrap">{{ $item['local'] }}</td>
                                <td class="p-4 text-slate-600 whitespace-nowrap">{{ $item['armazenamento'] }}</td>
                                <td class="p-4 text-slate-600 whitespace-nowrap">{{ $item['autor'] }}</td>
                                <td class="p-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('acervo.show', $item['id']) }}" title="Visualizar" class="p-1.5 text-slate-400 hover:text-brand hover:bg-brand-light rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        <a href="{{ route('acervo.edit', $item['id']) }}" title="Editar" class="p-1.5 text-slate-400 hover:text-brand hover:bg-brand-light rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                        </a>
                                        <button type="button" title="Excluir" x-on:click="$dispatch('open-modal', 'delete-{{ $item['id'] }}')" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-3 border-t border-slate-100">
                <form method="GET" action="{{ route('acervo.index') }}" class="flex items-center gap-2 text-sm text-muted">
                    @foreach (request()->except(['por_pagina', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    Exibir
                    <select name="por_pagina" onchange="this.form.submit()" class="rounded-lg border border-slate-300 text-sm py-1.5 pl-2 pr-7 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand">
                        @foreach ([10, 25, 50, 100] as $opt)
                            <option value="{{ $opt }}" @selected($filtros['porPagina'] === $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                    por página
                </form>
            </div>

            <x-pagination :meta="$meta" />
        @endif
    </x-card>

    <x-modal name="confirm-bulk-delete" max-width="sm">
        <form method="POST" action="{{ route('acervo.destroyEmMassa') }}" class="p-6">
            @csrf
            @method('DELETE')
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>

            <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center text-red-600 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-ink">Excluir <span x-text="selected.length"></span> registro(s) selecionado(s)?</h3>
            <p class="text-sm text-muted mt-1.5">Essa ação não poderá ser desfeita.</p>

            <div class="flex items-center justify-end gap-3 mt-6">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'confirm-bulk-delete')">Cancelar</x-button>
                <x-button type="submit" variant="danger-solid">Excluir</x-button>
            </div>
        </form>
    </x-modal>
</div>

@foreach ($itens as $item)
    <x-modal name="delete-{{ $item['id'] }}" max-width="sm">
        <div class="p-6">
            <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center text-red-600 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-ink">Excluir isolado {{ $item['codigo'] }}?</h3>
            <p class="text-sm text-muted mt-1.5">Essa ação não poderá ser desfeita. O registro será removido permanentemente do acervo.</p>

            <div class="flex items-center justify-end gap-3 mt-6">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'delete-{{ $item['id'] }}')">Cancelar</x-button>
                <form method="POST" action="{{ route('acervo.destroy', $item['id']) }}">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger-solid">Excluir</x-button>
                </form>
            </div>
        </div>
    </x-modal>
@endforeach

@endsection
