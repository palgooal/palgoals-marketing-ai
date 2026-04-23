<?php

use App\Models\Organization;
use App\Models\PromptTemplate;
use App\Models\PromptTemplateVersion;
use App\Models\User;

test('updating a prompt template creates a snapshot and increments the live version', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.versioned-template',
        'title' => 'Versioned Template',
        'description' => 'Original prompt body.',
        'system_prompt' => 'Original system prompt',
        'user_prompt_template' => 'Original user prompt {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('prompts.update', $promptTemplate), [
        'key' => 'content.versioned-template',
        'title' => 'Versioned Template Updated',
        'description' => 'Updated prompt body.',
        'system_prompt' => 'Updated system prompt',
        'user_prompt_template' => 'Updated user prompt {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => '0',
    ]);

    $response->assertRedirect(route('prompts.edit', $promptTemplate));

    $promptTemplate->refresh();
    $snapshot = PromptTemplateVersion::query()->where('prompt_template_id', $promptTemplate->id)->first();

    expect($promptTemplate->version)->toBe(2);
    expect($promptTemplate->title)->toBe('Versioned Template Updated');
    expect($promptTemplate->is_active)->toBeFalse();

    expect($snapshot)->not->toBeNull();
    expect($snapshot->version_number)->toBe(1);
    expect($snapshot->title)->toBe('Versioned Template');
    expect($snapshot->description)->toBe('Original prompt body.');
    expect($snapshot->system_prompt)->toBe('Original system prompt');
    expect($snapshot->user_prompt_template)->toBe('Original user prompt {{title}}');
    expect($snapshot->is_active)->toBeTrue();
});

test('prompt template edit page shows the recent versions section', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.version-history-template',
        'title' => 'Offer Version Template',
        'description' => 'Current version.',
        'system_prompt' => 'Current system prompt',
        'user_prompt_template' => 'Current user prompt {{type}}',
        'module' => 'offers',
        'version' => 3,
        'is_active' => false,
    ]);

    $promptTemplate->versions()->create([
        'version_number' => 2,
        'title' => 'Offer Version Template',
        'description' => 'Previous version.',
        'system_prompt' => 'Previous system prompt',
        'user_prompt_template' => 'Previous user prompt {{type}}',
        'module' => 'offers',
        'is_active' => true,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('prompts.edit', $promptTemplate));

    $response->assertOk();
    $response->assertSee('Current Version: 3', false);
    $response->assertSee('Recent Versions', false);
    $response->assertSee('Version 2', false);
    $response->assertSee('Offer Version Template', false);
    $response->assertSee('Preview', false);
    $response->assertSee('Revert', false);
    $response->assertSee('Active Snapshot', false);
});

test('prompt template version can be previewed', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.preview-template',
        'title' => 'Current Prompt Title',
        'description' => 'Current prompt description.',
        'system_prompt' => 'Current system prompt',
        'user_prompt_template' => 'Current user prompt {{title}}',
        'module' => 'content',
        'version' => 4,
        'is_active' => true,
    ]);

    $version = $promptTemplate->versions()->create([
        'version_number' => 3,
        'title' => 'Preview Prompt Title',
        'description' => 'Preview prompt description.',
        'system_prompt' => 'Preview system prompt',
        'user_prompt_template' => 'Preview user prompt {{title}}',
        'module' => 'offers',
        'is_active' => false,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('prompts.versions.show', [$promptTemplate, $version]));

    $response->assertOk();
    $response->assertSee('Prompt Version Preview', false);
    $response->assertSee('Version Snapshot Preview', false);
    $response->assertSee('Preview Prompt Title', false);
    $response->assertSee('Preview prompt description.', false);
    $response->assertSee('Preview system prompt', false);
    $response->assertSee('Preview user prompt {{title}}', false);
});

test('reverting a prompt template version restores snapshot fields and increments the live version', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.revert-template',
        'title' => 'Live Prompt Title',
        'description' => 'Live prompt description.',
        'system_prompt' => 'Live system prompt',
        'user_prompt_template' => 'Live user prompt {{title}}',
        'module' => 'content',
        'version' => 5,
        'is_active' => true,
    ]);

    $version = $promptTemplate->versions()->create([
        'version_number' => 2,
        'title' => 'Historical Prompt Title',
        'description' => 'Historical prompt description.',
        'system_prompt' => 'Historical system prompt',
        'user_prompt_template' => 'Historical user prompt {{title}}',
        'module' => 'analysis',
        'is_active' => false,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('prompts.versions.revert', [$promptTemplate, $version]));

    $response->assertRedirect(route('prompts.edit', $promptTemplate));
    $response->assertSessionHas('status', 'Prompt template reverted from version 2 and a new snapshot was saved successfully.');

    $promptTemplate->refresh();

    expect($promptTemplate->title)->toBe('Historical Prompt Title');
    expect($promptTemplate->description)->toBe('Historical prompt description.');
    expect($promptTemplate->system_prompt)->toBe('Historical system prompt');
    expect($promptTemplate->user_prompt_template)->toBe('Historical user prompt {{title}}');
    expect($promptTemplate->module)->toBe('analysis');
    expect($promptTemplate->version)->toBe(6);
    expect($promptTemplate->is_active)->toBeTrue();

    $latestSnapshot = PromptTemplateVersion::query()
        ->where('prompt_template_id', $promptTemplate->id)
        ->orderByDesc('id')
        ->first();

    expect($latestSnapshot)->not->toBeNull();
    expect($latestSnapshot->version_number)->toBe(5);
    expect($latestSnapshot->title)->toBe('Live Prompt Title');
    expect($latestSnapshot->module)->toBe('content');
    expect($latestSnapshot->is_active)->toBeTrue();
});

test('prompt template version preview fails safely for a version from another prompt template', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.primary-template',
        'title' => 'Primary Prompt',
        'description' => 'Primary description.',
        'system_prompt' => 'Primary system prompt',
        'user_prompt_template' => 'Primary user prompt {{title}}',
        'module' => 'content',
        'version' => 1,
        'is_active' => true,
    ]);

    $otherPromptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.secondary-template',
        'title' => 'Secondary Prompt',
        'description' => 'Secondary description.',
        'system_prompt' => 'Secondary system prompt',
        'user_prompt_template' => 'Secondary user prompt {{title}}',
        'module' => 'offers',
        'version' => 1,
        'is_active' => false,
    ]);

    $otherVersion = $otherPromptTemplate->versions()->create([
        'version_number' => 1,
        'title' => 'Secondary Snapshot',
        'description' => 'Secondary snapshot description.',
        'system_prompt' => 'Secondary snapshot system prompt',
        'user_prompt_template' => 'Secondary snapshot user prompt {{title}}',
        'module' => 'offers',
        'is_active' => false,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('prompts.versions.show', [$promptTemplate, $otherVersion]));

    $response->assertNotFound();
});

test('prompt template compare page can compare a snapshot against the current live template', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'content.compare-live-template',
        'title' => 'Live Compare Prompt',
        'description' => 'Current description.',
        'system_prompt' => 'Current system prompt',
        'user_prompt_template' => 'Current user prompt {{title}}',
        'module' => 'content',
        'version' => 4,
        'is_active' => true,
    ]);

    $version = $promptTemplate->versions()->create([
        'version_number' => 3,
        'title' => 'Snapshot Compare Prompt',
        'description' => 'Current description.',
        'system_prompt' => 'Older system prompt',
        'user_prompt_template' => 'Current user prompt {{title}}',
        'module' => 'content',
        'is_active' => false,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('prompts.compare', [
        $promptTemplate,
        'from_version_id' => $version->id,
    ]));

    $response->assertOk();
    $response->assertSee('Prompt Comparison', false);
    $response->assertSee('Snapshot v3', false);
    $response->assertSee('Current Live Template', false);
    $response->assertSee('Changed', false);
    $response->assertSee('Unchanged', false);
    $response->assertSee('Older system prompt', false);
    $response->assertSee('Current system prompt', false);
});

test('prompt template compare page can compare two snapshots and fails safely for invalid versions', function () {
    /** @var \Tests\TestCase $this */

    $organization = Organization::query()->create([
        'name' => 'Palgoals',
        'slug' => 'palgoals',
        'status' => 'active',
    ]);

    $promptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'offers.compare-snapshots-template',
        'title' => 'Snapshot Compare Prompt',
        'description' => 'Live description.',
        'system_prompt' => 'Live system prompt',
        'user_prompt_template' => 'Live user prompt {{type}}',
        'module' => 'offers',
        'version' => 5,
        'is_active' => true,
    ]);

    $fromVersion = $promptTemplate->versions()->create([
        'version_number' => 2,
        'title' => 'Older Snapshot',
        'description' => 'Older description.',
        'system_prompt' => 'Older system prompt',
        'user_prompt_template' => 'Older user prompt {{type}}',
        'module' => 'offers',
        'is_active' => false,
    ]);

    $toVersion = $promptTemplate->versions()->create([
        'version_number' => 4,
        'title' => 'Newer Snapshot',
        'description' => 'Newer description.',
        'system_prompt' => 'Newer system prompt',
        'user_prompt_template' => 'Newer user prompt {{type}}',
        'module' => 'analysis',
        'is_active' => true,
    ]);

    $otherPromptTemplate = PromptTemplate::query()->create([
        'organization_id' => $organization->id,
        'key' => 'analysis.compare-guardrail-template',
        'title' => 'Guardrail Prompt',
        'description' => 'Guardrail description.',
        'system_prompt' => 'Guardrail system prompt',
        'user_prompt_template' => 'Guardrail user prompt {{title}}',
        'module' => 'analysis',
        'version' => 1,
        'is_active' => false,
    ]);

    $otherVersion = $otherPromptTemplate->versions()->create([
        'version_number' => 1,
        'title' => 'Other Snapshot',
        'description' => 'Other snapshot description.',
        'system_prompt' => 'Other snapshot system prompt',
        'user_prompt_template' => 'Other snapshot user prompt {{title}}',
        'module' => 'analysis',
        'is_active' => false,
    ]);

    $user = User::factory()->create();

    $compareResponse = $this->actingAs($user)->get(route('prompts.compare', [
        $promptTemplate,
        'from_version_id' => $fromVersion->id,
        'to_version_id' => $toVersion->id,
    ]));

    $compareResponse->assertOk();
    $compareResponse->assertSee('Snapshot v2', false);
    $compareResponse->assertSee('Snapshot v4', false);
    $compareResponse->assertSee('Older Snapshot', false);
    $compareResponse->assertSee('Newer Snapshot', false);
    $compareResponse->assertSee('Analysis', false);

    $invalidResponse = $this->actingAs($user)->get(route('prompts.compare', [
        $promptTemplate,
        'from_version_id' => $fromVersion->id,
        'to_version_id' => $otherVersion->id,
    ]));

    $invalidResponse->assertNotFound();
});
