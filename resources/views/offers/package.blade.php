@extends('layouts.admin')

@section('title', 'Offer Package')
@section('page-title', 'Offer package')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Offer Package</h2>
                <p class="mt-1 text-sm text-gray-600">Internal handoff view for this generated offer record.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('offers.export-package-text', $offerGeneration) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Export Package Text
                </a>

                <a href="{{ route('offers.show', $offerGeneration) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Back to Result
                </a>
            </div>
        </div>

        @include('partials.ai.package-header', [
            'title' => $offerGeneration->title ?: 'Untitled Offer',
            'subtitle' => 'Internal offer handoff package.',
            'typeLabel' => 'Offer Type',
            'typeValue' => $offerGeneration->offer_type,
            'status' => $offerGeneration->status,
            'isPublished' => $offerGeneration->is_published,
            'publishedAt' => $offerGeneration->published_at,
            'providerName' => $offerGeneration->provider_name,
            'modelName' => $offerGeneration->model_name,
            'promptTitle' => $offerGeneration->promptTemplate?->title,
            'promptKey' => $offerGeneration->promptTemplate?->key,
        ])

        @include('partials.ai.package-section', [
            'title' => 'Context Details',
            'content' => data_get($offerGeneration->input_payload, 'context'),
            'emptyText' => 'No stored context details.',
        ])

        @include('partials.ai.package-section', [
            'title' => 'Main Output',
            'content' => $offerGeneration->output_text,
            'emptyText' => 'No output was generated.',
        ])

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Input Payload Preview</h3>
            </div>

            <div class="px-6 py-6">
                @include('partials.ai.json-preview', [
                    'payload' => $offerGeneration->input_payload,
                    'emptyText' => 'No input payload was stored.',
                ])
            </div>
        </div>
    </div>
@endsection
