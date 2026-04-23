@extends('layouts.admin')

@section('title', 'Edit Prompt Template')
@section('page-title', 'Edit prompt template')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Edit Prompt Template</h2>
            <p class="mt-1 text-sm text-gray-600">Update the prompt template used by AI workflows.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-6 py-4 text-sm text-green-700 shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-700 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h3 class="font-semibold text-slate-900">Prompt authoring guidance</h3>
                    <p class="mt-2">Use the system prompt for stable behavioral instructions and the user prompt template
                        for runtime placeholders.</p>
                    <p class="mt-2">Supported placeholders include <code>@{{ title }}</code>,
                        <code>@{{ type }}</code>, <code>@{{ language }}</code>, and
                        <code>@{{ tone }}</code>. Some workflows may also pass additional payload or context
                        fields such as <code>@{{ context }}</code>, <code>@{{ goals }}</code>, or
                        page-specific fields.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span
                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $promptTemplate->organization_id ? 'bg-slate-200 text-slate-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $promptTemplate->organization_id ? 'Organization' : 'Global' }}
                    </span>

                    <div
                        class="inline-flex rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                        Current Version: {{ $promptTemplate->version }}
                    </div>

                    <form method="POST" action="{{ route('prompts.toggle-active', $promptTemplate) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                            {{ $promptTemplate->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('prompts.duplicate', $promptTemplate) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Duplicate Draft
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Usage Summary</h3>
                    <p class="mt-1 text-sm text-gray-600">Lightweight usage insight for this prompt template across all
                        existing workflows.</p>
                </div>

                <div class="text-sm text-gray-500">
                    Last Used:
                    <span
                        class="font-medium text-gray-700">{{ $usageSummary['last_used_at']?->format('Y-m-d H:i') ?: 'Not used yet' }}</span>
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-5">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Usage</div>
                    <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $usageSummary['total'] }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Content Usage</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">{{ $usageSummary['content'] }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Offers Usage</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">{{ $usageSummary['offers'] }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Plans Usage</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">{{ $usageSummary['plans'] }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Analysis Usage</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">{{ $usageSummary['analysis'] }}</div>
                </div>
            </div>
        </div>

        <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Recent Usage</h3>
            </div>

            <div class="px-6 py-4">
                @if ($recentUsage === [])
                    <p class="text-sm text-gray-500">No recent usage yet for this prompt template.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($recentUsage as $usage)
                            @include('prompts.partials.recent-usage-item', ['usage' => $usage])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Content Usage</div>
                <div class="mt-2 text-2xl font-semibold text-gray-900">{{ $promptTemplate->content_generations_count }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Offers Usage</div>
                <div class="mt-2 text-2xl font-semibold text-gray-900">{{ $promptTemplate->offer_generations_count }}</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Plans Usage</div>
                <div class="mt-2 text-2xl font-semibold text-gray-900">{{ $promptTemplate->strategy_plans_count }}</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Analysis Usage</div>
                <div class="mt-2 text-2xl font-semibold text-gray-900">{{ $promptTemplate->page_analyses_count }}</div>
            </div>
        </div>

        <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Recent Versions</h3>
            </div>

            <div class="px-6 py-4">
                @if ($promptTemplate->versions->isEmpty())
                    <p class="text-sm text-gray-500">No previous snapshots yet. A version snapshot will be created the next
                        time this prompt template is updated.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($promptTemplate->versions as $version)
                            @include('prompts.partials.version-row', [
                                'promptTemplate' => $promptTemplate,
                                'version' => $version,
                            ])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-6">
                <form method="POST" action="{{ route('prompts.update', $promptTemplate) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                :value="old('title', $promptTemplate->title)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="key" :value="__('Key')" />
                            <x-text-input id="key" name="key" type="text" class="mt-1 block w-full"
                                :value="old('key', $promptTemplate->key)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('key')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $promptTemplate->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="module" :value="__('Module')" />
                            <select id="module" name="module"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-700 focus:ring-slate-700"
                                required>
                                @foreach (['content' => 'Content', 'offers' => 'Offers', 'plans' => 'Plans', 'analysis' => 'Analysis'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('module', $promptTemplate->module) === $value)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('module')" />
                        </div>

                        <div>
                            <x-input-label for="version" :value="__('Version')" />
                            <p class="mt-1 text-xs text-gray-500">This value increments automatically when you save
                                changes.
                                The current value is shown for reference.</p>
                            <x-text-input id="version" name="version" type="number" min="1"
                                class="mt-1 block w-full bg-gray-50" :value="old('version', $promptTemplate->version)" required readonly />
                            <x-input-error class="mt-2" :messages="$errors->get('version')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="system_prompt" :value="__('System Prompt')" />
                        <p class="mt-1 text-xs text-gray-500">Use this for the persistent role, guardrails, and tone of the
                            assistant.</p>
                        <textarea id="system_prompt" name="system_prompt" rows="6"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('system_prompt', $promptTemplate->system_prompt) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('system_prompt')" />
                    </div>

                    <div>
                        <x-input-label for="user_prompt_template" :value="__('User Prompt Template')" />
                        <p class="mt-1 text-xs text-gray-500">Use this template for runtime fields. Available placeholders
                            include <code>@{{ title }}</code>, <code>@{{ type }}</code>,
                            <code>@{{ language }}</code>, and <code>@{{ tone }}</code>. Additional payload
                            or context values may also be available depending on the workflow.
                        </p>
                        <textarea id="user_prompt_template" name="user_prompt_template" rows="10"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('user_prompt_template', $promptTemplate->user_prompt_template) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('user_prompt_template')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" name="is_active" type="checkbox" value="1"
                            class="rounded border-gray-300 text-slate-900 shadow-sm focus:ring-slate-700"
                            @checked(old('is_active', $promptTemplate->is_active ? '1' : '0') === '1')>
                        <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('prompts.index') }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900">Back to prompt templates</a>

                        <button type="submit"
                            class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Update prompt template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
