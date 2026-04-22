@extends('layouts.admin')

@section('title', 'Offer Result')
@section('page-title', 'Offer result')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Generated Offer</h2>
                <p class="mt-1 text-sm text-gray-600">Review the generated offer draft and its stored input context.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('offers.create', ['from' => $offerGeneration->id]) }}"
                    class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Run Again
                </a>

                <a href="{{ route('offers.index') }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Back to Offers
                </a>
            </div>
        </div>

        @php
            $statusClasses = match ($offerGeneration->status) {
                'completed' => 'bg-green-100 text-green-700',
                'failed' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-700',
            };
        @endphp

        <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Offer Summary</h3>

                <dl class="mt-4 space-y-4 text-sm text-gray-700">
                    <div>
                        <dt class="font-medium text-gray-500">Title</dt>
                        <dd class="mt-1">{{ $offerGeneration->title ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Prompt Template</dt>
                        <dd class="mt-1">{{ $offerGeneration->promptTemplate?->title ?? 'Not available' }}</dd>
                        <dd class="mt-1 text-xs text-gray-500">
                            {{ $offerGeneration->promptTemplate?->key ?? 'No prompt key available' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Offer Type</dt>
                        <dd class="mt-1">{{ str($offerGeneration->offer_type)->replace('_', ' ')->title() }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Provider</dt>
                        <dd class="mt-1">{{ $offerGeneration->provider_name ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Model</dt>
                        <dd class="mt-1">{{ $offerGeneration->model_name ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                {{ ucfirst($offerGeneration->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Created</dt>
                        <dd class="mt-1">{{ $offerGeneration->created_at?->format('Y-m-d H:i') ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Rendered Output</h3>
                    </div>

                    <div class="px-6 py-6">
                        <div class="whitespace-pre-wrap text-sm leading-7 text-gray-800">
                            {{ $offerGeneration->output_text ?: 'No output was generated.' }}</div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Input Payload Preview</h3>
                    </div>

                    <div class="px-6 py-6">
                        <pre class="overflow-x-auto rounded-lg bg-slate-950 p-4 text-sm text-slate-100">{{ json_encode($offerGeneration->input_payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
