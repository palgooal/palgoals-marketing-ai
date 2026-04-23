<?php

use App\Models\Organization;
use App\Models\User;

test('ai request logs index can be filtered', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $organization->aiRequests()->create([
        'module' => 'offers',
        'task_type' => 'discount_offer',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'prompt_snapshot' => 'offers snapshot',
        'input_payload' => ['title' => 'Offer'],
        'output_payload' => 'Offer output',
        'tokens_input' => 100,
        'tokens_output' => 200,
        'estimated_cost' => 0.123456,
        'latency_ms' => 900,
        'status' => 'completed',
        'error_message' => null,
    ]);

    $organization->aiRequests()->create([
        'module' => 'analysis',
        'task_type' => 'landing_page',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'prompt_snapshot' => 'analysis snapshot',
        'input_payload' => ['title' => 'Analysis'],
        'output_payload' => 'Analysis output',
        'tokens_input' => 50,
        'tokens_output' => 80,
        'estimated_cost' => 0.050000,
        'latency_ms' => 700,
        'status' => 'failed',
        'error_message' => 'Bad prompt',
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('logs.ai-requests.index', [
        'module' => 'analysis',
        'status' => 'failed',
    ]));

    $response->assertOk();
    $response->assertViewHas('aiRequests', function ($aiRequests): bool {
        return $aiRequests->count() === 1
            && $aiRequests->first()->module === 'analysis'
            && $aiRequests->first()->task_type === 'landing_page';
    });
});

test('ai request logs index supports lightweight health filters and indicators', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $organization->aiRequests()->create([
        'module' => 'content',
        'task_type' => 'social_post',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'prompt_snapshot' => 'content snapshot',
        'input_payload' => ['title' => 'Content'],
        'output_payload' => 'Done',
        'tokens_input' => 100,
        'tokens_output' => 200,
        'estimated_cost' => 0.100000,
        'latency_ms' => 1300,
        'status' => 'completed',
        'error_message' => null,
    ]);

    $organization->aiRequests()->create([
        'module' => 'offers',
        'task_type' => 'discount_offer',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'prompt_snapshot' => 'offer snapshot',
        'input_payload' => ['title' => 'Offer'],
        'output_payload' => null,
        'tokens_input' => 90,
        'tokens_output' => 0,
        'estimated_cost' => 0.050000,
        'latency_ms' => 600,
        'status' => 'failed',
        'error_message' => 'Timeout',
    ]);

    $user = User::factory()->create();

    $slowResponse = $this->actingAs($user)->get(route('logs.ai-requests.index', ['health' => 'slow']));

    $slowResponse->assertOk();
    $slowResponse->assertSee('Slow Requests', false);
    $slowResponse->assertSee('Slow', false);
    $slowResponse->assertSee('1300 ms', false);
    $slowResponse->assertDontSee('Timeout', false);

    $missingOutputResponse = $this->actingAs($user)->get(route('logs.ai-requests.index', ['health' => 'missing-output']));

    $missingOutputResponse->assertOk();
    $missingOutputResponse->assertSee('Missing Output / Error', false);
    $missingOutputResponse->assertSee('Failed', false);
    $missingOutputResponse->assertSee('discount_offer', false);
    $missingOutputResponse->assertViewHas('aiRequests', function ($aiRequests): bool {
        return $aiRequests->count() === 1
            && $aiRequests->first()->module === 'offers'
            && $aiRequests->first()->task_type === 'discount_offer';
    });
});

test('ai request log show displays stored request details', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $aiRequest = $organization->aiRequests()->create([
        'module' => 'plans',
        'task_type' => 'weekly',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'prompt_snapshot' => 'system prompt and user prompt',
        'input_payload' => ['goals' => ['Goal A']],
        'output_payload' => 'Generated weekly plan',
        'tokens_input' => 120,
        'tokens_output' => 250,
        'estimated_cost' => 0.250000,
        'latency_ms' => 1100,
        'status' => 'completed',
        'error_message' => null,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('logs.ai-requests.show', $aiRequest));

    $response->assertOk();
    $response->assertSee('plans', false);
    $response->assertSee('weekly', false);
    $response->assertSee('system prompt and user prompt', false);
    $response->assertSee('Generated weekly plan', false);
});
