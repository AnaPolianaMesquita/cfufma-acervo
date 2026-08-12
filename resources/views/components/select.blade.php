@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'placeholder' => 'Selecione...',
    'selected' => null,
    'required' => false,
])

<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-ink mb-1.5">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-ink shadow-sm transition-colors focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand bg-white',
        ]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $text }}</option>
        @endforeach

        {{ $slot }}
    </select>
</div>
