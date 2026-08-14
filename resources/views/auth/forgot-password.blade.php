@extends('layouts.guest')

@section('title', 'Recuperar senha - Micoteca')

@section('content')

<div class="sm:mx-auto w-full sm:max-w-md">
    <div class="mx-auto w-12 h-12 rounded-xl bg-brand flex items-center justify-center text-white font-bold text-2xl shadow-sm">
        M
    </div>
    <h2 class="mt-6 text-center text-2xl font-bold tracking-tight text-ink">
        Recuperar senha
    </h2>
    <p class="mt-2 text-center text-sm text-muted">
        Informe seu e-mail e enviaremos um link para redefinir sua senha.
    </p>
</div>

<div class="mt-8 sm:mx-auto w-full sm:max-w-md">
    <x-card class="sm:px-10">
        @if (session('success'))
            <x-alert variant="success" class="mb-6" :dismissible="false">
                {{ session('success') }}
            </x-alert>
        @endif

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

        <form class="space-y-6" method="POST" action="{{ route('password.email') }}">
            @csrf

            <x-input
                label="Endereço de e-mail"
                name="email"
                type="email"
                autocomplete="email"
                required
                value="{{ old('email') }}"
            />

            <x-button type="submit" class="w-full">
                Enviar link de recuperação
            </x-button>
        </form>
    </x-card>

    <p class="mt-6 text-center text-sm text-muted">
        Lembrou a senha?
        <a href="{{ route('login') }}" class="font-medium text-brand hover:underline">Voltar ao login</a>
    </p>
</div>

@endsection
