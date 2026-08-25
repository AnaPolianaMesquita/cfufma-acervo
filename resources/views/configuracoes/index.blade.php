@extends('layouts.app')

@section('title', 'Configurações - MicoNIBA')
@section('page-title', 'Configurações')

@section('content')

<div x-data="{ tab: 'perfil' }" class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-ink">Configurações</h1>
            <p class="text-muted mt-1">Gerencie seu perfil e a segurança da conta.</p>
        </div>
        @if ($usuario->perfil === 'Administrador')
            <x-button variant="secondary" :href="route('configuracoes.usuarios')">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Gerenciar usuários
            </x-button>
        @endif
    </div>

    <div class="flex items-center gap-1 border-b border-slate-200">
        <button type="button" x-on:click="tab = 'perfil'" class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors" :class="tab === 'perfil' ? 'border-brand text-brand-dark' : 'border-transparent text-muted hover:text-ink'">
            Perfil
        </button>
        <button type="button" x-on:click="tab = 'senha'" class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors" :class="tab === 'senha' ? 'border-brand text-brand-dark' : 'border-transparent text-muted hover:text-ink'">
            Alterar senha
        </button>
    </div>

    <div x-show="tab === 'perfil'" x-cloak>
        <x-card>
            <form method="POST" action="{{ route('configuracoes.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-brand-light flex items-center justify-center font-bold text-brand-dark text-xl">
                        AS
                    </div>
                    <div>
                        <p class="text-sm font-medium text-ink">{{ $usuario->name }}</p>
                        <p class="text-xs text-muted">{{ $usuario->perfil }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-input label="Nome completo" name="nome" required value="{{ old('nome', $usuario->name) }}" :error="$errors->first('nome')" />
                    <x-input label="E-mail" name="email" type="email" required value="{{ old('email', $usuario->email) }}" :error="$errors->first('email')" />
                </div>

                <div class="flex items-center justify-end pt-2 border-t border-slate-100">
                    <x-button type="submit">Salvar alterações</x-button>
                </div>
            </form>
        </x-card>
    </div>

    <div x-show="tab === 'senha'" x-cloak>
        <x-card>
            <form method="POST" action="{{ route('configuracoes.senha.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-input label="Senha atual" name="senha_atual" type="password" required :error="$errors->first('senha_atual')" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-input label="Nova senha" name="nova_senha" type="password" required hint="Mínimo de 8 caracteres." :error="$errors->first('nova_senha')" />
                    <x-input label="Confirmar nova senha" name="nova_senha_confirmation" type="password" required />
                </div>

                <div class="flex items-center justify-end pt-2 border-t border-slate-100">
                    <x-button type="submit">Atualizar senha</x-button>
                </div>
            </form>
        </x-card>
    </div>
</div>

@endsection
