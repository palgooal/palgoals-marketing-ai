<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <x-input-label for="title" :value="__('Title')" />
            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $brandService?->title)" required />
            <x-input-error class="mt-2" :messages="$errors->get('title')" />
        </div>

        <div>
            <x-input-label for="slug" :value="__('Slug')" />
            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $brandService?->slug)" required />
            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
        </div>
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $brandService?->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div>
        <x-input-label for="audience" :value="__('Audience')" />
        <x-text-input id="audience" name="audience" type="text" class="mt-1 block w-full" :value="old('audience', $brandService?->audience)" />
        <x-input-error class="mt-2" :messages="$errors->get('audience')" />
    </div>

    <div>
        <x-input-label for="benefits_json" :value="__('Benefits')" />
        <p class="mt-1 text-xs text-gray-500">Enter one item per line.</p>
        <textarea id="benefits_json" name="benefits_json" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('benefits_json', implode(PHP_EOL, $brandService?->benefits_json ?? [])) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('benefits_json')" />
    </div>

    <div>
        <x-input-label for="problems_solved_json" :value="__('Problems Solved')" />
        <p class="mt-1 text-xs text-gray-500">Enter one item per line.</p>
        <textarea id="problems_solved_json" name="problems_solved_json" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('problems_solved_json', implode(PHP_EOL, $brandService?->problems_solved_json ?? [])) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('problems_solved_json')" />
    </div>

    <div>
        <x-input-label for="pricing_notes" :value="__('Pricing Notes')" />
        <textarea id="pricing_notes" name="pricing_notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('pricing_notes', $brandService?->pricing_notes) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('pricing_notes')" />
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="draft" @selected(old('status', $brandService?->status ?? 'draft') === 'draft')>Draft</option>
                <option value="active" @selected(old('status', $brandService?->status) === 'active')>Active</option>
                <option value="archived" @selected(old('status', $brandService?->status) === 'archived')>Archived</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('status')" />
        </div>

        <div>
            <x-input-label for="sort_order" :value="__('Sort Order')" />
            <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full" :value="old('sort_order', $brandService?->sort_order ?? 0)" required />
            <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
        </div>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('services.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Back to services</a>

        <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
            {{ $submitLabel }}
        </button>
    </div>
</form>
