<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div>
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $knowledgeDocument?->title)" required />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <x-input-label for="type" :value="__('Type')" />
            <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="internal_note" @selected(old('type', $knowledgeDocument?->type ?? 'internal_note') === 'internal_note')>Internal Note</option>
                <option value="reference" @selected(old('type', $knowledgeDocument?->type) === 'reference')>Reference</option>
                <option value="faq" @selected(old('type', $knowledgeDocument?->type) === 'faq')>FAQ</option>
                <option value="policy" @selected(old('type', $knowledgeDocument?->type) === 'policy')>Policy</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('type')" />
        </div>

        <div>
            <x-input-label for="source" :value="__('Source')" />
            <x-text-input id="source" name="source" type="text" class="mt-1 block w-full" :value="old('source', $knowledgeDocument?->source)" />
            <x-input-error class="mt-2" :messages="$errors->get('source')" />
        </div>
    </div>

    <div>
        <x-input-label for="content" :value="__('Content')" />
        <textarea id="content" name="content" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content', $knowledgeDocument?->content) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('content')" />
    </div>

    <div>
        <x-input-label for="metadata_json" :value="__('Metadata')" />
        <p class="mt-1 text-xs text-gray-500">Enter one `key:value` pair per line.</p>
        <textarea id="metadata_json" name="metadata_json" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('metadata_json', collect($knowledgeDocument?->metadata_json ?? [])->map(fn ($value, $key) => $key.':'.$value)->implode(PHP_EOL)) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('metadata_json')" />
    </div>

    <div class="flex items-center gap-3">
        <input type="hidden" name="is_active" value="0">
        <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-slate-900 shadow-sm focus:ring-slate-700" @checked(old('is_active', ($knowledgeDocument?->is_active ?? true) ? '1' : '0') === '1')>
        <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
    </div>
    <x-input-error class="mt-2" :messages="$errors->get('is_active')" />

    <div class="flex items-center justify-between">
        <a href="{{ route('knowledge-documents.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Back to knowledge documents</a>

        <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
            {{ $submitLabel }}
        </button>
    </div>
</form>
