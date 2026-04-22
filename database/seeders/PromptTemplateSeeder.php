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

        $templates = [
            [
                'key' => 'content.basic-marketing',
                'title' => 'Basic Marketing Content',
                'description' => 'Simple starter prompt for generating marketing copy.',
                'system_prompt' => 'You are a concise marketing assistant. Write clear, persuasive, and practical copy based on the provided request.',
                'user_prompt_template' => "Generate marketing copy.\nTitle: {{title}}\nType: {{type}}\nLanguage: {{language}}\nTone: {{tone}}\nContext: {{context}}",
                'module' => 'content',
                'version' => 1,
                'is_active' => true,
            ],
            [
                'key' => 'offers.basic-offer',
                'title' => 'Basic Offer Generator',
                'description' => 'Starter prompt for generating commercial offers.',
                'system_prompt' => 'You are a concise commercial offer assistant. Produce clear, practical offers with a strong value proposition, clear audience fit, and a direct call to action.',
                'user_prompt_template' => "Generate a commercial offer draft.\nTitle: {{title}}\nOffer type: {{type}}\nContext: {{context}}\nUse the provided placeholders when available and keep the result ready for marketing review.",
                'module' => 'offers',
                'version' => 1,
                'is_active' => true,
            ],
            [
                'key' => 'plans.basic-strategy',
                'title' => 'Basic Strategy Planner',
                'description' => 'Starter prompt for generating weekly or monthly marketing plans.',
                'system_prompt' => 'You are a concise planning assistant. Produce practical marketing plans with clear priorities, channels, next actions, and measurable focus.',
                'user_prompt_template' => "Generate a marketing strategy plan.\nTitle: {{title}}\nPeriod type: {{type}}\nContext: {{context}}\nGoals: {{goals}}\nUse the provided inputs when available and keep the result practical for immediate review.",
                'module' => 'plans',
                'version' => 1,
                'is_active' => true,
            ],
            [
                'key' => 'analysis.basic-page-review',
                'title' => 'Basic Page Analyzer',
                'description' => 'Starter prompt for analyzing a marketing page.',
                'system_prompt' => 'You are a concise page analysis assistant. Review the provided page information and produce practical findings and recommendations focused on clarity, conversion, and messaging.',
                'user_prompt_template' => "Analyze this marketing page.\nPage title: {{title}}\nPage type: {{type}}\nPage URL: {{page_url}}\nContext: {{context}}\nPage content: {{page_content}}\nUse the provided inputs when available and keep the review practical.",
                'module' => 'analysis',
                'version' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            PromptTemplate::query()->updateOrCreate(
                ['key' => $template['key']],
                [
                    'organization_id' => $organization?->id,
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'system_prompt' => $template['system_prompt'],
                    'user_prompt_template' => $template['user_prompt_template'],
                    'module' => $template['module'],
                    'version' => $template['version'],
                    'is_active' => $template['is_active'],
                ],
            );
        }
    }
}
