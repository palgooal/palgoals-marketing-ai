<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Offers\StoreOfferGenerationRequest;
use App\Models\OfferGeneration;
use App\Services\Offers\OfferGenerationRunner;
use App\Support\AIPackageTextFormatter;
use App\Support\AIOutputStatuses;
use App\Support\AITextExportFormatter;
use App\Support\CurrentOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

class OfferGenerationController extends Controller
{
    public function __construct(
        private readonly OfferGenerationRunner $offerGenerationRunner,
        private readonly AITextExportFormatter $aiTextExportFormatter,
        private readonly AIPackageTextFormatter $aiPackageTextFormatter,
    ) {}

    public function index(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $query = $organization->offerGenerations()
            ->with('promptTemplate')
            ->latest();

        $this->applyIndexFilters($query, $request);

        return view('offers.index', [
            'offerGenerations' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['offer_type', 'status', 'published', 'prompt_template_id', 'search']),
            'promptTemplates' => $organization->promptTemplates()
                ->orderBy('title')
                ->get(['id', 'title']),
            'availableOfferTypes' => $this->availableOfferTypes(),
            'availableStatuses' => AIOutputStatuses::labels(),
            'availablePublishedStates' => $this->availablePublishedStates(),
        ]);
    }

    public function create(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $sourceOfferGeneration = null;

        if ($request->filled('from')) {
            $sourceOfferGeneration = $organization->offerGenerations()
                ->with('promptTemplate')
                ->findOrFail($request->integer('from'));
        }

        return view('offers.create', [
            'promptTemplates' => $organization->promptTemplates()
                ->where('is_active', true)
                ->orderBy('title')
                ->get(),
            'offerTypes' => $this->availableOfferTypes(),
            'defaults' => $this->buildCreateDefaults($sourceOfferGeneration),
            'sourceOfferGeneration' => $sourceOfferGeneration,
        ]);
    }

    public function store(StoreOfferGenerationRequest $request): RedirectResponse
    {
        $organization = CurrentOrganization::get();
        $promptTemplate = $organization->promptTemplates()
            ->where('is_active', true)
            ->findOrFail($request->integer('prompt_template_id'));

        try {
            $offerGeneration = $this->offerGenerationRunner->run($organization, $promptTemplate, [
                'title' => $request->input('title'),
                'offer_type' => $request->input('offer_type'),
                'context' => $request->input('context'),
                'input_payload' => $request->input('input_payload'),
            ]);

            return redirect()->route('offers.show', $offerGeneration);
        } catch (Throwable $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'generation' => $exception->getMessage(),
                ]);
        }
    }

    public function show(OfferGeneration $offerGeneration): View
    {
        return view('offers.show', [
            'offerGeneration' => $this->findOfferGeneration($offerGeneration),
        ]);
    }

    public function package(OfferGeneration $offerGeneration): View
    {
        return view('offers.package', [
            'offerGeneration' => $this->findOfferGeneration($offerGeneration),
        ]);
    }

    public function markReviewed(OfferGeneration $offerGeneration): RedirectResponse
    {
        $offerGeneration = $this->findOfferGeneration($offerGeneration);
        $offerGeneration->update(['status' => AIOutputStatuses::REVIEWED]);

        return redirect()
            ->route('offers.show', $offerGeneration)
            ->with('status', 'Offer marked as reviewed.');
    }

    public function markApproved(OfferGeneration $offerGeneration): RedirectResponse
    {
        $offerGeneration = $this->findOfferGeneration($offerGeneration);
        $offerGeneration->update(['status' => AIOutputStatuses::APPROVED]);

        return redirect()
            ->route('offers.show', $offerGeneration)
            ->with('status', 'Offer marked as approved.');
    }

    public function publish(OfferGeneration $offerGeneration): RedirectResponse
    {
        $offerGeneration = $this->findOfferGeneration($offerGeneration);

        if (! AIOutputStatuses::canPublish($offerGeneration->status)) {
            return redirect()
                ->route('offers.show', $offerGeneration)
                ->with('error', 'Offer can only be published after review or approval.');
        }

        $offerGeneration->update([
            'is_published' => true,
            'published_at' => $offerGeneration->published_at ?? now(),
        ]);

        return redirect()
            ->route('offers.show', $offerGeneration)
            ->with('status', 'Offer published successfully.');
    }

    public function unpublish(OfferGeneration $offerGeneration): RedirectResponse
    {
        $offerGeneration = $this->findOfferGeneration($offerGeneration);
        $offerGeneration->update([
            'is_published' => false,
        ]);

        return redirect()
            ->route('offers.show', $offerGeneration)
            ->with('status', 'Offer unpublished successfully.');
    }

    public function exportText(OfferGeneration $offerGeneration): Response
    {
        $offerGeneration = $this->findOfferGeneration($offerGeneration);

        return response($this->aiTextExportFormatter->formatOffer($offerGeneration), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function exportPackageText(OfferGeneration $offerGeneration): Response
    {
        $offerGeneration = $this->findOfferGeneration($offerGeneration);

        return response($this->aiPackageTextFormatter->formatOffer($offerGeneration), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    private function findOfferGeneration(OfferGeneration $offerGeneration): OfferGeneration
    {
        return CurrentOrganization::get()->offerGenerations()
            ->with('promptTemplate')
            ->findOrFail($offerGeneration->getKey());
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<OfferGeneration>  $query
     */
    private function applyIndexFilters($query, Request $request): void
    {
        $query->when($request->filled('offer_type'), function ($builder) use ($request): void {
            $builder->where('offer_type', $request->string('offer_type')->toString());
        });

        $query->when($request->filled('status'), function ($builder) use ($request): void {
            $builder->whereIn('status', AIOutputStatuses::databaseValuesFor($request->string('status')->toString()));
        });

        $query->when($request->filled('published'), function ($builder) use ($request): void {
            $builder->where('is_published', $request->string('published')->toString() === 'published');
        });

        $query->when($request->filled('prompt_template_id'), function ($builder) use ($request): void {
            $builder->where('prompt_template_id', $request->integer('prompt_template_id'));
        });

        $query->when($request->filled('search'), function ($builder) use ($request): void {
            $search = $request->string('search')->trim()->toString();

            $builder->where('title', 'like', "%{$search}%");
        });
    }

    /**
     * @return array<string, string>
     */
    private function availableOfferTypes(): array
    {
        return [
            'limited_time_offer' => 'Limited Time Offer',
            'bundle_offer' => 'Bundle Offer',
            'seasonal_offer' => 'Seasonal Offer',
            'discount_offer' => 'Discount Offer',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function availablePublishedStates(): array
    {
        return [
            'published' => 'Published',
            'unpublished' => 'Unpublished',
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function buildCreateDefaults(?OfferGeneration $offerGeneration): array
    {
        if ($offerGeneration === null) {
            return [
                'prompt_template_id' => null,
                'title' => null,
                'offer_type' => 'limited_time_offer',
                'context' => null,
                'input_payload' => '{}',
            ];
        }

        $inputPayload = $offerGeneration->input_payload ?? [];

        if (is_array($inputPayload) && array_key_exists('context', $inputPayload)) {
            unset($inputPayload['context']);
        }

        return [
            'prompt_template_id' => (string) $offerGeneration->prompt_template_id,
            'title' => $offerGeneration->title,
            'offer_type' => $offerGeneration->offer_type,
            'context' => is_array($offerGeneration->input_payload) ? ($offerGeneration->input_payload['context'] ?? null) : null,
            'input_payload' => $this->formatInputPayload(is_array($inputPayload) ? $inputPayload : null),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function formatInputPayload(?array $payload): string
    {
        if ($payload === null || $payload === []) {
            return '{}';
        }

        return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';
    }
}
