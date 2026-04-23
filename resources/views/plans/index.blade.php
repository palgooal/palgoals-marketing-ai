@extends('layouts.admin')

@section('title', 'Plans')
@section('page-title', 'Planner foundation')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Plans</h2>
                <p class="mt-1 text-sm text-gray-600">Recent generated strategy plans using the lightweight planner workflow.
                </p>
            </div>

            <a href="{{ route('plans.create') }}"
                class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                New strategy plan
            </a>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('plans.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div>
                    <label for="search" class="text-sm font-medium text-gray-700">Title Search</label>
                    <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700"
                        placeholder="Search titles" />
                </div>

                <div>
                    <label for="period_type" class="text-sm font-medium text-gray-700">Period Type</label>
                    <select id="period_type" name="period_type"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All period types</option>
                        @foreach ($availablePeriodTypes as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['period_type'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All statuses</option>
                        @foreach ($availableStatuses as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="published" class="text-sm font-medium text-gray-700">Publishing</label>
                    <select id="published" name="published"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All outputs</option>
                        @foreach ($availablePublishedStates as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['published'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="prompt_template_id" class="text-sm font-medium text-gray-700">Prompt Template</label>
                    <select id="prompt_template_id" name="prompt_template_id"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All templates</option>
                        @foreach ($promptTemplates as $promptTemplate)
                            <option value="{{ $promptTemplate->id }}" @selected((string) ($filters['prompt_template_id'] ?? '') === (string) $promptTemplate->id)>
                                {{ $promptTemplate->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-3 md:col-span-2 xl:col-span-5">
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Apply Filters
                    </button>

                    <a href="{{ route('plans.index') }}"
                        class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Period Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Provider / Model</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Publishing</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Created</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($strategyPlans as $strategyPlan)
                            <tr>
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-medium text-gray-900">{{ $strategyPlan->title ?: '-' }}</div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $strategyPlan->promptTemplate?->title ?? 'No prompt template' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $availablePeriodTypes[$strategyPlan->period_type] ?? $strategyPlan->period_type }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ trim(($strategyPlan->provider_name ?: '-') . ' / ' . ($strategyPlan->model_name ?: '-')) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @include('partials.ai.status-badge', [
                                        'status' => $strategyPlan->status,
                                    ])
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="space-y-1">
                                        @include('partials.ai.publish-badge', [
                                            'isPublished' => $strategyPlan->is_published,
                                        ])

                                        <div class="text-xs text-gray-500">
                                            {{ $strategyPlan->published_at?->format('Y-m-d H:i') ?: 'Not published yet' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $strategyPlan->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('plans.show', $strategyPlan) }}"
                                            class="font-medium text-slate-700 hover:text-slate-900">View</a>
                                        <a href="{{ route('plans.package', $strategyPlan) }}"
                                            class="font-medium text-slate-700 hover:text-slate-900">Package</a>
                                        <a href="{{ route('plans.export-text', $strategyPlan) }}"
                                            class="font-medium text-slate-700 hover:text-slate-900">Export Text</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No plans matched the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($strategyPlans->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $strategyPlans->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
