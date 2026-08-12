@extends('layouts.app')

@section('title', 'Cadastrar Isolado - Micoteca')
@section('page-title', 'Cadastrar isolado')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('acervo.index') }}" class="text-sm text-muted hover:text-ink flex items-center gap-1 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            Voltar ao acervo
        </a>
        <h1 class="text-2xl font-bold text-ink">Cadastrar novo isolado</h1>
        <p class="text-muted mt-1">Preencha os dados do registro no acervo.</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('acervo.store') }}" class="space-y-6">
            @csrf

            @include('acervo._form')

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <x-button variant="secondary" :href="route('acervo.index')" type="button">Cancelar</x-button>
                <x-button type="submit">Salvar isolado</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
