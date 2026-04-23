<div>
    <dt class="font-medium text-gray-500">Provider</dt>
    <dd class="mt-1">{{ $providerName ?: '-' }}</dd>
</div>
<div>
    <dt class="font-medium text-gray-500">Model</dt>
    <dd class="mt-1">{{ $modelName ?: '-' }}</dd>
</div>
<div>
    <dt class="font-medium text-gray-500">Status</dt>
    <dd class="mt-1">
        @include('partials.ai.status-badge', ['status' => $status])
    </dd>
</div>
<div>
    <dt class="font-medium text-gray-500">Created</dt>
    <dd class="mt-1">{{ $createdAt?->format('Y-m-d H:i') ?: '-' }}</dd>
</div>
