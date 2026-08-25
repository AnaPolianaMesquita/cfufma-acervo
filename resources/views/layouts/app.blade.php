<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MicoNIBA')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="{ sidebarOpen: false, loading: false }"
    x-on:navigate-start.window="loading = true"
    class="h-full bg-surface text-ink antialiased"
>
    <div
        x-show="loading"
        x-cloak
        class="fixed top-0 left-0 right-0 h-0.5 z-[70] bg-brand-light overflow-hidden"
    >
        <div class="h-full bg-brand animate-pulse w-full"></div>
    </div>

    <x-toast />

    @include('components.sidebar')

    <div class="min-h-full flex flex-col lg:pl-64">
        @include('components.navbar')

        <main class="flex-1 p-4 sm:p-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
