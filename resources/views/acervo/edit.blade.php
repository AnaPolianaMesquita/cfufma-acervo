@extends('layouts.app')

@section('title', 'Editar '.$isolado['codigo'].' - MicoNIBA')
@section('page-title', 'Editar isolado')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('acervo.show', $isolado['id']) }}" class="text-sm text-muted hover:text-ink flex items-center gap-1 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            Voltar aos detalhes
        </a>
        <h1 class="text-2xl font-bold text-ink">Editar {{ $isolado['codigo'] }}</h1>
        <p class="text-muted mt-1">Atualize os dados do registro no acervo.</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('acervo.update', $isolado['id']) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            @include('acervo._form')

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <x-button variant="secondary" :href="route('acervo.show', $isolado['id'])" type="button">Cancelar</x-button>
                <x-button type="submit">Salvar alterações</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
