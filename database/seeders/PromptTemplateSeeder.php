<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\PromptTemplate;
use Illuminate\Database\Seeder;

class PromptTemplateSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $organization = Organization::query()->first();

        PromptTemplate::query()->updateOrCreate(
            ['key' => 'content.basic-marketing'],
            [
                'organization_id' => $organization?->id,
                'title' => 'Basic Marketing Content',
                'description' => 'Simple starter prompt for generating marketing copy.',
                'system_prompt' => 'You are a concise marketing assistant. Write clear, persuasive, and practical copy based on the provided request.',
                'user_prompt_template' => "Generate marketing copy.\nTitle: {{title}}\nType: {{type}}\nLanguage: {{language}}\nTone: {{tone}}\nContext: {{context}}",
                'module' => 'content',
                'version' => 1,
                'is_active' => true,
            ],
        );
    }
}
