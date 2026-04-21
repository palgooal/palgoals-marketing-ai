@extends('layouts.admin')

@section('title', 'Content')
@section('page-title', 'Content generation')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Content</h2>
                <p class="mt-1 text-sm text-gray-600">Recent generated marketing content using the current prompt foundation.</p>
            </div>

            <a href="{{ route('content.create') }}" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                New content generation
            </a>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('content.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div>
                    <label for="search" class="text-sm font-medium text-gray-700">Title Search</label>
                    <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700" placeholder="Search titles" />
                </div>

                <div>
                    <label for="type" class="text-sm font-medium text-gray-700">Type</label>
                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All types</option>
                        @foreach ($availableTypes as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All statuses</option>
                        @foreach ($availableStatuses as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="language" class="text-sm font-medium text-gray-700">Language</label>
                    <select id="language" name="language" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All languages</option>
                        @foreach ($availableLanguages as $language)
                            <option value="{{ $language }}" @selected(($filters['language'] ?? '') === $language)>{{ $language }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="prompt_template_id" class="text-sm font-medium text-gray-700">Prompt Template</label>
                    <select id="prompt_template_id" name="prompt_template_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                        <option value="">All templates</option>
                        @foreach ($promptTemplates as $promptTemplate)
                            <option value="{{ $promptTemplate->id }}" @selected((string) ($filters['prompt_template_id'] ?? '') === (string) $promptTemplate->id)>{{ $promptTemplate->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-3 md:col-span-2 xl:col-span-5">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Apply Filters
                    </button>

                    <a href="{{ route('content.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
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
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Provider / Model</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($contentGenerations as $contentGeneration)
                            @php
                                $statusClasses = match ($contentGeneration->status) {
                                    'completed' => 'bg-green-100 text-green-700',
                                    'failed' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $contentGeneration->type }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-medium text-gray-900">{{ $contentGeneration->title ?: '-' }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $contentGeneration->promptTemplate?->title ?? 'No prompt template' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ trim(($contentGeneration->provider_name ?: '-') . ' / ' . ($contentGeneration->model_name ?: '-')) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                        {{ ucfirst($contentGeneration->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $contentGeneration->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('content.show', $contentGeneration) }}" class="font-medium text-slate-700 hover:text-slate-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No content matched the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($contentGenerations->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $contentGenerations->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
