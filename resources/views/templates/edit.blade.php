@extends('layouts.admin')

@section('title', 'Edit Template')
@section('page-title', 'Edit template')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Edit Template</h2>
            <p class="mt-1 text-sm text-gray-600">Update the selected template entry.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-6">
                @include('templates.partials.form', [
                    'action' => route('templates.update', $template),
                    'method' => 'PUT',
                    'template' => $template,
                    'templateCategories' => $templateCategories,
                    'submitLabel' => 'Update template',
                ])
            </div>
        </div>
    </div>
@endsection
