<?php

use App\Models\ContentGeneration;
use App\Models\OfferGeneration;
use App\Models\Organization;
use App\Models\PageAnalysis;
use App\Models\PromptTemplate;
use App\Models\StrategyPlan;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

test('draft content cannot be published', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.publish-blocked',
        'title' => 'Content Publish Blocked Prompt',
        'description' => 'Prompt used for publishing tests.',
        'system_prompt' => 'Write concise content.',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $contentGeneration = $organization->contentGenerations()->create([
        'prompt_template_id' => $promptTemplate->id,
        'type' => 'social_post',
        'title' => 'Draft content',
        'input_payload' => [],
        'output_text' => 'Draft content output',
        'language' => 'en',
        'tone' => 'clear',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'draft',
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('content.publish', $contentGeneration));

    $response->assertRedirect(route('content.show', $contentGeneration));
    $response->assertSessionHas('error', 'Content can only be published after review or approval.');

    $contentGeneration->refresh();

    expect($contentGeneration->is_published)->toBeFalse();
    expect($contentGeneration->published_at)->toBeNull();
});

test('reviewed offer can be published and unpublished while keeping the publish timestamp', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.publishable-offer',
        'title' => 'Offer Publish Prompt',
        'description' => 'Prompt used for publishing tests.',
        'system_prompt' => 'Write concise offer copy.',
        'user_prompt_template' => 'Offer: {{title}}',
        'module' => 'offers',
        'version' => 1,
        'is_active' => true,
    ]);

    $offerGeneration = $organization->offerGenerations()->create([
        'prompt_template_id' => $promptTemplate->id,
        'title' => 'Reviewed offer',
        'offer_type' => 'discount_offer',
        'input_payload' => [],
        'output_text' => 'Reviewed offer output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'reviewed',
    ]);

    $user = User::factory()->create();

    $publishResponse = $this->actingAs($user)->patch(route('offers.publish', $offerGeneration));

    $publishResponse->assertRedirect(route('offers.show', $offerGeneration));
    $publishResponse->assertSessionHas('status', 'Offer published successfully.');

    $offerGeneration->refresh();
    $publishedAt = $offerGeneration->published_at;

    expect($offerGeneration->is_published)->toBeTrue();
    expect($publishedAt)->not->toBeNull();

    $unpublishResponse = $this->actingAs($user)->patch(route('offers.unpublish', $offerGeneration));

    $unpublishResponse->assertRedirect(route('offers.show', $offerGeneration));
    $unpublishResponse->assertSessionHas('status', 'Offer unpublished successfully.');

    $offerGeneration->refresh();

    expect($offerGeneration->is_published)->toBeFalse();
    expect($offerGeneration->published_at?->equalTo($publishedAt))->toBeTrue();
});

test('plans index can be filtered by published state', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'plans.publish-filter',
        'title' => 'Plan Publish Prompt',
        'description' => 'Prompt used for publishing tests.',
        'system_prompt' => 'Write concise plans.',
        'user_prompt_template' => 'Plan: {{title}}',
        'module' => 'plans',
        'version' => 1,
        'is_active' => true,
    ]);

    $organization->strategyPlans()->create([
        'prompt_template_id' => $promptTemplate->id,
        'period_type' => 'weekly',
        'title' => 'Published plan',
        'goals_json' => [],
        'input_payload' => [],
        'output_text' => 'Published plan output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'approved',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $organization->strategyPlans()->create([
        'prompt_template_id' => $promptTemplate->id,
        'period_type' => 'monthly',
        'title' => 'Unpublished plan',
        'goals_json' => [],
        'input_payload' => [],
        'output_text' => 'Unpublished plan output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'reviewed',
        'is_published' => false,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('plans.index', ['published' => 'published']));

    $response->assertOk();
    $response->assertViewHas('strategyPlans', function (LengthAwarePaginator $strategyPlans): bool {
        $items = $strategyPlans->items();

        return count($items) === 1
            && $items[0] instanceof StrategyPlan
            && $items[0]->title === 'Published plan'
            && $items[0]->is_published === true;
    });
    $response->assertSee('Publishing', false);
    $response->assertSee('Published', false);
});

test('content show page displays publishing state and action controls', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.show-publishing',
        'title' => 'Content Show Publishing Prompt',
        'description' => 'Prompt used for publishing UI tests.',
        'system_prompt' => 'Write concise content.',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $contentGeneration = $organization->contentGenerations()->create([
        'prompt_template_id' => $promptTemplate->id,
        'type' => 'social_post',
        'title' => 'Reviewed content',
        'input_payload' => [],
        'output_text' => 'Reviewed content output',
        'language' => 'en',
        'tone' => 'clear',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'reviewed',
        'is_published' => false,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('content.show', $contentGeneration));

    $response->assertOk();
    $response->assertSee('Publishing Status', false);
    $response->assertSee('Unpublished', false);
    $response->assertSee('Publish Output', false);
});

test('dashboard exposes aggregate publishing summary counts', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $contentPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.dashboard-publish',
        'title' => 'Content Dashboard Prompt',
        'description' => 'Prompt used for dashboard publishing tests.',
        'system_prompt' => 'Write concise content.',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $offerPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.dashboard-publish',
        'title' => 'Offer Dashboard Prompt',
        'description' => 'Prompt used for dashboard publishing tests.',
        'system_prompt' => 'Write concise offer copy.',
        'user_prompt_template' => 'Offer: {{title}}',
        'module' => 'offers',
        'version' => 1,
        'is_active' => true,
    ]);

    $plansPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'plans.dashboard-publish',
        'title' => 'Plan Dashboard Prompt',
        'description' => 'Prompt used for dashboard publishing tests.',
        'system_prompt' => 'Write concise plans.',
        'user_prompt_template' => 'Plan: {{title}}',
        'module' => 'plans',
        'version' => 1,
        'is_active' => true,
    ]);

    $analysisPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'analysis.dashboard-publish',
        'title' => 'Analysis Dashboard Prompt',
        'description' => 'Prompt used for dashboard publishing tests.',
        'system_prompt' => 'Review this page.',
        'user_prompt_template' => 'Page: {{title}}',
        'module' => 'analysis',
        'version' => 1,
        'is_active' => true,
    ]);

    $organization->contentGenerations()->create([
        'prompt_template_id' => $contentPrompt->id,
        'type' => 'social_post',
        'title' => 'Published content',
        'input_payload' => [],
        'output_text' => 'Published content output',
        'language' => 'en',
        'tone' => 'clear',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'approved',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $organization->offerGenerations()->create([
        'prompt_template_id' => $offerPrompt->id,
        'title' => 'Unpublished offer',
        'offer_type' => 'discount_offer',
        'input_payload' => [],
        'output_text' => 'Unpublished offer output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'reviewed',
        'is_published' => false,
    ]);

    $organization->strategyPlans()->create([
        'prompt_template_id' => $plansPrompt->id,
        'period_type' => 'campaign',
        'title' => 'Published plan',
        'goals_json' => [],
        'input_payload' => [],
        'output_text' => 'Published plan output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'approved',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $organization->pageAnalyses()->create([
        'prompt_template_id' => $analysisPrompt->id,
        'page_title' => 'Unpublished analysis',
        'page_url' => 'https://example.com',
        'page_type' => 'homepage',
        'input_payload' => [],
        'findings_text' => null,
        'recommendations_text' => 'Review CTA clarity',
        'score' => null,
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'reviewed',
        'is_published' => false,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertViewHas('publishingSummary', [
        'published' => 2,
        'unpublished' => 2,
    ]);
    $response->assertSee('Publishing Summary', false);
    $response->assertSee('Published Outputs', false);
    $response->assertSee('Unpublished Outputs', false);
});
