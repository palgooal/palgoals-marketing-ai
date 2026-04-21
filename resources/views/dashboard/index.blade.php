@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Overview')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Dashboard</h2>
            <p class="mt-1 text-sm text-gray-600">Internal overview for the current Palgoals workspace.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Organization Name</p>
                <p class="mt-3 text-lg font-semibold text-gray-900">{{ $organization?->name ?? 'Not available' }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Brand Name</p>
                <p class="mt-3 text-lg font-semibold text-gray-900">{{ $organization?->brandProfile?->brand_name ?? 'Not available' }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Primary Language</p>
                <p class="mt-3 text-lg font-semibold text-gray-900">{{ $organization?->brandProfile?->primary_language ?? 'Not available' }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Project</p>
                <p class="mt-3 text-lg font-semibold text-gray-900">Palgoals Marketing AI</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Current Workspace</h3>
                <dl class="mt-4 space-y-4 text-sm text-gray-700">
                    <div>
                        <dt class="font-medium text-gray-500">Organization</dt>
                        <dd class="mt-1">{{ $organization?->name ?? 'Not available' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Brand</dt>
                        <dd class="mt-1">{{ $organization?->brandProfile?->brand_name ?? 'Not available' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Brand Summary</dt>
                        <dd class="mt-1">{{ $organization?->brandProfile?->short_description ?? 'No short description has been set yet.' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Quick Links</h3>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('brand.edit') }}" class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Edit Brand Profile
                    </a>

                    <a href="{{ route('services.index') }}" class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Services
                    </a>

                    <a href="{{ route('template-categories.index') }}" class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Template Categories
                    </a>

                    <a href="{{ route('templates.index') }}" class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Templates
                    </a>

                    <a href="{{ route('knowledge-documents.index') }}" class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Knowledge Documents
                    </a>

                    <a href="{{ route('settings.edit') }}" class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
