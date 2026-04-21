@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Application settings')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Settings</h2>
            <p class="mt-1 text-sm text-gray-600">Lightweight internal defaults stored in the `settings` table.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">General</h3>
            </div>

            <div class="px-6 py-6">
                @if (session('status'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="app_name" :value="__('App Name')" />
                        <x-text-input id="app_name" name="app_name" type="text" class="mt-1 block w-full" :value="old('app_name', $settings['app_name'])" required />
                        <x-input-error class="mt-2" :messages="$errors->get('app_name')" />
                    </div>

                    <div>
                        <x-input-label for="support_email" :value="__('Support Email')" />
                        <x-text-input id="support_email" name="support_email" type="email" class="mt-1 block w-full" :value="old('support_email', $settings['support_email'])" />
                        <x-input-error class="mt-2" :messages="$errors->get('support_email')" />
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="default_primary_language" :value="__('Default Primary Language')" />
                            <x-text-input id="default_primary_language" name="default_primary_language" type="text" class="mt-1 block w-full" :value="old('default_primary_language', $settings['default_primary_language'])" required />
                            <x-input-error class="mt-2" :messages="$errors->get('default_primary_language')" />
                        </div>

                        <div>
                            <x-input-label for="default_secondary_language" :value="__('Default Secondary Language')" />
                            <x-text-input id="default_secondary_language" name="default_secondary_language" type="text" class="mt-1 block w-full" :value="old('default_secondary_language', $settings['default_secondary_language'])" />
                            <x-input-error class="mt-2" :messages="$errors->get('default_secondary_language')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Save settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
