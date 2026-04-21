<?php

namespace App\Services\AI;

use App\Models\AiRequest;
use App\Models\Organization;

class AIRequestLoggerService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function log(Organization $organization, array $attributes): ?AiRequest
    {
        if (! config('ai.request_logging.enabled')) {
            return null;
        }

        return $organization->aiRequests()->create($attributes);
    }
}
