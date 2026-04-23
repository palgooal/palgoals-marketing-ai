@extends('layouts.admin')

@section('title', 'Analysis Package')
@section('page-title', 'Analysis package')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Analysis Package</h2>
                <p class="mt-1 text-sm text-gray-600">Internal handoff view for this generated analysis record.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('analysis.export-package-text', $pageAnalysis) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Export Package Text
                </a>

                <a href="{{ route('analysis.show', $pageAnalysis) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Back to Result
                </a>
            </div>
        </div>

        @include('partials.ai.package-header', [
            'title' => $pageAnalysis->page_title ?: 'Untitled Analysis',
            'subtitle' => 'Internal analysis handoff package.',
            'typeLabel' => 'Page Type',
            'typeValue' => $pageAnalysis->page_type,
            'status' => $pageAnalysis->status,
            'isPublished' => $pageAnalysis->is_published,
            'publishedAt' => $pageAnalysis->published_at,
            'providerName' => $pageAnalysis->provider_name,
            'modelName' => $pageAnalysis->model_name,
            'promptTitle' => $pageAnalysis->promptTemplate?->title,
            'promptKey' => $pageAnalysis->promptTemplate?->key,
        ])

        @include('partials.ai.package-section', [
            'title' => 'Context Details',
            'content' => trim(
                implode(PHP_EOL . PHP_EOL,
                    array_filter([
                        data_get($pageAnalysis->input_payload, 'context')
                            ? 'Context: ' . data_get($pageAnalysis->input_payload, 'context')
                            : null,
                        data_get($pageAnalysis->input_payload, 'page_content')
                            ? 'Page Content:' . PHP_EOL . data_get($pageAnalysis->input_payload, 'page_content')
                            : null,
                    ]))),
            'emptyText' => 'No stored context details.',
        ])

        @include('partials.ai.package-section', [
            'title' => 'Main Output',
            'content' => trim(
                implode(PHP_EOL . PHP_EOL,
                    array_filter([
                        $pageAnalysis->findings_text
                            ? 'Findings' . PHP_EOL . '--------' . PHP_EOL . $pageAnalysis->findings_text
                            : null,
                        $pageAnalysis->recommendations_text
                            ? 'Recommendations' .
                                PHP_EOL .
                                '---------------' .
                                PHP_EOL .
                                $pageAnalysis->recommendations_text
                            : null,
                    ]))),
            'emptyText' => 'No output was generated.',
        ])

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Input Payload Preview</h3>
            </div>

            <div class="px-6 py-6">
                @include('partials.ai.json-preview', [
                    'payload' => $pageAnalysis->input_payload,
                    'emptyText' => 'No input payload was stored.',
                ])
            </div>
        </div>
    </div>
@endsection
