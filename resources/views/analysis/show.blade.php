@extends('layouts.admin')

@section('title', 'Analysis Result')
@section('page-title', 'Analysis result')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Generated Page Analysis</h2>
                <p class="mt-1 text-sm text-gray-600">Review the stored page review output and its input context.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('analysis.create', ['from' => $pageAnalysis->id]) }}"
                    class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Run Again
                </a>

                <a href="{{ route('analysis.index') }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Back to Analysis
                </a>
            </div>
        </div>

        @php
            $statusClasses = match ($pageAnalysis->status) {
                'completed' => 'bg-green-100 text-green-700',
                'failed' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-700',
            };
        @endphp

        <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Analysis Summary</h3>

                <dl class="mt-4 space-y-4 text-sm text-gray-700">
                    <div>
                        <dt class="font-medium text-gray-500">Page Title</dt>
                        <dd class="mt-1">{{ $pageAnalysis->page_title ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Page URL</dt>
                        <dd class="mt-1 break-all">{{ $pageAnalysis->page_url ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Page Type</dt>
                        <dd class="mt-1">
                            {{ $pageAnalysis->page_type ? str($pageAnalysis->page_type)->replace('_', ' ')->title() : '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Prompt Template</dt>
                        <dd class="mt-1">{{ $pageAnalysis->promptTemplate?->title ?? 'Not available' }}</dd>
                        <dd class="mt-1 text-xs text-gray-500">
                            {{ $pageAnalysis->promptTemplate?->key ?? 'No prompt key available' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Provider</dt>
                        <dd class="mt-1">{{ $pageAnalysis->provider_name ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Model</dt>
                        <dd class="mt-1">{{ $pageAnalysis->model_name ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Score</dt>
                        <dd class="mt-1">{{ $pageAnalysis->score ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                {{ ucfirst($pageAnalysis->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Created</dt>
                        <dd class="mt-1">{{ $pageAnalysis->created_at?->format('Y-m-d H:i') ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Findings</h3>
                    </div>

                    <div class="px-6 py-6">
                        <div class="whitespace-pre-wrap text-sm leading-7 text-gray-800">
                            {{ $pageAnalysis->findings_text ?: 'No separate findings were extracted in this first analyzer step.' }}
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Recommendations</h3>
                    </div>

                    <div class="px-6 py-6">
                        <div class="whitespace-pre-wrap text-sm leading-7 text-gray-800">
                            {{ $pageAnalysis->recommendations_text ?: 'No recommendations were generated.' }}</div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Input Payload Preview</h3>
                    </div>

                    <div class="px-6 py-6">
                        <pre class="overflow-x-auto rounded-lg bg-slate-950 p-4 text-sm text-slate-100">{{ json_encode($pageAnalysis->input_payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
