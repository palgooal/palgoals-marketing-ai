<?php

use App\Models\OfferGeneration;
use App\Models\Organization;
use App\Models\PromptTemplate;
use App\Models\User;
use App\Services\AI\AIExecutionService;

test('authenticated users can generate an offer', function () {
    /** @var \Tests\TestCase $this */

    config()->set('ai.request_logging.enabled', false);

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.test-basic-offer',
        'title' => 'Test Offer Prompt',
        'description' => 'Offer prompt for tests.',
        'system_prompt' => 'Write concise offer copy.',
        'user_prompt_template' => "Title: {{title}}\nOffer Type: {{type}}\nContext: {{context}}",
        'module' => 'offers',
        'version' => 1,
        'is_active' => true,
    ]);

    $mock = \Mockery::mock(AIExecutionService::class);
    $mock->shouldReceive('execute')
        ->once()
        ->with('offers', 'discount_offer', \Mockery::on(function (array $payload): bool {
            return ($payload['input_payload']['discount'] ?? null) === '15%'
                && ($payload['input_payload']['context'] ?? null) === 'Ramadan campaign';
        }))
        ->andReturn([
            'output_text' => 'Special Ramadan discount offer for returning customers.',
            'provider_name' => 'openai',
            'model_name' => 'gpt-test',
            'prompt_snapshot' => 'snapshot',
        ]);

    app()->instance(AIExecutionService::class, $mock);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('offers.generate'), [
        'prompt_template_id' => $promptTemplate->id,
        'title' => 'Ramadan Savings',
        'offer_type' => 'discount_offer',
        'context' => 'Ramadan campaign',
        'input_payload' => json_encode([
            'discount' => '15%',
            'audience' => 'returning customers',
        ], JSON_THROW_ON_ERROR),
    ]);

    $offerGeneration = OfferGeneration::query()->first();

    $response->assertRedirect(route('offers.show', $offerGeneration));

    expect($offerGeneration)->not->toBeNull();
    expect($offerGeneration->title)->toBe('Ramadan Savings');
    expect($offerGeneration->offer_type)->toBe('discount_offer');
    expect($offerGeneration->status)->toBe('draft');
    expect($offerGeneration->provider_name)->toBe('openai');
    expect($offerGeneration->model_name)->toBe('gpt-test');
    expect($offerGeneration->input_payload)->toMatchArray([
        'discount' => '15%',
        'audience' => 'returning customers',
        'context' => 'Ramadan campaign',
    ]);
});

test('offer create screen can be prefilled from a previous generation', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.prefill-offer',
        'title' => 'Prefill Offer Prompt',
        'description' => 'Offer prompt for prefill tests.',
        'system_prompt' => 'Write concise offer copy.',
        'user_prompt_template' => 'Offer Type: {{type}}',
        'module' => 'offers',
        'version' => 1,
        'is_active' => true,
    ]);

    $offerGeneration = $organization->offerGenerations()->create([
        'prompt_template_id' => $promptTemplate->id,
        'title' => 'Summer Deal',
        'offer_type' => 'seasonal_offer',
        'input_payload' => [
            'audience' => 'families',
            'context' => 'Summer launch',
        ],
        'output_text' => 'Summer deal output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'draft',
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('offers.create', ['from' => $offerGeneration->id]));

    $response->assertOk();
    $response->assertSee('Prefilled from a previous offer generation.', false);
    $response->assertSee('Summer Deal', false);
    $response->assertSee('Summer launch', false);
    $response->assertSee('&quot;audience&quot;: &quot;families&quot;', false);
});
