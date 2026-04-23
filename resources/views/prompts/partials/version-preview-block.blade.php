<div class="rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-6 py-4">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Version Snapshot Preview</h3>
    </div>

    <div class="px-6 py-6">
        <dl class="space-y-5 text-sm text-gray-700">
            <div>
                <dt class="font-medium text-gray-500">Version</dt>
                <dd class="mt-1">{{ $promptTemplateVersion->version_number }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Captured</dt>
                <dd class="mt-1">
                    {{ $promptTemplateVersion->created_at?->format('Y-m-d H:i') ?: ($promptTemplateVersion->updated_at?->format('Y-m-d H:i') ?: '-') }}
                </dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Title</dt>
                <dd class="mt-1">{{ $promptTemplateVersion->title }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Description</dt>
                <dd class="mt-1 whitespace-pre-wrap">{{ $promptTemplateVersion->description ?: '-' }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Module</dt>
                <dd class="mt-1">{{ str($promptTemplateVersion->module)->replace('_', ' ')->title() }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">System Prompt</dt>
                <dd class="mt-1 whitespace-pre-wrap rounded-lg bg-gray-50 p-4">
                    {{ $promptTemplateVersion->system_prompt ?: '-' }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">User Prompt Template</dt>
                <dd class="mt-1 whitespace-pre-wrap rounded-lg bg-gray-50 p-4">
                    {{ $promptTemplateVersion->user_prompt_template }}</dd>
            </div>
        </dl>
    </div>
</div>
