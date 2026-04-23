<div class="rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-6 py-4">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $title }}</h3>
    </div>

    <div class="px-6 py-6">
        <div class="{{ $bodyClass ?? 'whitespace-pre-wrap text-sm leading-7 text-gray-800' }}">
            {{ filled($content) ? $content : $emptyText ?? '-' }}
        </div>
    </div>
</div>
