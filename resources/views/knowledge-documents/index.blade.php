@extends('layouts.admin')

@section('title', 'Knowledge Documents')
@section('page-title', 'Knowledge documents')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Knowledge Documents</h2>
                <p class="mt-1 text-sm text-gray-600">Manage organization-aware notes and reference documents.</p>
            </div>

            <a href="{{ route('knowledge-documents.create') }}" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                New document
            </a>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            @if (session('status'))
                <div class="border-b border-green-200 bg-green-50 px-6 py-4 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route('knowledge-documents.index') }}" class="grid gap-4 border-b border-gray-200 px-6 py-4 md:grid-cols-[1fr_220px_auto]">
                <div>
                    <x-input-label for="search" :value="__('Search')" />
                    <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="request('search')" placeholder="Search by title" />
                </div>

                <div>
                    <x-input-label for="activity" :value="__('Active Status')" />
                    <select id="activity" name="activity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All documents</option>
                        <option value="active" @selected(request('activity') === 'active')>Active</option>
                        <option value="inactive" @selected(request('activity') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div class="flex items-end gap-3">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Filter
                    </button>

                    <a href="{{ route('knowledge-documents.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Reset</a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Source</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Active</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Updated</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($knowledgeDocuments as $knowledgeDocument)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $knowledgeDocument->title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ str_replace('_', ' ', $knowledgeDocument->type) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $knowledgeDocument->source ?: '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $knowledgeDocument->is_active ? 'Active' : 'Inactive' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $knowledgeDocument->updated_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('knowledge-documents.edit', $knowledgeDocument) }}" class="font-medium text-slate-700 hover:text-slate-900">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No knowledge documents have been created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($knowledgeDocuments->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $knowledgeDocuments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
