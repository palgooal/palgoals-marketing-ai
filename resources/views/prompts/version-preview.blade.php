@extends('layouts.admin')

@section('title', 'Prompt Version Preview')
@section('page-title', 'Prompt version preview')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Prompt Version Preview</h2>
                <p class="mt-1 text-sm text-gray-600">Lightweight snapshot preview for the selected prompt template version.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('prompts.edit', $promptTemplate) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Back to Edit
                </a>

                <form method="POST"
                    action="{{ route('prompts.versions.revert', [$promptTemplate, $promptTemplateVersion]) }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Revert to This Version
                    </button>
                </form>
            </div>
        </div>

        @include('prompts.partials.version-preview-block', [
            'promptTemplateVersion' => $promptTemplateVersion,
        ])
    </div>
@endsection
