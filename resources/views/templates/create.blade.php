@extends('layouts.admin')

@section('title', 'Create Template')
@section('page-title', 'Create template')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Create Template</h2>
            <p class="mt-1 text-sm text-gray-600">Add a reusable template record for the current organization.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-6">
                @include('templates.partials.form', [
                    'action' => route('templates.store'),
                    'template' => null,
                    'templateCategories' => $templateCategories,
                    'submitLabel' => 'Save template',
                ])
            </div>
        </div>
    </div>
@endsection
