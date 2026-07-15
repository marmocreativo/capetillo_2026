<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Talent extends Model
{
    use HasFactory;

    protected $table = 'talents';

    protected $fillable = [
        'name',
        'slug',
        'cover_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'content',
        'summary',
        'spotify_url',
        'highlights',
        'is_active',
        'orden',
        'destacado',
        'mostrar_network',
        'recomendaciones_network',
        'mostrar_party',
        'recomendaciones_party',
        'honorarios_default',
    ];

    protected $casts = [
        'highlights' => 'array',
        'destacado' => 'boolean',
        'mostrar_network' => 'boolean',
        'recomendaciones_network' => 'array',
        'mostrar_party' => 'boolean',
        'recomendaciones_party' => 'array',
        'honorarios_default' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Talent $talent) {
            if (empty($talent->slug)) {
                $talent->slug = 'contrataciones-' . Str::slug($talent->name);
            }
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TalentImage::class)->orderBy('order');
    }

    public function videos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TalentVideo::class)->orderBy('order');
    }

    public function rosterEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RosterTalent::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}