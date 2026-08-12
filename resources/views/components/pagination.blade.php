@props(['meta'])

@php
    $current = $meta['current_page'];
    $last = $meta['last_page'];
    $window = collect(range(max(1, $current - 1), min($last, $current + 1)));
@endphp

<div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-5 py-4 border-t border-slate-100">
    <p class="text-xs text-muted">
        Mostrando <span class="font-medium text-ink">{{ $meta['from'] }}</span>
        a <span class="font-medium text-ink">{{ $meta['to'] }}</span>
        de <span class="font-medium text-ink">{{ $meta['total'] }}</span> registros
    </p>

    <div class="flex items-center gap-1">
        <a
            href="{{ $current > 1 ? request()->fullUrlWithQuery(['page' => $current - 1]) : '#' }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm text-muted border border-slate-200 hover:bg-slate-50 transition-colors {{ $current <= 1 ? 'pointer-events-none opacity-40' : '' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
        </a>

        @if ($window->first() > 1)
            <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm text-muted hover:bg-slate-50 transition-colors">1</a>
            <span class="text-slate-300 px-1">&hellip;</span>
        @endif

        @foreach ($window as $page)
            <a
                href="{{ request()->fullUrlWithQuery(['page' => $page]) }}"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-medium transition-colors {{ $page === $current ? 'bg-brand text-white' : 'text-ink hover:bg-slate-50' }}"
            >{{ $page }}</a>
        @endforeach

        @if ($window->last() < $last)
            <span class="text-slate-300 px-1">&hellip;</span>
            <a href="{{ request()->fullUrlWithQuery(['page' => $last]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm text-muted hover:bg-slate-50 transition-colors">{{ $last }}</a>
        @endif

        <a
            href="{{ $current < $last ? request()->fullUrlWithQuery(['page' => $current + 1]) : '#' }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm text-muted border border-slate-200 hover:bg-slate-50 transition-colors {{ $current >= $last ? 'pointer-events-none opacity-40' : '' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
        </a>
    </div>
</div>
