<div class="rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="font-medium text-gray-900">Version {{ $version->version_number }}</div>
            <div class="mt-1 text-xs text-gray-500">
                {{ $version->created_at?->format('Y-m-d H:i') ?: ($version->updated_at?->format('Y-m-d H:i') ?: '-') }}
            </div>
            <div class="mt-2 text-sm text-gray-700">{{ $version->title }}</div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span
                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $version->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                {{ $version->is_active ? 'Active Snapshot' : 'Inactive Snapshot' }}
            </span>

            <a href="{{ route('prompts.compare', [$promptTemplate, 'from_version_id' => $version->id]) }}"
                class="inline-flex items-center rounded-lg border border-amber-300 px-3 py-2 text-xs font-medium text-amber-700 transition hover:bg-amber-50">
                Compare to Current
            </a>

            <a href="{{ route('prompts.versions.show', [$promptTemplate, $version]) }}"
                class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                Preview
            </a>

            <form method="POST" action="{{ route('prompts.versions.revert', [$promptTemplate, $version]) }}">
                @csrf
                <button type="submit"
                    class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                    Revert
                </button>
            </form>
        </div>
    </div>
</div>
