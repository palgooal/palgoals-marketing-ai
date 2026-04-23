<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromptTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'key',
        'title',
        'description',
        'system_prompt',
        'user_prompt_template',
        'module',
        'version',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function contentGenerations(): HasMany
    {
        return $this->hasMany(ContentGeneration::class);
    }

    public function offerGenerations(): HasMany
    {
        return $this->hasMany(OfferGeneration::class);
    }

    public function strategyPlans(): HasMany
    {
        return $this->hasMany(StrategyPlan::class);
    }

    public function pageAnalyses(): HasMany
    {
        return $this->hasMany(PageAnalysis::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PromptTemplateVersion::class);
    }

    public function latestVersion(): HasMany
    {
        return $this->versions()->latest('version_number')->limit(1);
    }
}
