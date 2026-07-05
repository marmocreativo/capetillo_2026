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
    ];

    protected $casts = [
        'highlights' => 'array',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}