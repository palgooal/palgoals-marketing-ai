<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\StoreBrandServiceRequest;
use App\Http\Requests\Services\UpdateBrandServiceRequest;
use App\Models\BrandService;
use App\Support\CurrentOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandServiceController extends Controller
{
    public function index(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();

        return view('services.index', [
            'brandServices' => $organization->brandServices()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                })
                ->when(in_array($status, ['draft', 'active', 'archived'], true), function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->orderBy('sort_order')
                ->orderBy('title')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('services.create');
    }

    public function store(StoreBrandServiceRequest $request): RedirectResponse
    {
        $organization = CurrentOrganization::get();

        $organization->brandServices()->create(
            $this->normalizeArrayFields($request->validated()),
        );

        return redirect()
            ->route('services.index')
            ->with('status', 'Service created successfully.');
    }

    public function edit(BrandService $brandService): View
    {
        return view('services.edit', [
            'brandService' => CurrentOrganization::get()->brandServices()->findOrFail($brandService->getKey()),
        ]);
    }

    public function update(UpdateBrandServiceRequest $request, BrandService $brandService): RedirectResponse
    {
        $brandService = CurrentOrganization::get()->brandServices()->findOrFail($brandService->getKey());

        $brandService->update(
            $this->normalizeArrayFields($request->validated()),
        );

        return redirect()
            ->route('services.index')
            ->with('status', 'Service updated successfully.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeArrayFields(array $data): array
    {
        $data['benefits_json'] = $this->normalizeLines($data['benefits_json'] ?? null);
        $data['problems_solved_json'] = $this->normalizeLines($data['problems_solved_json'] ?? null);

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
