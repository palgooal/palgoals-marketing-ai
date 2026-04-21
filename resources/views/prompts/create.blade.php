@extends('layouts.admin')

@section('title', 'Create Prompt Template')
@section('page-title', 'Create prompt template')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Create Prompt Template</h2>
            <p class="mt-1 text-sm text-gray-600">Add a reusable prompt template for content generation workflows.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-6">
                <form method="POST" action="{{ route('prompts.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="key" :value="__('Key')" />
                            <x-text-input id="key" name="key" type="text" class="mt-1 block w-full" :value="old('key')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('key')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="module" :value="__('Module')" />
                            <x-text-input id="module" name="module" type="text" class="mt-1 block w-full" :value="old('module', 'content')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('module')" />
                        </div>

                        <div>
                            <x-input-label for="version" :value="__('Version')" />
                            <x-text-input id="version" name="version" type="number" min="1" class="mt-1 block w-full" :value="old('version', 1)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('version')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="system_prompt" :value="__('System Prompt')" />
                        <textarea id="system_prompt" name="system_prompt" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('system_prompt') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('system_prompt')" />
                    </div>

                    <div>
                        <x-input-label for="user_prompt_template" :value="__('User Prompt Template')" />
                        <p class="mt-1 text-xs text-gray-500">Only simple placeholder replacement is supported here. Available placeholders include <code>@{{title}}</code>, <code>@{{type}}</code>, <code>@{{language}}</code>, and <code>@{{tone}}</code>.</p>
                        <textarea id="user_prompt_template" name="user_prompt_template" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('user_prompt_template') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('user_prompt_template')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-slate-900 shadow-sm focus:ring-slate-700" @checked(old('is_active', '1') === '1')>
                        <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('prompts.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Back to prompt templates</a>

                        <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Save prompt template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
