@extends('layouts.admin')

@section('title', 'Edit Service')
@section('page-title', 'Edit service')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Edit Service</h2>
            <p class="mt-1 text-sm text-gray-600">Update the selected service entry.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-6">
                @include('services.partials.form', [
                    'action' => route('services.update', $brandService),
                    'method' => 'PUT',
                    'brandService' => $brandService,
                    'submitLabel' => 'Update service',
                ])
            </div>
        </div>
    </div>
@endsection
