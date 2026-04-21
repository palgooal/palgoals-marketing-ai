@extends('layouts.admin')

@section('title', 'Brand Profile')
@section('page-title', 'Brand profile editor')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Brand Profile</h2>
            <p class="mt-1 text-sm text-gray-600">Manage the core internal brand details for {{ $brandProfile->organization->name }}.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <p class="text-sm font-semibold uppercase tracking-wide text-gray-500">Organization</p>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $brandProfile->organization->name }}</p>
            </div>

            <div class="px-6 py-6">
                @if (session('status'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('brand.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="brand_name" :value="__('Brand Name')" />
                        <x-text-input id="brand_name" name="brand_name" type="text" class="mt-1 block w-full" :value="old('brand_name', $brandProfile->brand_name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('brand_name')" />
                    </div>

                    <div>
                        <x-input-label for="short_description" :value="__('Short Description')" />
                        <textarea id="short_description" name="short_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('short_description', $brandProfile->short_description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('short_description')" />
                    </div>

                    <div>
                        <x-input-label for="long_description" :value="__('Long Description')" />
                        <textarea id="long_description" name="long_description" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('long_description', $brandProfile->long_description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('long_description')" />
                    </div>

                    <div>
                        <x-input-label for="tone_of_voice" :value="__('Tone Of Voice')" />
                        <textarea id="tone_of_voice" name="tone_of_voice" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('tone_of_voice', $brandProfile->tone_of_voice) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('tone_of_voice')" />
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="primary_language" :value="__('Primary Language')" />
                            <x-text-input id="primary_language" name="primary_language" type="text" class="mt-1 block w-full" :value="old('primary_language', $brandProfile->primary_language)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('primary_language')" />
                        </div>

                        <div>
                            <x-input-label for="secondary_language" :value="__('Secondary Language')" />
                            <x-text-input id="secondary_language" name="secondary_language" type="text" class="mt-1 block w-full" :value="old('secondary_language', $brandProfile->secondary_language)" />
                            <x-input-error class="mt-2" :messages="$errors->get('secondary_language')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Back to dashboard
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Save brand profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
