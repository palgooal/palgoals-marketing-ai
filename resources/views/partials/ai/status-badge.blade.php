@php
    $resolvedStatus = match ($status ?? 'unknown') {
        'completed' => 'draft',
        default => $status ?? 'unknown',
    };
    $statusClasses = match ($resolvedStatus) {
        'draft' => 'bg-amber-100 text-amber-700',
        'reviewed' => 'bg-blue-100 text-blue-700',
        'approved' => 'bg-green-100 text-green-700',
        'failed' => 'bg-red-100 text-red-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
    {{ ucfirst($resolvedStatus) }}
</span>
