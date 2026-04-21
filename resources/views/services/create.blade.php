@extends('layouts.admin')

@section('title', 'Create Service')
@section('page-title', 'Create service')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Create Service</h2>
            <p class="mt-1 text-sm text-gray-600">Add a new service entry to the brand knowledge base.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-6">
                @include('services.partials.form', [
                    'action' => route('services.store'),
                    'brandService' => null,
                    'submitLabel' => 'Save service',
                ])
            </div>
        </div>
    </div>
@endsection
