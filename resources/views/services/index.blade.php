@extends('layouts.admin')

@section('title', 'Services')
@section('page-title', 'Brand services')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Services</h2>
                <p class="mt-1 text-sm text-gray-600">Manage organization-aware service definitions for the brand knowledge base.</p>
            </div>

            <a href="{{ route('services.create') }}" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                New service
            </a>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            @if (session('status'))
                <div class="border-b border-green-200 bg-green-50 px-6 py-4 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route('services.index') }}" class="grid gap-4 border-b border-gray-200 px-6 py-4 md:grid-cols-[1fr_220px_auto]">
                <div>
                    <x-input-label for="search" :value="__('Search')" />
                    <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="request('search')" placeholder="Search by title" />
                </div>

                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All statuses</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="archived" @selected(request('status') === 'archived')>Archived</option>
                    </select>
                </div>

                <div class="flex items-end gap-3">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Filter
                    </button>

                    <a href="{{ route('services.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Reset</a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Sort</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($brandServices as $brandService)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $brandService->title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $brandService->slug }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($brandService->status) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $brandService->sort_order }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('services.edit', $brandService) }}" class="font-medium text-slate-700 hover:text-slate-900">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No services have been created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($brandServices->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $brandServices->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
