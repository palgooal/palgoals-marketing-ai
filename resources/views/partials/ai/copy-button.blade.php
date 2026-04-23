@php
    $label = $label ?? 'Copy';
    $copiedLabel = $copiedLabel ?? 'Copied';
    $variant = $variant ?? 'secondary';
    $classes =
        $variant === 'primary'
            ? 'inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800'
            : 'inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50';
@endphp

<button type="button" class="{{ $classes }}" data-original-label="{{ $label }}"
    data-copied-label="{{ $copiedLabel }}"
    onclick="const button = this; navigator.clipboard.writeText(@js($text ?? '')).then(() => { clearTimeout(button._copyTimer); button.textContent = button.dataset.copiedLabel; button._copyTimer = setTimeout(() => button.textContent = button.dataset.originalLabel, 1500); });">
    {{ $label }}
</button>
