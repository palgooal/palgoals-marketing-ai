<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'brand_name',
        'short_description',
        'long_description',
        'tone_of_voice',
        'primary_language',
        'secondary_language',
        'target_markets_json',
        'usp_json',
        'objections_json',
        'cta_preferences_json',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'target_markets_json' => 'array',
            'usp_json' => 'array',
            'objections_json' => 'array',
            'cta_preferences_json' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
