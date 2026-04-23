<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\AiRequest;
use App\Support\AIWorkflowHealthInsights;
use App\Support\CurrentOrganization;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiRequestLogController extends Controller
{
    public function __construct(
        private readonly AIWorkflowHealthInsights $aiWorkflowHealthInsights,
    ) {}

    public function index(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $query = $organization->aiRequests()->latest();

        $this->applyFilters($query, $request);

        return view('logs.ai-requests.index', [
            'aiRequests' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['module', 'task_type', 'status', 'provider_name', 'health', 'search']),
            'modules' => $organization->aiRequests()->distinct()->orderBy('module')->pluck('module'),
            'taskTypes' => $organization->aiRequests()->distinct()->orderBy('task_type')->pluck('task_type'),
            'providerNames' => $organization->aiRequests()->distinct()->orderBy('provider_name')->pluck('provider_name'),
            'availableStatuses' => [
                'completed' => 'Completed',
                'failed' => 'Failed',
            ],
            'availableHealthFilters' => [
                'failed' => 'Failed Only',
                'slow' => 'Slow Requests',
                'missing-output' => 'Missing Output / Error',
            ],
            'slowRequestMs' => $this->aiWorkflowHealthInsights->slowRequestThresholdMs(),
            'aiWorkflowHealthInsights' => $this->aiWorkflowHealthInsights,
        ]);
    }

    public function show(AiRequest $aiRequest): View
    {
        return view('logs.ai-requests.show', [
            'aiRequest' => CurrentOrganization::get()->aiRequests()
                ->with('organization')
                ->findOrFail($aiRequest->getKey()),
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<AiRequest>  $query
     */
    private function applyFilters($query, Request $request): void
    {
        $query->when($request->filled('module'), function ($builder) use ($request): void {
            $builder->where('module', $request->string('module')->toString());
        });

        $query->when($request->filled('task_type'), function ($builder) use ($request): void {
            $builder->where('task_type', $request->string('task_type')->toString());
        });

        $query->when($request->filled('status'), function ($builder) use ($request): void {
            $builder->where('status', $request->string('status')->toString());
        });

        $query->when($request->filled('provider_name'), function ($builder) use ($request): void {
            $builder->where('provider_name', $request->string('provider_name')->toString());
        });

        $query->when($request->filled('health'), function ($builder) use ($request): void {
            $health = $request->string('health')->toString();

            if ($health === 'failed') {
                $builder->where('status', 'failed');
            }

            if ($health === 'slow') {
                $builder->whereNotNull('latency_ms')
                    ->where('latency_ms', '>=', $this->aiWorkflowHealthInsights->slowRequestThresholdMs());
            }

            if ($health === 'missing-output') {
                $builder->where(function ($healthQuery): void {
                    $healthQuery->whereNotNull('error_message')
                        ->orWhereNull('output_payload')
                        ->orWhere('output_payload', '');
                });
            }
        });

        $query->when($request->filled('search'), function ($builder) use ($request): void {
            $search = $request->string('search')->trim()->toString();

            $builder->where(function ($nestedBuilder) use ($search): void {
                $nestedBuilder
                    ->where('module', 'like', "%{$search}%")
                    ->orWhere('task_type', 'like', "%{$search}%");
            });
        });
    }
}
