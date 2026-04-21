@extends('layouts.admin')

@section('title', 'Prompt Templates')
@section('page-title', 'Prompt templates')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Prompt Templates</h2>
                <p class="mt-1 text-sm text-gray-600">Manage reusable prompts for production AI workflows.</p>
            </div>

            <a href="{{ route('prompts.create') }}" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                New prompt template
            </a>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            @if (session('status'))
                <div class="border-b border-green-200 bg-green-50 px-6 py-4 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="border-b border-gray-200 px-6 py-6">
                <form method="GET" action="{{ route('prompts.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="xl:col-span-2">
                        <label for="search" class="text-sm font-medium text-gray-700">Search</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700" placeholder="Search by title or key" />
                    </div>

                    <div>
                        <label for="module" class="text-sm font-medium text-gray-700">Module</label>
                        <select id="module" name="module" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                            <option value="">All modules</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>{{ $module }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="active" class="text-sm font-medium text-gray-700">Status</label>
                        <select id="active" name="active" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-slate-700 focus:ring-slate-700">
                            <option value="">All statuses</option>
                            <option value="1" @selected(($filters['active'] ?? '') === '1')>Active</option>
                            <option value="0" @selected(($filters['active'] ?? '') === '0')>Inactive</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-3 md:col-span-2 xl:col-span-4">
                        <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Apply Filters
                        </button>

                        <a href="{{ route('prompts.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Key</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Module</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Version</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($promptTemplates as $promptTemplate)
                            @php
                                $statusClasses = $promptTemplate->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700';
                            @endphp
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $promptTemplate->title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $promptTemplate->key }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $promptTemplate->module }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $promptTemplate->version }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                        {{ $promptTemplate->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('prompts.edit', $promptTemplate) }}" class="font-medium text-slate-700 hover:text-slate-900">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No prompt templates matched the current filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($promptTemplates->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $promptTemplates->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
