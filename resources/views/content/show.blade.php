@extends('layouts.admin')

@section('title', 'Content Result')
@section('page-title', 'Content result')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-6 py-4 text-sm text-green-700 shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-700 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Generated Content</h2>
                <p class="mt-1 text-sm text-gray-600">Review the generated output and its input context.</p>
            </div>

            <div class="flex items-center gap-3">
                @include('partials.ai.copy-button', [
                    'text' => $contentGeneration->output_text ?: 'No output was generated.',
                    'label' => 'Copy Output',
                ])

                <a href="{{ route('content.export-text', $contentGeneration) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Export Text
                </a>

                <a href="{{ route('content.package', $contentGeneration) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    View Package
                </a>

                <a href="{{ route('content.export-package-text', $contentGeneration) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Export Package Text
                </a>

                <a href="{{ route('content.create', ['from' => $contentGeneration->id]) }}"
                    class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Run Again
                </a>

                <a href="{{ route('content.index') }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Back to Content
                </a>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold uppercase tracking-wide text-gray-500">Review Status</div>
                        <div class="mt-3">
                            @include('partials.ai.status-badge', ['status' => $contentGeneration->status])
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold uppercase tracking-wide text-gray-500">Publishing Status</div>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            @include('partials.ai.publish-badge', [
                                'isPublished' => $contentGeneration->is_published,
                            ])

                            <span class="text-xs text-gray-500">
                                {{ $contentGeneration->published_at?->format('Y-m-d H:i') ?: 'Not published yet' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if (\App\Support\AIOutputStatuses::canMarkReviewed($contentGeneration->status))
                        <form method="POST" action="{{ route('content.mark-reviewed', $contentGeneration) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                                Mark as Reviewed
                            </button>
                        </form>
                    @endif

                    @if (\App\Support\AIOutputStatuses::canMarkApproved($contentGeneration->status))
                        <form method="POST" action="{{ route('content.mark-approved', $contentGeneration) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Mark as Approved
                            </button>
                        </form>
                    @endif

                    @if ($contentGeneration->is_published)
                        <form method="POST" action="{{ route('content.unpublish', $contentGeneration) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                                Unpublish Output
                            </button>
                        </form>
                    @elseif (\App\Support\AIOutputStatuses::canPublish($contentGeneration->status))
                        <form method="POST" action="{{ route('content.publish', $contentGeneration) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                                Publish Output
                            </button>
                        </form>
                    @else
                        <div class="text-xs text-gray-500">Publish is available after review or approval.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Generation Metadata</h3>

                <dl class="mt-4 space-y-4 text-sm text-gray-700">
                    <div>
                        <dt class="font-medium text-gray-500">Title</dt>
                        <dd class="mt-1 flex items-center gap-2">
                            <span>{{ $contentGeneration->title ?: '-' }}</span>
                            @if (filled($contentGeneration->title))
                                @include('partials.ai.copy-button', [
                                    'text' => $contentGeneration->title,
                                    'label' => 'Copy Title',
                                ])
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Template</dt>
                        <dd class="mt-1">{{ $contentGeneration->promptTemplate?->title ?? 'Not available' }}</dd>
                        <dd class="mt-1 text-xs text-gray-500">
                            {{ $contentGeneration->promptTemplate?->key ?? 'No prompt key available' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Type</dt>
                        <dd class="mt-1">{{ $contentGeneration->type }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Language</dt>
                        <dd class="mt-1">{{ $contentGeneration->language ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Tone</dt>
                        <dd class="mt-1">{{ $contentGeneration->tone ?: '-' }}</dd>
                    </div>
                    @include('partials.ai.result-meta', [
                        'providerName' => $contentGeneration->provider_name,
                        'modelName' => $contentGeneration->model_name,
                        'status' => $contentGeneration->status,
                        'createdAt' => $contentGeneration->created_at,
                    ])
                    <div>
                        <dt class="font-medium text-gray-500">Published At</dt>
                        <dd class="mt-1">{{ $contentGeneration->published_at?->format('Y-m-d H:i') ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Output</h3>

                        @include('partials.ai.copy-button', [
                            'text' => $contentGeneration->output_text ?: 'No output was generated.',
                            'label' => 'Copy Output',
                        ])
                    </div>

                    <div class="px-6 py-6">
                        <div class="rounded-lg bg-gray-50 p-4 whitespace-pre-wrap text-sm leading-7 text-gray-800">
                            {{ $contentGeneration->output_text ?: 'No output was generated.' }}</div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Input Payload Preview</h3>

                        @include('partials.ai.copy-button', [
                            'text' =>
                                json_encode(
                                    $contentGeneration->input_payload ?? [],
                                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?:
                                '{}',
                            'label' => 'Copy JSON',
                        ])
                    </div>

                    <div class="px-6 py-6">
                        @include('partials.ai.json-preview', [
                            'payload' => $contentGeneration->input_payload,
                            'emptyText' => 'No input payload was stored.',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
