<?php

namespace App\Http\Controllers\Knowledge;

use App\Http\Controllers\Controller;
use App\Http\Requests\Knowledge\StoreKnowledgeDocumentRequest;
use App\Http\Requests\Knowledge\UpdateKnowledgeDocumentRequest;
use App\Models\KnowledgeDocument;
use App\Support\CurrentOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $search = trim($request->string('search')->toString());
        $activity = $request->string('activity')->toString();

        $knowledgeDocuments = $organization->knowledgeDocuments()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($activity === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($activity === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('knowledge-documents.index', [
            'knowledgeDocuments' => $knowledgeDocuments,
        ]);
    }

    public function create(): View
    {
        return view('knowledge-documents.create');
    }

    public function store(StoreKnowledgeDocumentRequest $request): RedirectResponse
    {
        $organization = CurrentOrganization::get();

        $organization->knowledgeDocuments()->create(
            $this->normalizeData($request->validated(), $request),
        );

        return redirect()
            ->route('knowledge-documents.index')
            ->with('status', 'Knowledge document created successfully.');
    }

    public function edit(KnowledgeDocument $knowledgeDocument): View
    {
        return view('knowledge-documents.edit', [
            'knowledgeDocument' => CurrentOrganization::get()->knowledgeDocuments()->findOrFail($knowledgeDocument->getKey()),
        ]);
    }

    public function update(UpdateKnowledgeDocumentRequest $request, KnowledgeDocument $knowledgeDocument): RedirectResponse
    {
        $knowledgeDocument = CurrentOrganization::get()->knowledgeDocuments()->findOrFail($knowledgeDocument->getKey());

        $knowledgeDocument->update(
            $this->normalizeData($request->validated(), $request),
        );

        return redirect()
            ->route('knowledge-documents.index')
            ->with('status', 'Knowledge document updated successfully.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeData(array $data, Request $request): array
    {
        $data['metadata_json'] = $this->parseMetadataLines($data['metadata_json'] ?? null);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /**
     * @return array<string, string>|null
     */
    private function parseMetadataLines(?string $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $metadata = [];

        foreach (preg_split('/\r\n|\r|\n/', $value) ?: [] as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            [$key, $itemValue] = array_pad(explode(':', $line, 2), 2, '');
            $key = trim($key);

            if ($key === '') {
                continue;
            }

            $metadata[$key] = trim($itemValue);
        }

        return $metadata === [] ? null : $metadata;
    }
}
