@extends('layouts.public')

@section('title', 'Galeria - Acervo público da MicoNIBA')

@section('content')

<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-ink">Galeria de espécies</h1>
            <p class="text-muted mt-1">Acervo micológico aberto ao público, com fotos e descrições.</p>
        </div>

        <x-button :href="route('register')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14m-7-7h14"/></svg>
            Contribuir com o acervo
        </x-button>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach ([
            ['label' => 'Isolados', 'value' => $stats['isolados']],
            ['label' => 'Espécies', 'value' => $stats['especies']],
            ['label' => 'Gêneros', 'value' => $stats['generos']],
            ['label' => 'Com foto', 'value' => $stats['comFoto']],
        ] as $card)
            <x-card>
                <p class="text-muted text-sm">{{ $card['label'] }}</p>
                <h2 class="text-3xl font-bold text-brand-dark mt-3">{{ $card['value'] }}</h2>
            </x-card>
        @endforeach
    </div>

    <x-card>
        <form method="GET" action="{{ route('galeria.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_auto_auto_auto] gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Pesquisar</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </span>
                    <input type="text" name="busca" value="{{ $filtros['busca'] }}" placeholder="Nome científico ou código..."
                        class="w-full text-sm pl-9 pr-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-colors">
                </div>
            </div>

            <x-select name="genero" label="Gênero" :selected="$filtros['genero']" placeholder="Todos">
                @foreach ($generos as $g)
                    <option value="{{ $g }}" @selected($filtros['genero'] === $g)>{{ $g }}</option>
                @endforeach
            </x-select>

            <x-select name="foto" label="Foto" :selected="$filtros['foto']" placeholder="Todos">
                <option value="com" @selected($filtros['foto'] === 'com')>Com foto</option>
                <option value="sem" @selected($filtros['foto'] === 'sem')>Sem foto</option>
            </x-select>

            <div class="flex gap-2">
                <x-button type="submit" variant="secondary" class="flex-1">Filtrar</x-button>
                @if ($filtros['busca'] || $filtros['genero'] || $filtros['foto'])
                    <x-button :href="route('galeria.index')" variant="ghost">Limpar</x-button>
                @endif
            </div>
        </form>
    </x-card>

    @if ($itens->isEmpty())
        <x-empty-state
            title="Nenhuma espécie encontrada"
            description="Ajuste os filtros ou tente outra pesquisa."
        />
    @else
        <div class="flex items-center justify-between">
            <h3 class="font-semibold text-ink">Espécies do acervo</h3>
            <x-badge variant="success">{{ $meta['total'] }} registros</x-badge>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($itens as $item)
                @php $foto = $item->imagemUrl(); @endphp
                <a href="{{ route('galeria.show', $item['id']) }}" class="group h-full flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md hover:border-brand/30 transition-all">
                    @if ($foto)
                        <div class="aspect-[4/3] bg-slate-50 overflow-hidden">
                            <img src="{{ $foto }}" alt="Foto de {{ $item->especieCompleta() }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                        </div>
                    @endif

                    <div class="p-4 flex-1 flex flex-col {{ $foto ? '' : 'justify-center text-center' }}">
                        <p class="italic font-semibold text-ink group-hover:text-brand transition-colors">{{ $item->especieCompleta() }}</p>
                        <p class="text-xs text-muted mt-0.5">{{ $item['codigo'] }}</p>
                        @if ($foto && $item['descricao'])
                            <p class="text-sm text-slate-600 mt-2 line-clamp-2">{{ $item['descricao'] }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <x-card :padded="false">
            <x-pagination :meta="$meta" />
        </x-card>
    @endif
</div>

@endsection
