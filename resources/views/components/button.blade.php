@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'icon' => null,
])

@php
    $variants = [
        'primary' => 'bg-brand text-white hover:bg-brand-dark focus-visible:outline-brand shadow-sm',
        'secondary' => 'bg-white text-ink border border-slate-200 hover:bg-slate-50 focus-visible:outline-brand',
        'ghost' => 'bg-transparent text-muted hover:bg-slate-100 hover:text-ink focus-visible:outline-brand',
        'danger' => 'bg-white text-red-600 border border-red-200 hover:bg-red-50 focus-visible:outline-red-500',
        'danger-solid' => 'bg-red-600 text-white hover:bg-red-700 focus-visible:outline-red-600 shadow-sm',
    ];

    $sizes = [
        'sm' => 'text-xs px-3 py-1.5 gap-1.5',
        'md' => 'text-sm px-4 py-2.5 gap-2',
        'lg' => 'text-sm px-5 py-3 gap-2',
    ];

    $classes = 'inline-flex items-center justify-center rounded-lg font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap '
        .($variants[$variant] ?? $variants['primary']).' '
        .($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
