@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral' => 'bg-slate-100 text-slate-600 border-slate-200',
        'success' => 'bg-brand-light text-brand-dark border-brand-light',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-100',
        'danger' => 'bg-red-50 text-red-700 border-red-100',
        'info' => 'bg-blue-50 text-blue-700 border-blue-100',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border '.($variants[$variant] ?? $variants['neutral'])]) }}>
    {{ $slot }}
</span>
