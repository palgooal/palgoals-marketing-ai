<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $organization = Organization::query()->updateOrCreate(
            ['slug' => 'palgoals'],
            [
                'name' => 'Palgoals',
                'status' => 'active',
            ],
        );

        $organization->brandProfile()->updateOrCreate(
            ['organization_id' => $organization->id],
            [
                'brand_name' => 'Palgoals',
                'short_description' => 'Internal Palgoals marketing workspace foundation.',
                'long_description' => 'Palgoals Marketing AI internal foundation for brand, dashboard, and settings management.',
                'tone_of_voice' => 'Clear, confident, and practical.',
                'primary_language' => 'ar',
                'secondary_language' => 'en',
                'target_markets_json' => ['Palestine'],
                'usp_json' => ['Performance-focused digital execution'],
                'objections_json' => ['Need clearer proof of ROI'],
                'cta_preferences_json' => ['Book a consultation'],
            ],
        );
    }
}
