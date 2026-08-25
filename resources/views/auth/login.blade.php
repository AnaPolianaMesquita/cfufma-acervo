@extends('layouts.guest')

@section('title', 'Entrar - MicoNIBA')

@section('content')

<div class="sm:mx-auto w-full sm:max-w-md">
    <div class="mx-auto w-12 h-12 rounded-xl bg-brand flex items-center justify-center text-white font-bold text-2xl shadow-sm">
        M
    </div>
    <h2 class="mt-6 text-center text-2xl font-bold tracking-tight text-ink">
        Acesse sua conta
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

        <form class="space-y-6" method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <x-input
                label="Endereço de e-mail"
                name="email"
                type="email"
                autocomplete="email"
                required
                value="{{ old('email') }}"
            />

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium text-ink">Senha</label>
                    <a href="{{ route('password.request') }}" class="font-medium text-brand hover:underline text-xs">Esqueceu sua senha?</a>
                </div>
                <input
                    id="password" name="password" type="password" autocomplete="current-password" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm placeholder-slate-400 shadow-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-colors"
                    placeholder="Digite sua senha"
                >
            </div>

            <div class="flex items-center">
                <input id="remember-me" name="remember" type="checkbox" checked
                    class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-brand">
                <label for="remember-me" class="ml-2 block text-xs text-slate-600">Lembrar neste dispositivo</label>
            </div>

            <x-button type="submit" class="w-full">
                Entrar no sistema
            </x-button>
        </form>
    </x-card>

    <p class="mt-6 text-center text-sm text-muted">
        Ainda não tem uma conta?
        <a href="{{ route('register') }}" class="font-medium text-brand hover:underline">Criar conta</a>
    </p>

    <p class="mt-2 text-center text-sm text-muted">
        <a href="{{ route('galeria.index') }}" class="font-medium text-brand hover:underline">Ver acervo público</a> sem fazer login
    </p>
</div>

@endsection
