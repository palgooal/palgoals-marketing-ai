<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $template?->name)" required />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="slug" :value="__('Slug')" />
            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $template?->slug)" required />
            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
        </div>
    </div>

    <div>
        <x-input-label for="template_category_id" :value="__('Template Category')" />
        <select id="template_category_id" name="template_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">No category</option>
            @foreach ($templateCategories as $templateCategory)
                <option value="{{ $templateCategory->id }}" @selected((string) old('template_category_id', $template?->template_category_id) === (string) $templateCategory->id)>
                    {{ $templateCategory->name }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('template_category_id')" />
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $template?->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div>
        <x-input-label for="audience" :value="__('Audience')" />
        <x-text-input id="audience" name="audience" type="text" class="mt-1 block w-full" :value="old('audience', $template?->audience)" />
        <x-input-error class="mt-2" :messages="$errors->get('audience')" />
    </div>

    <div>
        <x-input-label for="features_json" :value="__('Features')" />
        <p class="mt-1 text-xs text-gray-500">Enter one item per line.</p>
        <textarea id="features_json" name="features_json" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('features_json', implode(PHP_EOL, $template?->features_json ?? [])) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('features_json')" />
    </div>

    <div>
        <x-input-label for="benefits_json" :value="__('Benefits')" />
        <p class="mt-1 text-xs text-gray-500">Enter one item per line.</p>
        <textarea id="benefits_json" name="benefits_json" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('benefits_json', implode(PHP_EOL, $template?->benefits_json ?? [])) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('benefits_json')" />
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <x-input-label for="price" :value="__('Price')" />
            <x-text-input id="price" name="price" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="old('price', $template?->price)" />
            <x-input-error class="mt-2" :messages="$errors->get('price')" />
        </div>

        <div>
            <x-input-label for="sale_price" :value="__('Sale Price')" />
            <x-text-input id="sale_price" name="sale_price" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="old('sale_price', $template?->sale_price)" />
            <x-input-error class="mt-2" :messages="$errors->get('sale_price')" />
        </div>
    </div>

    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="draft" @selected(old('status', $template?->status ?? 'draft') === 'draft')>Draft</option>
            <option value="active" @selected(old('status', $template?->status) === 'active')>Active</option>
            <option value="archived" @selected(old('status', $template?->status) === 'archived')>Archived</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('templates.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Back to templates</a>

        <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
            {{ $submitLabel }}
        </button>
    </div>
</form>
