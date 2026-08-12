@props(['variant' => 'info', 'dismissible' => true, 'title' => null])

@php
    $variants = [
        'success' => ['wrap' => 'bg-brand-light border-brand-light text-brand-dark', 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        'warning' => ['wrap' => 'bg-amber-50 border-amber-100 text-amber-800', 'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z'],
        'danger' => ['wrap' => 'bg-red-50 border-red-100 text-red-700', 'icon' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z'],
        'info' => ['wrap' => 'bg-blue-50 border-blue-100 text-blue-700', 'icon' => 'M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z'],
    ];

    $style = $variants[$variant] ?? $variants['info'];
@endphp

<div x-data="{ open: true }" x-show="open" x-cloak class="rounded-xl border px-4 py-3 flex items-start gap-3 {{ $style['wrap'] }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="{{ $style['icon'] }}" />
    </svg>

    <div class="flex-1 text-sm">
        @if ($title)
            <p class="font-semibold mb-0.5">{{ $title }}</p>
        @endif
        {{ $slot }}
    </div>

    @if ($dismissible)
        <button type="button" x-on:click="open = false" class="shrink-0 opacity-60 hover:opacity-100 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12" /></svg>
        </button>
    @endif
</div>
