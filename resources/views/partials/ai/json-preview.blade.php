@php
    $emptyMessage = $emptyText ?? 'No data available.';
    $hasRenderablePayload = !($payload === null || $payload === [] || $payload === '');

    if (is_array($payload)) {
        $previewValue = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } elseif (is_string($payload)) {
        $decoded = json_decode($payload, true);
        $previewValue =
            json_last_error() === JSON_ERROR_NONE && is_array($decoded)
                ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : $payload;
    } elseif ($payload !== null) {
        $previewValue = (string) $payload;
    } else {
        $previewValue = null;
    }
@endphp

@if ($hasRenderablePayload && filled($previewValue))
    <pre class="overflow-x-auto rounded-lg bg-slate-950 p-4 text-sm text-slate-100">{{ $previewValue }}</pre>
@else
    <div class="text-sm text-gray-500">{{ $emptyMessage }}</div>
@endif
