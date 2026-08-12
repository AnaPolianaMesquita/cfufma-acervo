@extends('layouts.guest')

@section('title', 'Criar conta - Micoteca')

@section('content')

<div class="sm:mx-auto w-full sm:max-w-md">
    <div class="mx-auto w-12 h-12 rounded-xl bg-brand flex items-center justify-center text-white font-bold text-2xl shadow-sm">
        M
    </div>
    <h2 class="mt-6 text-center text-2xl font-bold tracking-tight text-ink">
        Crie sua conta
    </h2>
    <p class="mt-2 text-center text-sm text-muted">
        Gerenciamento Digital de Acervo Micológico
    </p>
</div>

<div class="mt-8 sm:mx-auto w-full sm:max-w-md">
    <x-card class="sm:px-10">
        @if ($errors->any())
            <x-alert variant="danger" class="mb-6" :dismissible="false">
                {{ $errors->first() }}
            </x-alert>
        @endif

        <form class="space-y-6" method="POST" action="{{ route('register.store') }}">
            @csrf

            <x-input
                label="Nome completo"
                name="nome"
                type="text"
                autocomplete="name"
                required
                value="{{ old('nome') }}"
            />

            <x-input
                label="Endereço de e-mail"
                name="email"
                type="email"
                autocomplete="email"
                required
                value="{{ old('email') }}"
            />

            <div>
                <label for="senha" class="block text-sm font-medium text-ink mb-1.5">Senha</label>
                <input
                    id="senha" name="senha" type="password" autocomplete="new-password" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm placeholder-slate-400 shadow-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-colors"
                    placeholder="Mínimo de 8 caracteres"
                >
            </div>

            <div>
                <label for="senha_confirmation" class="block text-sm font-medium text-ink mb-1.5">Confirmar senha</label>
                <input
                    id="senha_confirmation" name="senha_confirmation" type="password" autocomplete="new-password" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm placeholder-slate-400 shadow-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-colors"
                    placeholder="Digite a senha novamente"
                >
            </div>

            <x-button type="submit" class="w-full">
                Criar conta
            </x-button>
        </form>
    </x-card>

    <p class="mt-6 text-center text-sm text-muted">
        Já tem uma conta?
        <a href="{{ route('login') }}" class="font-medium text-brand hover:underline">Entrar</a>
    </p>
</div>

@endsection
