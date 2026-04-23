<?php

use App\Models\ContentGeneration;
use App\Models\Organization;
use App\Models\PromptTemplate;
use App\Models\User;

test('prompt templates index shows scope and management actions', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    PromptTemplate::query()->create([
        'organization_id' => null,
        'key' => 'content.global-template',
        'title' => 'Global Content Template',
        'description' => 'Shared across organizations.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 2,
        'is_active' => true,
    ]);

    PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.organization-template',
        'title' => 'Organization Offer Template',
        'description' => 'Scoped to the current organization.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Type: {{type}}',
        'module' => 'offers',
        'version' => 1,
        'is_active' => false,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('prompts.index'));

    $response->assertOk();
    $response->assertSee('Global', false);
    $response->assertSee('Organization', false);
    $response->assertSee('Duplicate', false);
    $response->assertSee('Activate', false);
    $response->assertSee('Deactivate', false);
});

test('prompt template can be duplicated into a new inactive draft', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.base-template',
        'title' => 'Base Template',
        'description' => 'Original prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 3,
        'is_active' => true,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('prompts.duplicate', $promptTemplate));

    $duplicate = PromptTemplate::query()->where('key', 'content.base-template.copy')->first();

    $response->assertRedirect(route('prompts.edit', $duplicate));

    expect($duplicate)->not->toBeNull();
    expect($duplicate->title)->toBe('Base Template Copy');
    expect($duplicate->organization_id)->toBe($organization->id);
    expect($duplicate->version)->toBe(1);
    expect($duplicate->is_active)->toBeFalse();
});

test('prompt template active status can be toggled', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'plans.toggle-template',
        'title' => 'Toggle Template',
        'description' => 'Toggle me.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Type: {{type}}',
        'module' => 'plans',
        'version' => 1,
        'is_active' => false,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('prompts.toggle-active', $promptTemplate));

    $response->assertRedirect(route('prompts.index'));

    expect($promptTemplate->fresh()->is_active)->toBeTrue();
});

test('prompt template edit page shows lightweight usage counts', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.usage-template',
        'title' => 'Usage Template',
        'description' => 'Usage prompt.',
        'system_prompt' => 'System prompt',
        'user_prompt_template' => 'Title: {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    ContentGeneration::query()->create([
        'organization_id' => $organization->id,
        'prompt_template_id' => $promptTemplate->id,
        'title' => 'Generated Content',
        'type' => 'article',
        'language' => 'English',
        'tone' => 'Professional',
        'input_payload' => ['context' => 'Usage test'],
        'output_text' => 'Output',
        'provider_name' => 'openai',
        'model_name' => 'gpt-test',
        'status' => 'completed',
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('prompts.edit', $promptTemplate));

    $response->assertOk();
    $response->assertSee('Content Usage', false);
    $response->assertSee('Organization', false);
    $response->assertSee('1', false);
});
