@props(['padded' => true])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200 shadow-sm '.($padded ? 'p-6' : '')]) }}>
    {{ $slot }}
</div>
