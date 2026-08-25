@extends('layouts.guest')

@section('title', 'Redefinir senha - MicoNIBA')

@section('content')

<div class="sm:mx-auto w-full sm:max-w-md">
    <div class="mx-auto w-12 h-12 rounded-xl bg-brand flex items-center justify-center text-white font-bold text-2xl shadow-sm">
        M
    </div>
    <h2 class="mt-6 text-center text-2xl font-bold tracking-tight text-ink">
        Redefinir senha
    </h2>
    <p class="mt-2 text-center text-sm text-muted">
        Escolha uma nova senha para sua conta.
    </p>
</div>

<div class="mt-8 sm:mx-auto w-full sm:max-w-md">
    <x-card class="sm:px-10">
        @if (session('error'))
            <x-alert variant="danger" class="mb-6" :dismissible="false">
                {{ session('error') }}
            </x-alert>
        @endif

        @if ($errors->any())
            <x-alert variant="danger" class="mb-6" :dismissible="false">
                {{ $errors->first() }}
            </x-alert>
        @endif

        <form class="space-y-6" method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <x-input
                label="Endereço de e-mail"
                name="email"
                type="email"
                autocomplete="email"
                required
                value="{{ old('email', $email) }}"
            />

            <x-input
                label="Nova senha"
                name="senha"
                type="password"
                autocomplete="new-password"
                required
                hint="Mínimo de 8 caracteres."
            />

            <x-input
                label="Confirmar nova senha"
                name="senha_confirmation"
                type="password"
                autocomplete="new-password"
                required
            />

            <x-button type="submit" class="w-full">
                Redefinir senha
            </x-button>
        </form>
    </x-card>
</div>

@endsection
