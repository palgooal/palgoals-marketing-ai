@extends('layouts.admin')

@section('title', 'Create Knowledge Document')
@section('page-title', 'Create knowledge document')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Create Knowledge Document</h2>
            <p class="mt-1 text-sm text-gray-600">Add an internal note or reference document for the organization.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-6">
                @include('knowledge-documents.partials.form', [
                    'action' => route('knowledge-documents.store'),
                    'knowledgeDocument' => null,
                    'submitLabel' => 'Save document',
                ])
            </div>
        </div>
    </div>
@endsection
