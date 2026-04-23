@extends('layouts.admin')

@section('title', 'AI Logs')
@section('page-title', 'Internal AI logs')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">AI Logs</h2>
                <p class="mt-1 text-sm text-gray-600">Recent internal AI request records across the current workflows.</p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('logs.ai-requests.index') }}"
                class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                <div>
                    <label for="search" class="text-sm font-medium text-gray-700">Search</label>
                    <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700"
                        placeholder="Module or task type" />
                </div>

                <div>
                    <label for="module" class="text-sm font-medium text-gray-700">Module</label>
                    <select id="module" name="module"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All modules</option>
                        @foreach ($modules as $module)
                            <option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>{{ $module }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="task_type" class="text-sm font-medium text-gray-700">Task Type</label>
                    <select id="task_type" name="task_type"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All task types</option>
                        @foreach ($taskTypes as $taskType)
                            <option value="{{ $taskType }}" @selected(($filters['task_type'] ?? '') === $taskType)>{{ $taskType }}</option>
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
                    <label for="provider_name" class="text-sm font-medium text-gray-700">Provider</label>
                    <select id="provider_name" name="provider_name"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All providers</option>
                        @foreach ($providerNames as $providerName)
                            <option value="{{ $providerName }}" @selected(($filters['provider_name'] ?? '') === $providerName)>{{ $providerName }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="health" class="text-sm font-medium text-gray-700">Health</label>
                    <select id="health" name="health"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All requests</option>
                        @foreach ($availableHealthFilters as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['health'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-3 md:col-span-2 xl:col-span-6">
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Apply Filters
                    </button>

                    <a href="{{ route('logs.ai-requests.index') }}"
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
                                Module</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Task
                                Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Provider / Model</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Health</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Latency</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Created</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($aiRequests as $aiRequest)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $aiRequest->module }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $aiRequest->task_type }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ trim($aiRequest->provider_name . ' / ' . $aiRequest->model_name) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @include('partials.ai.status-badge', ['status' => $aiRequest->status])
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="flex flex-wrap gap-2">
                                        @if ($aiRequest->status === 'failed')
                                            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Failed</span>
                                        @endif

                                        @if ($aiWorkflowHealthInsights->isSlowRequest($aiRequest->latency_ms))
                                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Slow</span>
                                        @endif

                                        @if ($aiWorkflowHealthInsights->hasMissingOutputOrError($aiRequest->output_payload, $aiRequest->error_message))
                                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">Missing Output / Error</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="space-y-1">
                                        <div>{{ $aiRequest->latency_ms !== null ? $aiRequest->latency_ms . ' ms' : '-' }}</div>
                                        <div class="text-xs text-gray-500">Slow at {{ $slowRequestMs }}+ ms</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $aiRequest->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('logs.ai-requests.show', $aiRequest) }}"
                                        class="font-medium text-slate-700 hover:text-slate-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No AI logs matched the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($aiRequests->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $aiRequests->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
