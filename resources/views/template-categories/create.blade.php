@extends('layouts.admin')

@section('title', 'Create Template Category')
@section('page-title', 'Create template category')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Create Template Category</h2>
            <p class="mt-1 text-sm text-gray-600">Add a category for organizing templates.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-6">
                @include('template-categories.partials.form', [
                    'action' => route('template-categories.store'),
                    'templateCategory' => null,
                    'submitLabel' => 'Save category',
                ])
            </div>
        </div>
    </div>
@endsection
