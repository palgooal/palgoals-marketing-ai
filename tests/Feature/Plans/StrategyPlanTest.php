<?php

use App\Models\Organization;
use App\Models\PromptTemplate;
use App\Models\StrategyPlan;
use App\Models\User;
use App\Services\AI\AIExecutionService;

test('authenticated users can generate a strategy plan', function () {
    /** @var \Tests\TestCase $this */

    config()->set('ai.request_logging.enabled', false);

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'plans.test-basic-strategy',
        'title' => 'Test Strategy Planner',
        'description' => 'Strategy planner prompt for tests.',
        'system_prompt' => 'Build a concise strategy plan.',
        'user_prompt_template' => "Title: {{title}}\nPeriod: {{type}}\nContext: {{context}}\nGoals: {{goals}}",
        'module' => 'plans',
        'version' => 1,
        'is_active' => true,
    ]);

    $mock = \Mockery::mock(AIExecutionService::class);
    $mock->shouldReceive('execute')
        ->once()
        ->with('plans', 'monthly', \Mockery::on(function (array $payload): bool {
            return ($payload['input_payload']['context'] ?? null) === 'Focus on lead quality'
                && ($payload['input_payload']['period_type'] ?? null) === 'monthly'
                && ($payload['input_payload']['goals'] ?? []) === ['Increase qualified leads', 'Improve follow-up speed'];
        }))
        ->andReturn([
            'output_text' => 'Monthly strategy plan output.',
            'provider_name' => 'openai',
            'model_name' => 'gpt-test',
            'prompt_snapshot' => 'snapshot',
        ]);

    app()->instance(AIExecutionService::class, $mock);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('plans.generate'), [
        'prompt_template_id' => $promptTemplate->id,
        'title' => 'May Growth Plan',
        'period_type' => 'monthly',
        'goals' => "Increase qualified leads\nImprove follow-up speed",
        'context' => 'Focus on lead quality',
        'input_payload' => json_encode([
            'channels' => ['instagram', 'email'],
        ], JSON_THROW_ON_ERROR),
    ]);

    $strategyPlan = StrategyPlan::query()->first();

    $response->assertRedirect(route('plans.show', $strategyPlan));

    expect($strategyPlan)->not->toBeNull();
    expect($strategyPlan->title)->toBe('May Growth Plan');
    expect($strategyPlan->period_type)->toBe('monthly');
    expect($strategyPlan->status)->toBe('draft');
    expect($strategyPlan->provider_name)->toBe('openai');
    expect($strategyPlan->model_name)->toBe('gpt-test');
    expect($strategyPlan->goals_json)->toBe([
        'Increase qualified leads',
        'Improve follow-up speed',
    ]);
    expect($strategyPlan->input_payload)->toMatchArray([
        'channels' => ['instagram', 'email'],
        'context' => 'Focus on lead quality',
    ]);
});

test('strategy plan create screen can be prefilled from a previous plan', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'plans.prefill-plan',
        'title' => 'Prefill Strategy Planner',
        'description' => 'Strategy planner prompt for prefill tests.',
        'system_prompt' => 'Build a concise strategy plan.',
        'user_prompt_template' => 'Period: {{type}}',
        'module' => 'plans',
        'version' => 1,
        'is_active' => true,
    ]);

    $strategyPlan = $organization->strategyPlans()->create([
        'prompt_template_id' => $promptTemplate->id,
        'title' => 'Weekly Launch Plan',
        'period_type' => 'weekly',
        'goals_json' => ['Launch new offer', 'Review channel performance'],
        'input_payload' => [
            'owner' => 'marketing team',
            'context' => 'Prioritize quick wins',
        ],
        'output_text' => 'Weekly plan output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'draft',
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('plans.create', ['from' => $strategyPlan->id]));

    $response->assertOk();
    $response->assertSee('Prefilled from a previous strategy plan.', false);
    $response->assertSee('Weekly Launch Plan', false);
    $response->assertSee('Prioritize quick wins', false);
    $response->assertSee('Launch new offer', false);
    $response->assertSee('Review channel performance', false);
    $response->assertSee('&quot;owner&quot;: &quot;marketing team&quot;', false);
});
