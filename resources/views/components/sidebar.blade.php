@php
    $usuario = auth()->user();
    $iniciais = collect(explode(' ', $usuario->name))->map(fn ($n) => mb_substr($n, 0, 1))->take(2)->implode('');

    $nav = collect([
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard'),
            'visible' => true,
            'icon' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="10" rx="1"/><rect width="7" height="5" x="3" y="15" rx="1"/>',
        ],
        [
            'label' => 'Acervo',
            'route' => 'acervo.index',
            'active' => request()->routeIs('acervo.*'),
            'visible' => true,
            'icon' => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/><path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"/>',
        ],
        [
            'label' => 'Importar Excel',
            'route' => 'importacao.index',
            'active' => request()->routeIs('importacao.*'),
            'visible' => in_array($usuario->perfil, ['Administrador', 'Curador'], true),
            'icon' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 13h8"/><path d="M8 17h8"/><path d="M10 9h2"/>',
        ],
        [
            'label' => 'Relatórios',
            'route' => 'relatorios.index',
            'active' => request()->routeIs('relatorios.*'),
            'visible' => true,
            'icon' => '<line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/>',
        ],
        [
            'label' => 'Configurações',
            'route' => 'configuracoes.index',
            'active' => request()->routeIs('configuracoes.*'),
            'visible' => true,
            'icon' => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
        ],
    ])->filter(fn ($item) => $item['visible']);
@endphp

<div
    x-show="sidebarOpen"
    x-cloak
    x-on:click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden"
></div>

<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200 flex flex-col justify-between z-40 transition-transform duration-200 lg:translate-x-0"
>
    <div class="min-h-0 flex-1 overflow-y-auto">
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-brand flex items-center justify-center text-white font-bold text-lg">
                    M
                </div>
                <div>
                    <span class="text-base font-bold text-ink block leading-tight">Micoteca</span>
                    <span class="text-xs text-muted block">Gestão de Fungos</span>
                </div>
            </div>

            <button type="button" x-on:click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600 p-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="p-4 space-y-1">
            @foreach ($nav as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $item['active'] ? 'bg-brand-light text-brand-dark' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    <div class="p-4 border-t border-slate-100 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-full bg-brand-light flex items-center justify-center font-semibold text-brand-dark text-sm shrink-0">
                {{ $iniciais }}
            </div>
            <div class="min-w-0">
                <span class="text-sm font-medium text-slate-700 block truncate leading-none mb-1">{{ $usuario->name }}</span>
                <span class="text-xs text-muted block truncate">{{ $usuario->perfil }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Sair" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
            </button>
        </form>
    </div>
</aside>
