@extends('layouts.admin')

@section('title', 'Edit Template Category')
@section('page-title', 'Edit template category')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Edit Template Category</h2>
            <p class="mt-1 text-sm text-gray-600">Update the selected template category.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-6">
                @include('template-categories.partials.form', [
                    'action' => route('template-categories.update', $templateCategory),
                    'method' => 'PUT',
                    'templateCategory' => $templateCategory,
                    'submitLabel' => 'Update category',
                ])
            </div>
        </div>
    </div>
@endsection
