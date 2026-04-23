<?php

use App\Models\ContentGeneration;
use App\Models\OfferGeneration;
use App\Models\Organization;
use App\Models\PageAnalysis;
use App\Models\PromptTemplate;
use App\Models\StrategyPlan;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

test('prompt templates index shows compact usage counts and supports used filter', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $usedPromptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.used-template',
        'title' => 'Used Template',
        'description' => 'Used prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $unusedPromptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.unused-template',
        'title' => 'Unused Template',
        'description' => 'Unused prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'offers',
        'version' => 1,
        'is_active' => false,
    ]);

    ContentGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $usedPromptTemplate->id,
        'title' => 'Used content',
        'type' => 'social_post',
        'language' => 'en',
        'tone' => 'Direct',
        'input_payload' => ['context' => 'Used filter'],
        'output_text' => 'Output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'completed',
    ]);

    OfferGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $usedPromptTemplate->id,
        'title' => 'Used offer',
        'offer_type' => 'discount_offer',
        'input_payload' => ['context' => 'Used filter'],
        'output_text' => 'Output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'reviewed',
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('prompts.index', ['usage' => 'used']));

    $response->assertOk();
    $response->assertSee('Usage', false);
    $response->assertSee('Total 2', false);
    $response->assertSee('C 1', false);
    $response->assertSee('O 1', false);
    $response->assertViewHas('promptTemplates', function (LengthAwarePaginator $promptTemplates) use ($usedPromptTemplate, $unusedPromptTemplate): bool {
        $items = $promptTemplates->items();

        return count($items) === 1
            && $items[0] instanceof PromptTemplate
            && $items[0]->is($usedPromptTemplate)
            && ! $items[0]->is($unusedPromptTemplate);
    });
});

test('prompt templates index shows unused active prompt health hint', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'analysis.unused-active-template',
        'title' => 'Unused Active Prompt',
        'description' => 'Active but unused prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'analysis',
        'version' => 1,
        'is_active' => true,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('prompts.index'));

    $response->assertOk();
    $response->assertSee('Unused Active Prompt', false);
});

test('prompt template edit page shows usage summary and recent usage links', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.usage-insights-template',
        'title' => 'Usage Insights Template',
        'description' => 'Usage insights prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $contentGeneration = ContentGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $promptTemplate->id,
        'title' => 'Recent Content Usage',
        'type' => 'social_post',
        'language' => 'en',
        'tone' => 'Direct',
        'input_payload' => ['context' => 'Recent content usage'],
        'output_text' => 'Output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'completed',
    ]);

    StrategyPlan::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $promptTemplate->id,
        'title' => 'Recent Plan Usage',
        'period_type' => 'monthly',
        'goals_json' => ['Goal A'],
        'input_payload' => ['context' => 'Recent plan usage'],
        'output_text' => 'Plan output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'approved',
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('prompts.edit', $promptTemplate));

    $response->assertOk();
    $response->assertSee('Usage Summary', false);
    $response->assertSee('Total Usage', false);
    $response->assertSee('Recent Usage', false);
    $response->assertSee('2', false);
    $response->assertSee('Recent Content Usage', false);
    $response->assertSee('Recent Plan Usage', false);
    $response->assertSee(route('content.show', $contentGeneration), false);
});

test('dashboard shows lightweight prompt activity insights', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $activePrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.dashboard-prompt-active',
        'title' => 'Dashboard Active Prompt',
        'description' => 'Prompt for dashboard usage tests.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $unusedPrompt = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.dashboard-prompt-unused',
        'title' => 'Dashboard Unused Prompt',
        'description' => 'Unused prompt for dashboard usage tests.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'offers',
        'version' => 1,
        'is_active' => false,
    ]);

    ContentGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $activePrompt->id,
        'title' => 'Dashboard usage content',
        'type' => 'social_post',
        'language' => 'en',
        'tone' => 'Direct',
        'input_payload' => ['context' => 'Dashboard usage'],
        'output_text' => 'Output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'completed',
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Prompt Activity', false);
    $response->assertSee('Total Prompt Templates', false);
    $response->assertSee('Active Prompt Templates', false);
    $response->assertSee('Unused Prompt Templates', false);
    $response->assertSee('Unused Active Prompts', false);
    $response->assertSee(route('prompts.index'), false);
    $response->assertViewHas('promptInsights', [
        'total' => 2,
        'active' => 1,
        'unused' => 1,
        'unused_active' => 0,
    ]);
});
