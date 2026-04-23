<?php

use App\Models\AiRequest;
use App\Models\ContentGeneration;
use App\Models\OfferGeneration;
use App\Models\Organization;
use App\Models\PageAnalysis;
use App\Models\PromptTemplate;
use App\Models\StrategyPlan;
use App\Models\User;

test('dashboard shows lightweight AI workflow health insights and stale drafts', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $contentPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.health-template',
        'title' => 'Health Content Prompt',
        'description' => 'Health test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $offerPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.health-template',
        'title' => 'Health Offer Prompt',
        'description' => 'Health test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Offer: {{title}}',
        'module' => 'offers',
        'version' => 1,
        'is_active' => true,
    ]);

    $plansPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'plans.health-template',
        'title' => 'Health Plan Prompt',
        'description' => 'Health test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Plan: {{title}}',
        'module' => 'plans',
        'version' => 1,
        'is_active' => true,
    ]);

    $analysisPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'analysis.health-template',
        'title' => 'Health Analysis Prompt',
        'description' => 'Health test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Page: {{title}}',
        'module' => 'analysis',
        'version' => 1,
        'is_active' => true,
    ]);

    PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.unused-health-template',
        'title' => 'Unused Health Prompt',
        'description' => 'Unused health prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    AiRequest::query()->create([
        'organization_id' => $organization->id,
        'module' => 'content',
        'task_type' => 'social_post',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'prompt_snapshot' => 'Snapshot',
        'input_payload' => ['title' => 'One'],
        'output_payload' => 'Output',
        'tokens_input' => 10,
        'tokens_output' => 20,
        'estimated_cost' => 0.010000,
        'latency_ms' => 500,
        'status' => 'completed',
        'error_message' => null,
    ]);

    AiRequest::query()->create([
        'organization_id' => $organization->id,
        'module' => 'offers',
        'task_type' => 'discount_offer',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'prompt_snapshot' => 'Snapshot',
        'input_payload' => ['title' => 'Two'],
        'output_payload' => null,
        'tokens_input' => 10,
        'tokens_output' => 0,
        'estimated_cost' => 0.010000,
        'latency_ms' => 1500,
        'status' => 'failed',
        'error_message' => 'Bad request',
    ]);

    $staleContentGeneration = ContentGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $contentPrompt->id,
        'type' => 'social_post',
        'title' => 'Old draft content',
        'input_payload' => [],
        'output_text' => 'Output',
        'language' => 'en',
        'tone' => 'clear',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'draft',
        'is_published' => false,
    ]);

    $staleContentGeneration->forceFill([
        'created_at' => now()->subDays(10),
        'updated_at' => now()->subDays(10),
    ])->saveQuietly();

    OfferGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $offerPrompt->id,
        'title' => 'Reviewed offer',
        'offer_type' => 'discount_offer',
        'input_payload' => [],
        'output_text' => 'Output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'reviewed',
        'is_published' => false,
    ]);

    StrategyPlan::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $plansPrompt->id,
        'period_type' => 'monthly',
        'title' => 'Approved plan',
        'goals_json' => ['Goal'],
        'input_payload' => [],
        'output_text' => 'Output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'approved',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    PageAnalysis::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $analysisPrompt->id,
        'page_title' => 'Recent draft analysis',
        'page_url' => 'https://example.com',
        'page_type' => 'homepage',
        'input_payload' => [],
        'findings_text' => null,
        'recommendations_text' => 'Review this',
        'score' => null,
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'draft',
        'is_published' => false,
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subDays(2),
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Workflow Health', false);
    $response->assertSee('Failed AI Requests', false);
    $response->assertSee('Successful AI Requests', false);
    $response->assertSee('Stale Drafts', false);
    $response->assertSee('Older Than 7 Days', false);
    $response->assertSee('Unused Active Prompts', false);
    $response->assertSee('Review AI Logs', false);
    $response->assertViewHas('workflowHealth', [
        'failed' => 1,
        'successful' => 1,
    ]);
    $response->assertViewHas('staleDraftInsights', function (array $staleDraftInsights): bool {
        return $staleDraftInsights['count'] === 1
            && $staleDraftInsights['days'] === 7
            && count($staleDraftInsights['links']) === 1
            && $staleDraftInsights['links'][0]['label'] === 'Content';
    });
    $response->assertViewHas('promptInsights', function (array $promptInsights): bool {
        return $promptInsights['unused_active'] === 1;
    });
});
