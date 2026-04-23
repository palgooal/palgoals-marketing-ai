@extends('layouts.admin')

@section('title', 'AI Log')
@section('page-title', 'AI log details')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">AI Request Log</h2>
                <p class="mt-1 text-sm text-gray-600">Review the stored prompt snapshot, payloads, and execution metadata.
                </p>
            </div>

            <a href="{{ route('logs.ai-requests.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                Back to AI Logs
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Request Summary</h3>

                <dl class="mt-4 space-y-4 text-sm text-gray-700">
                    <div>
                        <dt class="font-medium text-gray-500">Organization</dt>
                        <dd class="mt-1">{{ $aiRequest->organization?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Module</dt>
                        <dd class="mt-1">{{ $aiRequest->module }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Task Type</dt>
                        <dd class="mt-1">{{ $aiRequest->task_type }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Provider</dt>
                        <dd class="mt-1">{{ $aiRequest->provider_name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Model</dt>
                        <dd class="mt-1">{{ $aiRequest->model_name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Tokens In / Out</dt>
                        <dd class="mt-1">{{ $aiRequest->tokens_input ?? '-' }} / {{ $aiRequest->tokens_output ?? '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Estimated Cost</dt>
                        <dd class="mt-1">{{ $aiRequest->estimated_cost !== null ? $aiRequest->estimated_cost : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Latency</dt>
                        <dd class="mt-1">{{ $aiRequest->latency_ms !== null ? $aiRequest->latency_ms . ' ms' : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @include('partials.ai.status-badge', ['status' => $aiRequest->status])
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Created</dt>
                        <dd class="mt-1">{{ $aiRequest->created_at?->format('Y-m-d H:i') ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                @if ($aiRequest->error_message)
                    <div class="rounded-xl border border-red-200 bg-red-50 px-6 py-5 shadow-sm">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-red-700">Error Message</h3>
                        <div class="mt-3 whitespace-pre-wrap text-sm leading-7 text-red-800">
                            {{ $aiRequest->error_message }}</div>
                    </div>
                @endif

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Prompt Snapshot</h3>
                    </div>

                    <div class="px-6 py-6">
                        <pre class="overflow-x-auto rounded-lg bg-slate-950 p-4 text-sm text-slate-100">{{ $aiRequest->prompt_snapshot ?: 'No prompt snapshot was stored.' }}</pre>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Input Payload</h3>
                    </div>

                    <div class="px-6 py-6">
                        @include('partials.ai.json-preview', [
                            'payload' => $aiRequest->input_payload,
                            'emptyText' => 'No input payload was stored.',
                        ])
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Output Payload</h3>
                    </div>

                    <div class="px-6 py-6">
                        @include('partials.ai.json-preview', [
                            'payload' => $aiRequest->output_payload,
                            'emptyText' => 'No output payload was stored.',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
