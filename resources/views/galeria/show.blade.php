@extends('layouts.public')

@section('title', $isolado->especieCompleta().' - Galeria da Micoteca')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('galeria.index') }}" class="text-sm text-muted hover:text-ink flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
        Voltar à galeria
    </a>

    <x-card :padded="false">
        <div class="aspect-[16/9] bg-slate-50 rounded-t-2xl overflow-hidden">
            @if ($isolado->imagemUrl())
                <img src="{{ $isolado->imagemUrl() }}" alt="Foto de {{ $isolado->especieCompleta() }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
                </div>
            @endif
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <div>
                <p class="text-xs font-medium text-muted uppercase tracking-wide">{{ $isolado['codigo'] }}</p>
                <h1 class="text-2xl font-bold text-ink italic mt-1">{{ $isolado->especieCompleta() }}</h1>
            </div>

            <div>
                <h2 class="text-xs font-medium text-muted uppercase tracking-wide mb-1.5">Descrição</h2>
                @if ($isolado['descricao'])
                    <p class="text-sm text-ink leading-relaxed whitespace-pre-line">{{ $isolado['descricao'] }}</p>
                @else
                    <p class="text-sm text-muted italic">Descrição ainda não cadastrada para esta espécie.</p>
                @endif
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 pt-4 border-t border-slate-100">
                <div>
                    <dt class="text-xs font-medium text-muted uppercase tracking-wide">Origem</dt>
                    <dd class="mt-1 text-sm text-ink">{{ $isolado['origem'] ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-muted uppercase tracking-wide">Data de coleta</dt>
                    <dd class="mt-1 text-sm text-ink">{{ $isolado['data']?->format('d/m/Y') ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </x-card>

    <x-card class="text-center">
        <p class="text-sm text-ink font-medium">Quer ajudar a ampliar o acervo?</p>
        <p class="text-sm text-muted mt-1">Crie uma conta para cadastrar novos isolados com fotos e descrições.</p>
        <x-button class="mt-4" :href="route('register')">Criar conta</x-button>
    </x-card>
</div>

@endsection
