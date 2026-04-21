<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $templateCategory?->name)" required />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="slug" :value="__('Slug')" />
            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $templateCategory?->slug)" required />
            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
        </div>
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $templateCategory?->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="active" @selected(old('status', $templateCategory?->status ?? 'active') === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $templateCategory?->status) === 'inactive')>Inactive</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('status')" />
        </div>

        <div>
            <x-input-label for="sort_order" :value="__('Sort Order')" />
            <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full" :value="old('sort_order', $templateCategory?->sort_order ?? 0)" required />
            <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
        </div>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('template-categories.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Back to template categories</a>

        <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
            {{ $submitLabel }}
        </button>
    </div>
</form>
