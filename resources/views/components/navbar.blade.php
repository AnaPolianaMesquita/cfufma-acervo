@php
    $usuario = auth()->user();
    $iniciais = collect(explode(' ', $usuario->name))->map(fn ($n) => mb_substr($n, 0, 1))->take(2)->implode('');
@endphp

<header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-20">
    <div class="flex items-center gap-4 min-w-0">
        <button type="button" x-on:click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 p-1 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
        </button>

        <div class="min-w-0">
            <h1 class="text-lg font-semibold text-ink truncate">@yield('page-title', 'Painel Geral')</h1>
        </div>
    </div>

    <div class="flex items-center gap-3 sm:gap-4">
        <div class="relative w-40 sm:w-64 hidden sm:block">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </span>
            <input type="text" placeholder="Buscar código ou espécie..." class="w-full text-xs pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-brand focus:ring-1 focus:ring-brand transition-colors">
        </div>

        <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

        <div x-data="{ open: false }" class="relative">
            <button type="button" x-on:click="open = !open" class="flex items-center gap-2 p-1 rounded-lg hover:bg-slate-50 transition-colors">
                <div class="w-8 h-8 rounded-full bg-brand-light flex items-center justify-center font-semibold text-brand-dark text-xs">
                    {{ $iniciais }}
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 hidden sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
            </button>

            <div
                x-show="open"
                x-cloak
                x-on:click.outside="open = false"
                x-transition:enter="ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="absolute right-0 mt-2 w-56 bg-white rounded-xl border border-slate-200 shadow-lg py-1.5 z-30"
            >
                <div class="px-3.5 py-2 border-b border-slate-100">
                    <p class="text-sm font-medium text-ink truncate">{{ $usuario->name }}</p>
                    <p class="text-xs text-muted truncate">{{ $usuario->email }}</p>
                </div>

                <a href="{{ route('configuracoes.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-slate-600 hover:bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                    Meu perfil
                </a>

                @if ($usuario->perfil === 'Administrador')
                    <a href="{{ route('configuracoes.usuarios') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-slate-600 hover:bg-slate-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                        Configurações
                    </a>
                @endif

                <div class="border-t border-slate-100 my-1.5"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm text-red-600 hover:bg-red-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
