<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Template extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'template_category_id',
        'name',
        'slug',
        'description',
        'audience',
        'features_json',
        'benefits_json',
        'price',
        'sale_price',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'features_json' => 'array',
            'benefits_json' => 'array',
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function templateCategory(): BelongsTo
    {
        return $this->belongsTo(TemplateCategory::class);
    }
}
