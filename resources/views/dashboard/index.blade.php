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
                <p class="mt-3 text-lg font-semibold text-gray-900">
                    {{ $organization?->brandProfile?->brand_name ?? 'Not available' }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Primary Language</p>
                <p class="mt-3 text-lg font-semibold text-gray-900">
                    {{ $organization?->brandProfile?->primary_language ?? 'Not available' }}</p>
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
                        <dd class="mt-1">
                            {{ $organization?->brandProfile?->short_description ?? 'No short description has been set yet.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Quick Links</h3>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('brand.edit') }}"
                        class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Edit Brand Profile
                    </a>

                    <a href="{{ route('services.index') }}"
                        class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Services
                    </a>

                    <a href="{{ route('template-categories.index') }}"
                        class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Template Categories
                    </a>

                    <a href="{{ route('templates.index') }}"
                        class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Templates
                    </a>

                    <a href="{{ route('knowledge-documents.index') }}"
                        class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Knowledge Documents
                    </a>

                    <a href="{{ route('settings.edit') }}"
                        class="block rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                        Manage Settings
                    </a>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Workflow Health</h3>
                    <p class="mt-1 text-sm text-gray-600">Compact internal health counts across AI requests and workflow outputs.</p>
                </div>

                <a href="{{ route('logs.ai-requests.index') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">
                    Review AI Logs
                </a>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Failed AI Requests</p>
                    <p class="mt-2 text-2xl font-semibold text-red-900">{{ $workflowHealth['failed'] }}</p>
                </div>

                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Successful AI Requests</p>
                    <p class="mt-2 text-2xl font-semibold text-emerald-900">{{ $workflowHealth['successful'] }}</p>
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Draft Outputs</p>
                    <p class="mt-2 text-2xl font-semibold text-amber-900">{{ $reviewSummary['draft'] }}</p>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-700">Unpublished Outputs</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $publishingSummary['unpublished'] }}</p>
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Reviewed Outputs</p>
                    <p class="mt-2 text-2xl font-semibold text-blue-900">{{ $reviewSummary['reviewed'] }}</p>
                </div>

                <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Approved Outputs</p>
                    <p class="mt-2 text-2xl font-semibold text-green-900">{{ $reviewSummary['approved'] }}</p>
                </div>

                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Published Outputs</p>
                    <p class="mt-2 text-2xl font-semibold text-emerald-900">{{ $publishingSummary['published'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Stale Drafts</h3>
                    <p class="mt-1 text-sm text-gray-600">Draft outputs older than {{ $staleDraftInsights['days'] }} days that may need review or cleanup.</p>
                </div>

                <div class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                    Stale Draft
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-[220px_1fr]">
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Older Than {{ $staleDraftInsights['days'] }} Days</p>
                    <p class="mt-2 text-2xl font-semibold text-amber-900">{{ $staleDraftInsights['count'] }}</p>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-4">
                    @if ($staleDraftInsights['links'] === [])
                        <p class="text-sm text-gray-500">No stale draft outputs right now.</p>
                    @else
                        <div class="flex flex-wrap gap-3">
                            @foreach ($staleDraftInsights['links'] as $link)
                                <a href="{{ $link['url'] }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                                    {{ $link['label'] }} · {{ $link['count'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">AI Activity</h3>
                    <p class="mt-1 text-sm text-gray-600">Quick access to the latest AI workflow outputs.</p>
                </div>

                <a href="{{ route('logs.ai-requests.index') }}"
                    class="text-sm font-medium text-slate-700 hover:text-slate-900">
                    Open AI Logs
                </a>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg border border-gray-200 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Latest Content</p>
                    <p class="mt-2 text-sm font-medium text-gray-900">
                        {{ $latestContentGeneration?->title ?: 'No content yet' }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $latestContentGeneration?->created_at?->format('Y-m-d H:i') ?: 'No runs available' }}</p>
                    <a href="{{ $latestContentGeneration ? route('content.show', $latestContentGeneration) : route('content.index') }}"
                        class="mt-3 inline-block text-sm font-medium text-slate-700 hover:text-slate-900">Open</a>
                </div>

                <div class="rounded-lg border border-gray-200 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Latest Offer</p>
                    <p class="mt-2 text-sm font-medium text-gray-900">
                        {{ $latestOfferGeneration?->title ?: 'No offers yet' }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $latestOfferGeneration?->created_at?->format('Y-m-d H:i') ?: 'No runs available' }}</p>
                    <a href="{{ $latestOfferGeneration ? route('offers.show', $latestOfferGeneration) : route('offers.index') }}"
                        class="mt-3 inline-block text-sm font-medium text-slate-700 hover:text-slate-900">Open</a>
                </div>

                <div class="rounded-lg border border-gray-200 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Latest Plan</p>
                    <p class="mt-2 text-sm font-medium text-gray-900">{{ $latestStrategyPlan?->title ?: 'No plans yet' }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $latestStrategyPlan?->created_at?->format('Y-m-d H:i') ?: 'No runs available' }}</p>
                    <a href="{{ $latestStrategyPlan ? route('plans.show', $latestStrategyPlan) : route('plans.index') }}"
                        class="mt-3 inline-block text-sm font-medium text-slate-700 hover:text-slate-900">Open</a>
                </div>

                <div class="rounded-lg border border-gray-200 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Latest Analysis</p>
                    <p class="mt-2 text-sm font-medium text-gray-900">
                        {{ $latestPageAnalysis?->page_title ?: 'No analysis yet' }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $latestPageAnalysis?->created_at?->format('Y-m-d H:i') ?: 'No runs available' }}</p>
                    <a href="{{ $latestPageAnalysis ? route('analysis.show', $latestPageAnalysis) : route('analysis.index') }}"
                        class="mt-3 inline-block text-sm font-medium text-slate-700 hover:text-slate-900">Open</a>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Review Summary</h3>
                <p class="mt-1 text-sm text-gray-600">Aggregate review status across content, offers, plans, and analysis
                    outputs.</p>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Draft Outputs</p>
                    <p class="mt-2 text-2xl font-semibold text-amber-900">{{ $reviewSummary['draft'] }}</p>
                </div>

                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Reviewed Outputs</p>
                    <p class="mt-2 text-2xl font-semibold text-blue-900">{{ $reviewSummary['reviewed'] }}</p>
                </div>

                <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Approved Outputs</p>
                    <p class="mt-2 text-2xl font-semibold text-green-900">{{ $reviewSummary['approved'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Publishing Summary</h3>
                <p class="mt-1 text-sm text-gray-600">Internal count of published and unpublished generated outputs.</p>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Published Outputs</p>
                    <p class="mt-2 text-2xl font-semibold text-emerald-900">{{ $publishingSummary['published'] }}</p>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-700">Unpublished Outputs</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $publishingSummary['unpublished'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Prompt Activity</h3>
                    <p class="mt-1 text-sm text-gray-600">Lightweight prompt template usage and readiness snapshot for the
                        current workspace.</p>
                </div>

                <a href="{{ route('prompts.index') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">
                    Open Prompt Templates
                </a>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Total Prompt Templates</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $promptInsights['total'] }}</p>
                </div>

                <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Active Prompt Templates</p>
                    <p class="mt-2 text-2xl font-semibold text-green-900">{{ $promptInsights['active'] }}</p>
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Unused Prompt Templates</p>
                    <p class="mt-2 text-2xl font-semibold text-amber-900">{{ $promptInsights['unused'] }}</p>
                </div>

                <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Unused Active Prompts</p>
                    <p class="mt-2 text-2xl font-semibold text-red-900">{{ $promptInsights['unused_active'] }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
