<?php

namespace App\Http\Controllers\Templates;

use App\Http\Controllers\Controller;
use App\Http\Requests\Templates\StoreTemplateRequest;
use App\Http\Requests\Templates\UpdateTemplateRequest;
use App\Models\Organization;
use App\Models\Template;
use App\Support\CurrentOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();

        return view('templates.index', [
            'templates' => $organization->templates()
                ->with('templateCategory')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->when(in_array($status, ['draft', 'active', 'archived'], true), function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('templates.create', [
            'templateCategories' => CurrentOrganization::get()->templateCategories()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreTemplateRequest $request): RedirectResponse
    {
        $organization = CurrentOrganization::get();

        $organization->templates()->create(
            $this->normalizeData($request->validated(), $organization),
        );

        return redirect()
            ->route('templates.index')
            ->with('status', 'Template created successfully.');
    }

    public function edit(Template $template): View
    {
        $organization = CurrentOrganization::get();

        return view('templates.edit', [
            'template' => $organization->templates()->findOrFail($template->getKey()),
            'templateCategories' => $organization->templateCategories()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateTemplateRequest $request, Template $template): RedirectResponse
    {
        $organization = CurrentOrganization::get();
        $template = $organization->templates()->findOrFail($template->getKey());

        $template->update(
            $this->normalizeData($request->validated(), $organization),
        );

        return redirect()
            ->route('templates.index')
            ->with('status', 'Template updated successfully.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeData(array $data, Organization $organization): array
    {
        if (! empty($data['template_category_id'])) {
            $organization->templateCategories()->findOrFail($data['template_category_id']);
        }

        $data['features_json'] = $this->normalizeLines($data['features_json'] ?? null);
        $data['benefits_json'] = $this->normalizeLines($data['benefits_json'] ?? null);

        return $data;
    }

    /**
     * @return list<string>|null
     */
    private function normalizeLines(?string $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $lines = array_values(array_filter(
            array_map('trim', preg_split('/\r\n|\r|\n/', $value) ?: []),
            static fn (string $line): bool => $line !== '',
        ));

        return $lines === [] ? null : $lines;
    }
}
