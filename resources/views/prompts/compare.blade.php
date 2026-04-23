@extends('layouts.admin')

@section('title', 'Prompt Comparison')
@section('page-title', 'Prompt comparison')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Prompt Comparison</h2>
                <p class="mt-1 text-sm text-gray-600">Lightweight field comparison for this prompt template without a full diff engine.</p>
            </div>

            <a href="{{ route('prompts.edit', $promptTemplate) }}"
                class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                Back to Edit
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            @foreach (['from', 'to'] as $side)
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ strtoupper($side) }}</div>
                    <div class="mt-2 text-lg font-semibold text-gray-900">{{ $comparison[$side]['label'] }}</div>
                    <div class="mt-1 text-sm text-gray-500">
                        {{ $comparison[$side]['timestamp']?->format('Y-m-d H:i') ?: 'No timestamp available' }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="space-y-4">
            @foreach ($comparison['fields'] as $field)
                <div class="rounded-xl border {{ $field['changed'] ? 'border-amber-200 bg-amber-50' : 'border-gray-200 bg-white' }} shadow-sm">
                    <div class="flex items-center justify-between gap-4 border-b {{ $field['changed'] ? 'border-amber-200' : 'border-gray-200' }} px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $field['label'] }}</h3>

                        <span
                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $field['changed'] ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $field['changed'] ? 'Changed' : 'Unchanged' }}
                        </span>
                    </div>

                    <div class="grid gap-px md:grid-cols-2">
                        <div class="px-6 py-5 {{ $field['changed'] ? 'bg-amber-50/60' : 'bg-white' }}">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $comparison['from']['label'] }}</div>
                            <div class="mt-3 whitespace-pre-wrap rounded-lg bg-white/80 p-4 text-sm leading-7 text-gray-800 ring-1 ring-inset ring-gray-200">
                                {{ filled($field['from']) ? $field['from'] : '-' }}
                            </div>
                        </div>

                        <div class="px-6 py-5 {{ $field['changed'] ? 'bg-amber-50/30' : 'bg-white' }}">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $comparison['to']['label'] }}</div>
                            <div class="mt-3 whitespace-pre-wrap rounded-lg bg-white/80 p-4 text-sm leading-7 text-gray-800 ring-1 ring-inset ring-gray-200">
                                {{ filled($field['to']) ? $field['to'] : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection