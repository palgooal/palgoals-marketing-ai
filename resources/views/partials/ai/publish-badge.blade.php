@php
    $publishClasses = $isPublished ?? false ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700';
@endphp

<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $publishClasses }}">
    {{ $isPublished ?? false ? 'Published' : 'Unpublished' }}
</span>
