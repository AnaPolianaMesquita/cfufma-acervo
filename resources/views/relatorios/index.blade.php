@extends('layouts.app')

@section('title', 'Relatórios - MicoNIBA')
@section('page-title', 'Relatórios')

@section('content')

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-ink">Relatórios do acervo</h1>
            <p class="text-muted mt-1">{{ $total }} registros correspondem aos filtros aplicados.</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="secondary" :href="route('relatorios.exportar.excel', request()->query())">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 13h8"/><path d="M8 17h8"/></svg>
                Exportar Excel
            </x-button>
            <x-button variant="secondary" :href="route('relatorios.exportar.pdf', request()->query())">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M9 15h1a1 1 0 1 0 0-2H9v4"/><path d="M13 17v-4h1.5a1.5 1.5 0 0 1 0 3H13"/></svg>
                Exportar PDF
            </x-button>
        </div>
    </div>

    <!-- Filtros -->
    <x-card>
        <form method="GET" action="{{ route('relatorios.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-input type="date" name="data_inicio" label="Data inicial" value="{{ $filtros['data_inicio'] }}" />
                <x-input type="date" name="data_fim" label="Data final" value="{{ $filtros['data_fim'] }}" />

                <x-select name="genero" label="Gênero" :selected="$filtros['genero']" placeholder="Todos">
                    @foreach ($generos as $g)
                        <option value="{{ $g }}" @selected($filtros['genero'] === $g)>{{ $g }}</option>
                    @endforeach
                </x-select>

                <x-select name="especie" label="Espécie" :selected="$filtros['especie']" placeholder="Todas">
                    @foreach ($especies as $e)
                        <option value="{{ $e }}" @selected($filtros['especie'] === $e)>{{ $e }}</option>
                    @endforeach
                </x-select>

                <x-select name="autor" label="Autor" :selected="$filtros['autor']" placeholder="Todos">
                    @foreach ($autores as $a)
                        <option value="{{ $a }}" @selected($filtros['autor'] === $a)>{{ $a }}</option>
                    @endforeach
                </x-select>

                <x-select name="local" label="Local" :selected="$filtros['local']" placeholder="Todos">
                    @foreach ($locais as $l)
                        <option value="{{ $l }}" @selected($filtros['local'] === $l)>{{ $l }}</option>
                    @endforeach
                </x-select>

                <x-select name="conservacao" label="Conservação" :selected="$filtros['conservacao']" placeholder="Todas">
                    @foreach ($conservacoes as $c)
                        <option value="{{ $c }}" @selected($filtros['conservacao'] === $c)>{{ $c }}</option>
                    @endforeach
                </x-select>

                <x-select name="meio_cultivo" label="Meio de cultivo" :selected="$filtros['meio_cultivo']" placeholder="Todos">
                    @foreach ($meiosCultivo as $m)
                        <option value="{{ $m }}" @selected($filtros['meio_cultivo'] === $m)>{{ $m }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                @if (collect($filtros)->filter()->isNotEmpty())
                    <x-button :href="route('relatorios.index')" variant="ghost" type="button">Limpar filtros</x-button>
                @endif
                <x-button type="submit">Aplicar filtros</x-button>
            </div>
        </form>
    </x-card>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card>
            <p class="text-xs text-muted">Total de isolados</p>
            <p class="text-2xl font-bold text-ink mt-2">{{ $total }}</p>
        </x-card>
        <x-card>
            <p class="text-xs text-muted">Gêneros distintos</p>
            <p class="text-2xl font-bold text-ink mt-2">{{ $porGenero->count() }}</p>
        </x-card>
        <x-card>
            <p class="text-xs text-muted">Autores envolvidos</p>
            <p class="text-2xl font-bold text-ink mt-2">{{ $porAutor->count() }}</p>
        </x-card>
        <x-card>
            <p class="text-xs text-muted">Métodos de conservação</p>
            <p class="text-2xl font-bold text-ink mt-2">{{ $porConservacao->count() }}</p>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card>
            <h3 class="font-semibold text-ink mb-5">Distribuição por gênero</h3>
            @if ($porGenero->isEmpty())
                <x-empty-state title="Sem dados para exibir" description="Ajuste os filtros para visualizar o gráfico." />
            @else
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
            @endif
        </x-card>

        <x-card>
            <h3 class="font-semibold text-ink mb-5">Distribuição por conservação</h3>
            @if ($porConservacao->isEmpty())
                <x-empty-state title="Sem dados para exibir" description="Ajuste os filtros para visualizar o gráfico." />
            @else
                <div class="space-y-3">
                    @foreach ($porConservacao as $conservacao => $qtd)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-slate-600">{{ $conservacao }}</span>
                                <span class="font-medium text-ink">{{ $qtd }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-brand-dark rounded-full" style="width: {{ $porConservacao->max() ? ($qtd / $porConservacao->max() * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card>
            <h3 class="font-semibold text-ink mb-5">Distribuição por meio de cultivo</h3>
            @if ($porMeioCultivo->isEmpty())
                <x-empty-state title="Sem dados para exibir" description="Ajuste os filtros para visualizar o gráfico." />
            @else
                <div class="space-y-3">
                    @foreach ($porMeioCultivo as $meio => $qtd)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-slate-600">{{ $meio }}</span>
                                <span class="font-medium text-ink">{{ $qtd }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-emerald-400 rounded-full" style="width: {{ $porMeioCultivo->max() ? ($qtd / $porMeioCultivo->max() * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card>
            <h3 class="font-semibold text-ink mb-5">Registros por autor</h3>
            @if ($porAutor->isEmpty())
                <x-empty-state title="Sem dados para exibir" description="Ajuste os filtros para visualizar o gráfico." />
            @else
                <div class="space-y-3">
                    @foreach ($porAutor as $autor => $qtd)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-slate-600">{{ $autor }}</span>
                                <span class="font-medium text-ink">{{ $qtd }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-slate-400 rounded-full" style="width: {{ $porAutor->max() ? ($qtd / $porAutor->max() * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>
</div>

@endsection
