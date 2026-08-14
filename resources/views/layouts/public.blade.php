<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Micoteca - Acervo público')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-surface text-ink antialiased flex flex-col">
    <x-toast />

    <header class="bg-white border-b border-slate-200 px-4 sm:px-10 py-5 flex items-center justify-between sticky top-0 z-20">
        <a href="{{ route('galeria.index') }}" class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand to-brand-dark flex items-center justify-center text-white shadow-sm shadow-brand/30 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 10C4 5.5 7.5 3 12 3s8 2.5 8 7" />
                    <path d="M3 10h18" />
                    <path d="M9 10v6a3 3 0 0 0 6 0v-6" />
                </svg>
            </div>
            <div>
                <span class="text-xl font-bold text-ink block leading-tight">Micoteca</span>
                <span class="text-sm text-muted block">Acervo público de fungos</span>
            </div>
        </a>

        @auth
            <x-button variant="secondary" :href="route('dashboard')">Painel</x-button>
        @else
            <div class="flex items-center gap-3">
                <x-button variant="ghost" :href="route('login')">Entrar</x-button>
                <x-button :href="route('register')">Criar conta</x-button>
            </div>
        @endauth
    </header>

    <main class="flex-1 p-4 sm:p-8">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white px-4 sm:px-10 py-10">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand to-brand-dark flex items-center justify-center text-white shadow-sm shadow-brand/30 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 10C4 5.5 7.5 3 12 3s8 2.5 8 7" />
                        <path d="M3 10h18" />
                        <path d="M9 10v6a3 3 0 0 0 6 0v-6" />
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-semibold text-ink block">Micoteca</span>
                    <span class="text-xs text-muted block">Acervo mantido pela equipe de curadoria</span>
                </div>
            </div>

            <p class="text-sm text-muted">
                Quer contribuir com novos registros?
                <a href="{{ route('register') }}" class="text-brand font-medium hover:underline">Crie uma conta</a>.
            </p>
        </div>
    </footer>
</body>
</html>
