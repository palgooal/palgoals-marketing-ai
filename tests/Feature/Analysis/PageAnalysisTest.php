<?php

use App\Models\Organization;
use App\Models\PageAnalysis;
use App\Models\PromptTemplate;
use App\Models\User;
use App\Services\AI\AIExecutionService;

test('authenticated users can run a page analysis', function () {
    /** @var \Tests\TestCase $this */

    config()->set('ai.request_logging.enabled', false);

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'analysis.test-basic-review',
        'title' => 'Test Page Analyzer',
        'description' => 'Page analyzer prompt for tests.',
        'system_prompt' => 'Review this page.',
        'user_prompt_template' => "Page title: {{title}}\nPage type: {{type}}\nContext: {{context}}\nURL: {{page_url}}\nContent: {{page_content}}",
        'module' => 'analysis',
        'version' => 1,
        'is_active' => true,
    ]);

    $mock = \Mockery::mock(AIExecutionService::class);
    $mock->shouldReceive('execute')
        ->once()
        ->with('analysis', 'landing_page', \Mockery::on(function (array $payload): bool {
            return ($payload['input_payload']['page_url'] ?? null) === 'https://example.com/landing'
                && ($payload['input_payload']['page_content'] ?? null) === 'Hero headline and CTA text'
                && ($payload['input_payload']['context'] ?? null) === 'Focus on conversion clarity';
        }))
        ->andReturn([
            'output_text' => 'Recommendations: clarify headline and strengthen CTA.',
            'provider_name' => 'openai',
            'model_name' => 'gpt-test',
            'prompt_snapshot' => 'snapshot',
        ]);

    app()->instance(AIExecutionService::class, $mock);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('analysis.run'), [
        'prompt_template_id' => $promptTemplate->id,
        'page_title' => 'Landing Page Draft',
        'page_url' => 'https://example.com/landing',
        'page_type' => 'landing_page',
        'page_content' => 'Hero headline and CTA text',
        'context' => 'Focus on conversion clarity',
        'input_payload' => json_encode([
            'audience' => 'small businesses',
        ], JSON_THROW_ON_ERROR),
    ]);

    $pageAnalysis = PageAnalysis::query()->first();

    $response->assertRedirect(route('analysis.show', $pageAnalysis));

    expect($pageAnalysis)->not->toBeNull();
    expect($pageAnalysis->page_title)->toBe('Landing Page Draft');
    expect($pageAnalysis->page_url)->toBe('https://example.com/landing');
    expect($pageAnalysis->page_type)->toBe('landing_page');
    expect($pageAnalysis->status)->toBe('draft');
    expect($pageAnalysis->provider_name)->toBe('openai');
    expect($pageAnalysis->model_name)->toBe('gpt-test');
    expect($pageAnalysis->findings_text)->toBeNull();
    expect($pageAnalysis->recommendations_text)->toBe('Recommendations: clarify headline and strengthen CTA.');
    expect($pageAnalysis->input_payload)->toMatchArray([
        'audience' => 'small businesses',
        'page_url' => 'https://example.com/landing',
        'page_content' => 'Hero headline and CTA text',
        'context' => 'Focus on conversion clarity',
        'page_type' => 'landing_page',
    ]);
});

test('page analysis create screen can be prefilled from a previous analysis', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'analysis.prefill-review',
        'title' => 'Prefill Page Analyzer',
        'description' => 'Analyzer prompt for prefill tests.',
        'system_prompt' => 'Review this page.',
        'user_prompt_template' => 'Page type: {{type}}',
        'module' => 'analysis',
        'version' => 1,
        'is_active' => true,
    ]);

    $pageAnalysis = $organization->pageAnalyses()->create([
        'prompt_template_id' => $promptTemplate->id,
        'page_title' => 'Homepage Review',
        'page_url' => 'https://example.com',
        'page_type' => 'homepage',
        'input_payload' => [
            'audience' => 'founders',
            'page_content' => 'Current hero copy',
            'context' => 'Improve clarity',
            'page_url' => 'https://example.com',
            'page_type' => 'homepage',
        ],
        'findings_text' => null,
        'recommendations_text' => 'Use a sharper CTA.',
        'score' => null,
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'draft',
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('analysis.create', ['from' => $pageAnalysis->id]));

    $response->assertOk();
    $response->assertSee('Prefilled from a previous page analysis.', false);
    $response->assertSee('Homepage Review', false);
    $response->assertSee('https://example.com', false);
    $response->assertSee('Current hero copy', false);
    $response->assertSee('Improve clarity', false);
    $response->assertSee('&quot;audience&quot;: &quot;founders&quot;', false);
});
