@extends('layouts.admin')

@section('title', 'AI Test')
@section('page-title', 'Internal AI test')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">AI Test</h2>
            <p class="mt-1 text-sm text-gray-600">Internal verification screen for the initial AI foundation. Use it to send a simple module, task type, and JSON payload.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-6">
                <form method="POST" action="{{ route('ai.test.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="module" :value="__('Module')" />
                            <x-text-input id="module" name="module" type="text" class="mt-1 block w-full" :value="old('module', $module)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('module')" />
                        </div>

                        <div>
                            <x-input-label for="task_type" :value="__('Task Type')" />
                            <x-text-input id="task_type" name="task_type" type="text" class="mt-1 block w-full" :value="old('task_type', $taskType)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('task_type')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="payload" :value="__('Payload JSON')" />
                        <textarea id="payload" name="payload" rows="14" class="mt-1 block w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('payload', $payloadText) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('payload')" />
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Run AI test
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if ($error)
            <div class="rounded-xl border border-red-200 bg-red-50 px-6 py-5 text-sm text-red-700">
                <h3 class="font-semibold text-red-900">Request failed</h3>
                <p class="mt-2">{{ $error }}</p>
            </div>
        @endif

        @if ($response)
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Normalized Response</h3>
                    @if ($loggedRequestId)
                        <p class="mt-1 text-sm text-gray-600">Logged AI request ID: {{ $loggedRequestId }}</p>
                    @endif
                </div>

                <div class="px-6 py-6">
                    <pre class="overflow-x-auto rounded-lg bg-slate-950 p-4 text-sm text-slate-100">{{ json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        @endif
    </div>
@endsection
