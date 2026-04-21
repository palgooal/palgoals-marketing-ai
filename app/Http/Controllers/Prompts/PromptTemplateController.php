<?php

namespace App\Http\Controllers\Prompts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prompts\StorePromptTemplateRequest;
use App\Http\Requests\Prompts\UpdatePromptTemplateRequest;
use App\Models\PromptTemplate;
use App\Support\CurrentOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromptTemplateController extends Controller
{
    public function index(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $query = $organization->promptTemplates()
            ->orderBy('module')
            ->orderBy('title');

        $this->applyIndexFilters($query, $request);

        return view('prompts.index', [
            'promptTemplates' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['module', 'active', 'search']),
            'modules' => $organization->promptTemplates()
                ->select('module')
                ->distinct()
                ->orderBy('module')
                ->pluck('module'),
        ]);
    }

    public function create(): View
    {
        return view('prompts.create');
    }

    public function store(StorePromptTemplateRequest $request): RedirectResponse
    {
        CurrentOrganization::get()->promptTemplates()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('prompts.index')
            ->with('status', 'Prompt template created successfully.');
    }

    public function edit(PromptTemplate $promptTemplate): View
    {
        return view('prompts.edit', [
            'promptTemplate' => CurrentOrganization::get()->promptTemplates()->findOrFail($promptTemplate->getKey()),
        ]);
    }

    public function update(UpdatePromptTemplateRequest $request, PromptTemplate $promptTemplate): RedirectResponse
    {
        $promptTemplate = CurrentOrganization::get()->promptTemplates()->findOrFail($promptTemplate->getKey());

        $promptTemplate->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('prompts.index')
            ->with('status', 'Prompt template updated successfully.');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<PromptTemplate>  $query
     */
    private function applyIndexFilters($query, Request $request): void
    {
        $query->when($request->filled('module'), function ($builder) use ($request): void {
            $builder->where('module', $request->string('module')->toString());
        });

        $query->when($request->filled('active'), function ($builder) use ($request): void {
            $builder->where('is_active', $request->string('active')->toString() === '1');
        });

        $query->when($request->filled('search'), function ($builder) use ($request): void {
            $search = $request->string('search')->trim()->toString();

            $builder->where(function ($nestedQuery) use ($search): void {
                $nestedQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('key', 'like', "%{$search}%");
            });
        });
    }
}
