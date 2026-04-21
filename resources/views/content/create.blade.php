@extends('layouts.admin')

@section('title', 'Create Content')
@section('page-title', 'Create content')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Create Content</h2>
            <p class="mt-1 text-sm text-gray-600">Generate one marketing text output using an active prompt template.</p>
        </div>

        @if ($sourceContentGeneration)
            <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                Prefilled from a previous generation.
                <a href="{{ route('content.show', $sourceContentGeneration) }}" class="font-medium underline underline-offset-2">Review the source result</a>
                before running it again if needed.
            </div>
        @endif

        @if ($errors->has('generation'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first('generation') }}
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-6">
                <form method="POST" action="{{ route('content.generate') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="prompt_template_id" :value="__('Prompt Template')" />
                        <p class="mt-1 text-xs text-gray-500">Only active prompt templates are available for new runs.</p>
                        <select id="prompt_template_id" name="prompt_template_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select a prompt template</option>
                            @foreach ($promptTemplates as $promptTemplate)
                                <option value="{{ $promptTemplate->id }}" @selected((string) old('prompt_template_id', $defaults['prompt_template_id']) === (string) $promptTemplate->id)>
                                    {{ $promptTemplate->title }} ({{ $promptTemplate->key }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('prompt_template_id')" />
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="type" :value="__('Content Type')" />
                            <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @foreach ($contentTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(old('type', $defaults['type']) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('type')" />
                        </div>

                        <div>
                            <x-input-label for="language" :value="__('Output Language')" />
                            <x-text-input id="language" name="language" type="text" class="mt-1 block w-full" :value="old('language', $defaults['language'])" required />
                            <x-input-error class="mt-2" :messages="$errors->get('language')" />
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $defaults['title'])" />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="tone" :value="__('Tone')" />
                            <x-text-input id="tone" name="tone" type="text" class="mt-1 block w-full" :value="old('tone', $defaults['tone'])" />
                            <x-input-error class="mt-2" :messages="$errors->get('tone')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="context" :value="__('Optional Context')" />
                        <p class="mt-1 text-xs text-gray-500">Add short business context here if you do not want to place it directly inside the JSON payload.</p>
                        <textarea id="context" name="context" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('context', $defaults['context']) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('context')" />
                    </div>

                    <div>
                        <x-input-label for="input_payload" :value="__('Input Payload (JSON Object)')" />
                        <p class="mt-1 text-xs text-gray-500">Example: <code>{"audience":"small businesses","goal":"increase leads"}</code>. Use a JSON object with key/value pairs only.</p>
                        <textarea id="input_payload" name="input_payload" rows="10" class="mt-1 block w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('input_payload', $defaults['input_payload']) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('input_payload')" />
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('content.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Back to content</a>

                        <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Generate content
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
