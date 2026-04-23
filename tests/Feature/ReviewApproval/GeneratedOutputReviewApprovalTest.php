<?php

use App\Models\ContentGeneration;
use App\Models\OfferGeneration;
use App\Models\Organization;
use App\Models\PageAnalysis;
use App\Models\PromptTemplate;
use App\Models\StrategyPlan;
use App\Models\User;

test('generated outputs can be marked as reviewed and approved', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.review-template',
        'title' => 'Review Template',
        'description' => 'Prompt for review tests.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $contentGeneration = ContentGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $promptTemplate->id,
        'type' => 'social_post',
        'title' => 'Content Draft',
        'input_payload' => ['context' => 'Review content'],
        'output_text' => 'Generated content output',
        'language' => 'ar',
        'tone' => 'helpful',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'draft',
    ]);

    $offerPromptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.review-template',
        'title' => 'Offer Review Template',
        'description' => 'Prompt for offer review tests.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Type: {{type}}',
        'module' => 'offers',
        'version' => 1,
        'is_active' => true,
    ]);

    $offerGeneration = OfferGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $offerPromptTemplate->id,
        'title' => 'Offer Draft',
        'offer_type' => 'discount_offer',
        'input_payload' => ['context' => 'Offer review'],
        'output_text' => 'Offer output',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'draft',
    ]);

    $planPromptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'plans.review-template',
        'title' => 'Plan Review Template',
        'description' => 'Prompt for plan review tests.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Period: {{type}}',
        'module' => 'plans',
        'version' => 1,
        'is_active' => true,
    ]);

    $strategyPlan = StrategyPlan::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $planPromptTemplate->id,
        'period_type' => 'weekly',
        'title' => 'Plan Draft',
        'goals_json' => ['Goal A'],
        'input_payload' => ['context' => 'Plan review'],
        'output_text' => 'Plan output',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'draft',
    ]);

    $analysisPromptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'analysis.review-template',
        'title' => 'Analysis Review Template',
        'description' => 'Prompt for analysis review tests.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Page: {{title}}',
        'module' => 'analysis',
        'version' => 1,
        'is_active' => true,
    ]);

    $pageAnalysis = PageAnalysis::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $analysisPromptTemplate->id,
        'page_title' => 'Analysis Draft',
        'page_url' => 'https://example.com',
        'page_type' => 'landing_page',
        'input_payload' => ['context' => 'Analysis review'],
        'findings_text' => null,
        'recommendations_text' => 'Recommendations output',
        'score' => null,
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'draft',
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)->patch(route('content.mark-reviewed', $contentGeneration))
        ->assertRedirect(route('content.show', $contentGeneration));
    $this->actingAs($user)->patch(route('offers.mark-approved', $offerGeneration))
        ->assertRedirect(route('offers.show', $offerGeneration));
    $this->actingAs($user)->patch(route('plans.mark-reviewed', $strategyPlan))
        ->assertRedirect(route('plans.show', $strategyPlan));
    $this->actingAs($user)->patch(route('analysis.mark-approved', $pageAnalysis))
        ->assertRedirect(route('analysis.show', $pageAnalysis));

    expect($contentGeneration->fresh()->status)->toBe('reviewed');
    expect($offerGeneration->fresh()->status)->toBe('approved');
    expect($strategyPlan->fresh()->status)->toBe('reviewed');
    expect($pageAnalysis->fresh()->status)->toBe('approved');
});

test('content show page exposes review actions and dashboard summarizes statuses', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.dashboard-review-template',
        'title' => 'Dashboard Review Template',
        'description' => 'Prompt for dashboard review tests.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $contentGeneration = ContentGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $promptTemplate->id,
        'type' => 'social_post',
        'title' => 'Legacy Content',
        'input_payload' => ['context' => 'Legacy'],
        'output_text' => 'Output',
        'language' => 'ar',
        'tone' => 'helpful',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'completed',
    ]);

    OfferGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $promptTemplate->id,
        'title' => 'Reviewed Offer',
        'offer_type' => 'discount_offer',
        'input_payload' => ['context' => 'Reviewed'],
        'output_text' => 'Offer output',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'reviewed',
    ]);

    StrategyPlan::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $promptTemplate->id,
        'period_type' => 'weekly',
        'title' => 'Approved Plan',
        'goals_json' => ['Goal'],
        'input_payload' => ['context' => 'Approved'],
        'output_text' => 'Plan output',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'approved',
    ]);

    $user = User::factory()->create();

    $showResponse = $this->actingAs($user)->get(route('content.show', $contentGeneration));

    $showResponse->assertOk();
    $showResponse->assertSee('Review Status', false);
    $showResponse->assertSee('Mark as Reviewed', false);
    $showResponse->assertSee('Mark as Approved', false);
    $showResponse->assertSee('Draft', false);

    $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));

    $dashboardResponse->assertOk();
    $dashboardResponse->assertSee('Review Summary', false);
    $dashboardResponse->assertSee('Draft Outputs', false);
    $dashboardResponse->assertSee('Reviewed Outputs', false);
    $dashboardResponse->assertSee('Approved Outputs', false);
    $dashboardResponse->assertSee('1', false);
});
