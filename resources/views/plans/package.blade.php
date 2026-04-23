@extends('layouts.admin')

@section('title', 'Plan Package')
@section('page-title', 'Plan package')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Plan Package</h2>
                <p class="mt-1 text-sm text-gray-600">Internal handoff view for this generated strategy plan.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('plans.export-package-text', $strategyPlan) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Export Package Text
                </a>

                <a href="{{ route('plans.show', $strategyPlan) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Back to Result
                </a>
            </div>
        </div>

        @include('partials.ai.package-header', [
            'title' => $strategyPlan->title ?: 'Untitled Plan',
            'subtitle' => 'Internal strategy plan handoff package.',
            'typeLabel' => 'Period Type',
            'typeValue' => $strategyPlan->period_type,
            'status' => $strategyPlan->status,
            'isPublished' => $strategyPlan->is_published,
            'publishedAt' => $strategyPlan->published_at,
            'providerName' => $strategyPlan->provider_name,
            'modelName' => $strategyPlan->model_name,
            'promptTitle' => $strategyPlan->promptTemplate?->title,
            'promptKey' => $strategyPlan->promptTemplate?->key,
        ])

        @include('partials.ai.package-section', [
            'title' => 'Context Details',
            'content' => data_get($strategyPlan->input_payload, 'context'),
            'emptyText' => 'No stored context details.',
        ])

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Goals</h3>
            </div>

            <div class="px-6 py-6">
                @if (($strategyPlan->goals_json ?? []) !== [])
                    <ul class="space-y-2 text-sm text-gray-800">
                        @foreach ($strategyPlan->goals_json as $goal)
                            <li>{{ $goal }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-sm text-gray-500">No goals were provided.</div>
                @endif
            </div>
        </div>

        @include('partials.ai.package-section', [
            'title' => 'Main Output',
            'content' => $strategyPlan->output_text,
            'emptyText' => 'No output was generated.',
        ])

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Input Payload Preview</h3>
            </div>

            <div class="px-6 py-6">
                @include('partials.ai.json-preview', [
                    'payload' => $strategyPlan->input_payload,
                    'emptyText' => 'No input payload was stored.',
                ])
            </div>
        </div>
    </div>
@endsection
