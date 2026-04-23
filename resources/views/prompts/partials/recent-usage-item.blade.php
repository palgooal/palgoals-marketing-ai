<div class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700">
    <div>
        <div class="font-medium text-gray-900">{{ $usage['label'] }}</div>
        <div class="mt-1 text-xs text-gray-500">
            {{ $usage['module'] }} · {{ $usage['created_at']?->format('Y-m-d H:i') ?: '-' }}
        </div>
    </div>

    <a href="{{ $usage['url'] }}" class="font-medium text-slate-700 hover:text-slate-900">View</a>
</div>
