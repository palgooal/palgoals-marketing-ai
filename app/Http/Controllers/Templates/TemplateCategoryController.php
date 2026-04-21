<?php

namespace App\Http\Controllers\Templates;

use App\Http\Controllers\Controller;
use App\Http\Requests\Templates\StoreTemplateCategoryRequest;
use App\Http\Requests\Templates\UpdateTemplateCategoryRequest;
use App\Models\TemplateCategory;
use App\Support\CurrentOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();

        return view('template-categories.index', [
            'templateCategories' => $organization->templateCategories()
                ->withCount('templates')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('template-categories.create');
    }

    public function store(StoreTemplateCategoryRequest $request): RedirectResponse
    {
        CurrentOrganization::get()->templateCategories()->create($request->validated());

        return redirect()
            ->route('template-categories.index')
            ->with('status', 'Template category created successfully.');
    }

    public function edit(TemplateCategory $templateCategory): View
    {
        return view('template-categories.edit', [
            'templateCategory' => CurrentOrganization::get()->templateCategories()->findOrFail($templateCategory->getKey()),
        ]);
    }

    public function update(UpdateTemplateCategoryRequest $request, TemplateCategory $templateCategory): RedirectResponse
    {
        $templateCategory = CurrentOrganization::get()->templateCategories()->findOrFail($templateCategory->getKey());

        $templateCategory->update($request->validated());

        return redirect()
            ->route('template-categories.index')
            ->with('status', 'Template category updated successfully.');
    }

}
