<?php

use App\Models\ContentGeneration;
use App\Models\OfferGeneration;
use App\Models\Organization;
use App\Models\PageAnalysis;
use App\Models\PromptTemplate;
use App\Models\StrategyPlan;
use App\Models\User;

test('plain text export routes return compact text output for generated records', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $contentPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.export-template',
        'title' => 'Content Export Template',
        'description' => 'Export test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $offerPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.export-template',
        'title' => 'Offer Export Template',
        'description' => 'Export test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Type: {{type}}',
        'module' => 'offers',
        'version' => 1,
        'is_active' => true,
    ]);

    $planPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'plans.export-template',
        'title' => 'Plan Export Template',
        'description' => 'Export test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Period: {{type}}',
        'module' => 'plans',
        'version' => 1,
        'is_active' => true,
    ]);

    $analysisPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'analysis.export-template',
        'title' => 'Analysis Export Template',
        'description' => 'Export test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Page: {{title}}',
        'module' => 'analysis',
        'version' => 1,
        'is_active' => true,
    ]);

    $contentGeneration = ContentGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $contentPrompt->id,
        'type' => 'social_post',
        'title' => 'Content Export',
        'input_payload' => ['context' => 'Export content'],
        'output_text' => 'Content output body',
        'language' => 'ar',
        'tone' => 'direct',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'draft',
    ]);

    $offerGeneration = OfferGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $offerPrompt->id,
        'title' => 'Offer Export',
        'offer_type' => 'discount_offer',
        'input_payload' => ['context' => 'Export offer'],
        'output_text' => 'Offer output body',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'reviewed',
    ]);

    $strategyPlan = StrategyPlan::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $planPrompt->id,
        'period_type' => 'monthly',
        'title' => 'Plan Export',
        'goals_json' => ['Goal A'],
        'input_payload' => ['context' => 'Export plan'],
        'output_text' => 'Plan output body',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'approved',
    ]);

    $pageAnalysis = PageAnalysis::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $analysisPrompt->id,
        'page_title' => 'Analysis Export',
        'page_url' => 'https://example.com/export',
        'page_type' => 'landing_page',
        'input_payload' => ['context' => 'Export analysis'],
        'findings_text' => 'Finding details',
        'recommendations_text' => 'Recommendation details',
        'score' => null,
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'completed',
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('content.export-text', $contentGeneration))
        ->assertOk()
        ->assertHeader('content-type', 'text/plain; charset=UTF-8')
        ->assertSeeText('Title: Content Export')
        ->assertSeeText('Status: draft')
        ->assertSeeText('Content output body');

    $this->actingAs($user)
        ->get(route('offers.export-text', $offerGeneration))
        ->assertOk()
        ->assertHeader('content-type', 'text/plain; charset=UTF-8')
        ->assertSeeText('Offer Type: discount_offer')
        ->assertSeeText('Status: reviewed')
        ->assertSeeText('Offer output body');

    $this->actingAs($user)
        ->get(route('plans.export-text', $strategyPlan))
        ->assertOk()
        ->assertHeader('content-type', 'text/plain; charset=UTF-8')
        ->assertSeeText('Period Type: monthly')
        ->assertSeeText('Status: approved')
        ->assertSeeText('Plan output body');

    $this->actingAs($user)
        ->get(route('analysis.export-text', $pageAnalysis))
        ->assertOk()
        ->assertHeader('content-type', 'text/plain; charset=UTF-8')
        ->assertSeeText('Page URL: https://example.com/export')
        ->assertSeeText('Status: draft')
        ->assertSeeText('Finding details')
        ->assertSeeText('Recommendation details');
});

test('package text export routes return richer internal handoff text for generated records', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $contentPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.package-template',
        'title' => 'Content Package Template',
        'description' => 'Package test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $offerPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.package-template',
        'title' => 'Offer Package Template',
        'description' => 'Package test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Type: {{type}}',
        'module' => 'offers',
        'version' => 1,
        'is_active' => true,
    ]);

    $planPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'plans.package-template',
        'title' => 'Plan Package Template',
        'description' => 'Package test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Period: {{type}}',
        'module' => 'plans',
        'version' => 1,
        'is_active' => true,
    ]);

    $analysisPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'analysis.package-template',
        'title' => 'Analysis Package Template',
        'description' => 'Package test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Page: {{title}}',
        'module' => 'analysis',
        'version' => 1,
        'is_active' => true,
    ]);

    $contentGeneration = ContentGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $contentPrompt->id,
        'type' => 'landing_copy',
        'title' => 'Content Package',
        'input_payload' => ['context' => 'Landing page handoff'],
        'output_text' => 'Content package output body',
        'language' => 'en',
        'tone' => 'direct',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'approved',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $offerGeneration = OfferGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $offerPrompt->id,
        'title' => 'Offer Package',
        'offer_type' => 'bundle_offer',
        'input_payload' => ['context' => 'Bundle handoff'],
        'output_text' => 'Offer package output body',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'reviewed',
    ]);

    $strategyPlan = StrategyPlan::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $planPrompt->id,
        'period_type' => 'campaign',
        'title' => 'Plan Package',
        'goals_json' => ['Goal One'],
        'input_payload' => ['context' => 'Plan handoff'],
        'output_text' => 'Plan package output body',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'approved',
    ]);

    $pageAnalysis = PageAnalysis::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $analysisPrompt->id,
        'page_title' => 'Analysis Package',
        'page_url' => 'https://example.com/package',
        'page_type' => 'landing_page',
        'input_payload' => [
            'context' => 'Analysis handoff',
            'page_content' => 'Package page content',
        ],
        'findings_text' => 'Package findings',
        'recommendations_text' => 'Package recommendations',
        'score' => 88,
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'completed',
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('content.export-package-text', $contentGeneration))
        ->assertOk()
        ->assertHeader('content-type', 'text/plain; charset=UTF-8')
        ->assertSeeText('Content Package')
        ->assertSeeText('Prompt Template: Content Package Template')
        ->assertSeeText('Context: Landing page handoff')
        ->assertSeeText('Content package output body');

    $this->actingAs($user)
        ->get(route('offers.export-package-text', $offerGeneration))
        ->assertOk()
        ->assertHeader('content-type', 'text/plain; charset=UTF-8')
        ->assertSeeText('Offer Package')
        ->assertSeeText('Offer Type: bundle_offer')
        ->assertSeeText('Offer package output body');

    $this->actingAs($user)
        ->get(route('plans.export-package-text', $strategyPlan))
        ->assertOk()
        ->assertHeader('content-type', 'text/plain; charset=UTF-8')
        ->assertSeeText('Plan Package')
        ->assertSeeText('Goals')
        ->assertSeeText('Goal One')
        ->assertSeeText('Plan package output body');

    $this->actingAs($user)
        ->get(route('analysis.export-package-text', $pageAnalysis))
        ->assertOk()
        ->assertHeader('content-type', 'text/plain; charset=UTF-8')
        ->assertSeeText('Analysis Package')
        ->assertSeeText('Page Content:')
        ->assertSeeText('Package page content')
        ->assertSeeText('Package findings')
        ->assertSeeText('Package recommendations');
});

test('package views render internal handoff details for generated records', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $contentPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.package-view-template',
        'title' => 'Content Package View Template',
        'description' => 'Package view test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $analysisPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'analysis.package-view-template',
        'title' => 'Analysis Package View Template',
        'description' => 'Package view test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Page: {{title}}',
        'module' => 'analysis',
        'version' => 1,
        'is_active' => true,
    ]);

    $contentGeneration = ContentGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $contentPrompt->id,
        'type' => 'social_post',
        'title' => 'Content Package View',
        'input_payload' => ['context' => 'Package view context'],
        'output_text' => 'Package view output',
        'language' => 'ar',
        'tone' => 'direct',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'reviewed',
    ]);

    $pageAnalysis = PageAnalysis::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $analysisPrompt->id,
        'page_title' => 'Analysis Package View',
        'page_url' => 'https://example.com/package-view',
        'page_type' => 'homepage',
        'input_payload' => [
            'context' => 'Analysis package context',
            'page_content' => 'Analysis page content',
        ],
        'findings_text' => null,
        'recommendations_text' => 'Analysis package output',
        'score' => null,
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'draft',
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('content.package', $contentGeneration))
        ->assertOk()
        ->assertSee('Content Package', false)
        ->assertSee('Package view context', false)
        ->assertSee('Package view output', false)
        ->assertSee('Input Payload Preview', false);

    $this->actingAs($user)
        ->get(route('analysis.package', $pageAnalysis))
        ->assertOk()
        ->assertSee('Analysis Package', false)
        ->assertSee('Analysis package context', false)
        ->assertSee('Analysis page content', false)
        ->assertSee('Analysis package output', false);
});

test('generated output pages expose copy and export usability actions', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $contentPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.copy-template',
        'title' => 'Content Copy Template',
        'description' => 'Copy test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $analysisPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'analysis.copy-template',
        'title' => 'Analysis Copy Template',
        'description' => 'Copy test prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Page: {{title}}',
        'module' => 'analysis',
        'version' => 1,
        'is_active' => true,
    ]);

    $contentGeneration = ContentGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $contentPrompt->id,
        'type' => 'social_post',
        'title' => 'Content Copy',
        'input_payload' => ['context' => 'Copy content'],
        'output_text' => 'Copyable content output',
        'language' => 'ar',
        'tone' => 'direct',
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'draft',
    ]);

    $pageAnalysis = PageAnalysis::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $analysisPrompt->id,
        'page_title' => 'Analysis Copy',
        'page_url' => 'https://example.com/copy',
        'page_type' => 'homepage',
        'input_payload' => ['context' => 'Copy analysis'],
        'findings_text' => null,
        'recommendations_text' => 'Copyable recommendation output',
        'score' => null,
        'model_name' => 'gpt-test',
        'provider_name' => 'openai',
        'status' => 'draft',
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('content.show', $contentGeneration))
        ->assertOk()
        ->assertSee('Copy Output', false)
        ->assertSee('Copy JSON', false)
        ->assertSee('Copy Title', false)
        ->assertSee('Export Text', false)
        ->assertSee('View Package', false)
        ->assertSee('Export Package Text', false);

    $this->actingAs($user)
        ->get(route('analysis.show', $pageAnalysis))
        ->assertOk()
        ->assertSee('Copy Output', false)
        ->assertSee('Copy URL', false)
        ->assertSee('Export Text', false)
        ->assertSee('View Package', false)
        ->assertSee('Export Package Text', false);

    $this->actingAs($user)
        ->get(route('content.index'))
        ->assertOk()
        ->assertSee('Export Text', false)
        ->assertSee('Package', false);

    $this->actingAs($user)
        ->get(route('analysis.index'))
        ->assertOk()
        ->assertSee('Export Text', false)
        ->assertSee('Package', false);
});
