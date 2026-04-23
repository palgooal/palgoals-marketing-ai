<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            <p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @include('partials.ai.status-badge', ['status' => $status])
            @include('partials.ai.publish-badge', ['isPublished' => $isPublished])
        </div>
    </div>

    <dl class="mt-6 grid gap-4 text-sm text-gray-700 md:grid-cols-2 xl:grid-cols-3">
        <div>
            <dt class="font-medium text-gray-500">{{ $typeLabel }}</dt>
            <dd class="mt-1">{{ $typeValue ?: '-' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-gray-500">Prompt Template</dt>
            <dd class="mt-1">{{ $promptTitle ?: 'Not available' }}</dd>
            <dd class="mt-1 text-xs text-gray-500">{{ $promptKey ?: 'No prompt key available' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-gray-500">Provider / Model</dt>
            <dd class="mt-1">{{ trim(($providerName ?: '-') . ' / ' . ($modelName ?: '-')) }}</dd>
        </div>
        <div>
            <dt class="font-medium text-gray-500">Published At</dt>
            <dd class="mt-1">{{ $publishedAt?->format('Y-m-d H:i') ?: '-' }}</dd>
        </div>
    </dl>
</div>
