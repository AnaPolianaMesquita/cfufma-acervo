@extends('layouts.app')

@section('title', 'Usuários - MicoNIBA')
@section('page-title', 'Usuários')

@section('content')

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('configuracoes.index') }}" class="text-sm text-muted hover:text-ink flex items-center gap-1 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                Voltar às configurações
            </a>
            <h1 class="text-2xl font-bold text-ink">Usuários do sistema</h1>
            <p class="text-muted mt-1">{{ $usuarios->count() }} usuários cadastrados.</p>
        </div>
        <x-button x-data x-on:click="$dispatch('open-modal', 'user-create')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14m-7-7h14"/></svg>
            Novo usuário
        </x-button>
    </div>

    <x-card :padded="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-muted border-b border-slate-100">
                        <th class="p-4 font-medium">Nome</th>
                        <th class="p-4 font-medium">E-mail</th>
                        <th class="p-4 font-medium">Perfil</th>
                        <th class="p-4 font-medium">Status</th>
                        <th class="p-4 font-medium">Último acesso</th>
                        <th class="p-4 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $u)
                        <tr class="border-t border-slate-50 hover:bg-slate-50/60 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-light flex items-center justify-center font-semibold text-brand-dark text-xs shrink-0">
                                        {{ collect(explode(' ', $u->name))->map(fn ($n) => mb_substr($n, 0, 1))->take(2)->implode('') }}
                                    </div>
                                    <span class="font-medium text-ink">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-slate-600">{{ $u->email }}</td>
                            <td class="p-4"><x-badge variant="info">{{ $u->perfil }}</x-badge></td>
                            <td class="p-4">
                                <x-badge :variant="$u->ativo ? 'success' : 'neutral'">
                                    {{ $u->ativo ? 'Ativo' : 'Inativo' }}
                                </x-badge>
                            </td>
                            <td class="p-4 text-slate-600">{{ $u->ultimo_acesso?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="p-4">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" title="Editar" x-data x-on:click="$dispatch('open-modal', 'user-edit-{{ $u->id }}')" class="p-1.5 text-slate-400 hover:text-brand hover:bg-brand-light rounded-lg transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                    </button>

                                    <form method="POST" action="{{ route('configuracoes.usuarios.toggle', $u->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="{{ $u->ativo ? 'Desativar' : 'Ativar' }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                            @if ($u->ativo)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 12.75 2.25 2.25 6-6M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    <x-card>
        <h3 class="font-semibold text-ink mb-1">Permissões por perfil</h3>
        <p class="text-xs text-muted mb-5">Controla quais módulos cada perfil pode acessar. O perfil Administrador tem acesso total fixo, para evitar que alguém perca o acesso à própria gestão de permissões.</p>

        <form method="POST" action="{{ route('configuracoes.permissoes.update') }}">
            @csrf
            @method('PUT')

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-muted border-b border-slate-100">
                            <th class="py-3 pr-4 font-medium">Perfil</th>
                            <th class="py-3 px-4 font-medium text-center">Acervo</th>
                            <th class="py-3 px-4 font-medium text-center">Importação</th>
                            <th class="py-3 px-4 font-medium text-center">Relatórios</th>
                            <th class="py-3 px-4 font-medium text-center">Usuários</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['Administrador', 'Curador', 'Consulta'] as $perfil)
                            <tr class="border-t border-slate-50">
                                <td class="py-3 pr-4 font-medium text-ink">{{ $perfil }}</td>
                                @foreach (['acervo', 'importar', 'relatorios'] as $modulo)
                                    <td class="py-3 px-4 text-center">
                                        @if ($perfil === 'Administrador')
                                            <input type="checkbox" checked disabled title="Administrador sempre tem acesso total" class="rounded border-slate-300 text-brand focus:ring-brand opacity-60">
                                        @else
                                            <input type="hidden" name="permissoes[{{ $perfil }}][{{ $modulo }}]" value="0">
                                            <input
                                                type="checkbox"
                                                name="permissoes[{{ $perfil }}][{{ $modulo }}]"
                                                value="1"
                                                @checked(optional($permissoes[$perfil] ?? null)->$modulo)
                                                class="rounded border-slate-300 text-brand focus:ring-brand"
                                            >
                                        @endif
                                    </td>
                                @endforeach
                                <td class="py-3 px-4 text-center">
                                    <span class="text-xs {{ $perfil === 'Administrador' ? 'text-brand-dark font-medium' : 'text-slate-400' }}" title="Gestão de usuários é sempre restrita ao Administrador">
                                        {{ $perfil === 'Administrador' ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-5">
                <x-button type="submit">Salvar permissões</x-button>
            </div>
        </form>
    </x-card>
</div>

<x-modal name="user-create">
    <form method="POST" action="{{ route('configuracoes.usuarios.store') }}" class="p-6">
        @csrf
        <h3 class="text-lg font-semibold text-ink mb-5">Novo usuário</h3>

        <div class="space-y-4">
            <x-input label="Nome completo" name="nome" required />
            <x-input label="E-mail" name="email" type="email" required />
            <x-select label="Perfil de acesso" name="perfil" required :options="['Administrador' => 'Administrador', 'Curador' => 'Curador', 'Consulta' => 'Consulta']" placeholder="Selecione..." />
            <x-input label="Senha inicial" name="senha" type="password" required hint="Mínimo de 8 caracteres. O usuário poderá trocá-la depois." />
        </div>

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button type="button" variant="secondary" x-data x-on:click="$dispatch('close-modal', 'user-create')">Cancelar</x-button>
            <x-button type="submit">Criar usuário</x-button>
        </div>
    </form>
</x-modal>

@foreach ($usuarios as $u)
    <x-modal name="user-edit-{{ $u->id }}">
        <form method="POST" action="{{ route('configuracoes.usuarios.update', $u->id) }}" class="p-6">
            @csrf
            @method('PUT')
            <h3 class="text-lg font-semibold text-ink mb-5">Editar usuário</h3>

            <div class="space-y-4">
                <x-input label="Nome completo" name="nome" required value="{{ $u->name }}" />
                <x-input label="E-mail" name="email" type="email" required value="{{ $u->email }}" />
                <x-select
                    label="Perfil de acesso"
                    name="perfil"
                    required
                    :options="['Administrador' => 'Administrador', 'Curador' => 'Curador', 'Consulta' => 'Consulta']"
                    :selected="$u->perfil"
                    :placeholder="null"
                />
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <x-button type="button" variant="secondary" x-data x-on:click="$dispatch('close-modal', 'user-edit-{{ $u->id }}')">Cancelar</x-button>
                <x-button type="submit">Salvar alterações</x-button>
            </div>
        </form>
    </x-modal>
@endforeach

@endsection
