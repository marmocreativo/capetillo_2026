<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'cover_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'order',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function talents(): BelongsToMany
    {
        return $this->belongsToMany(Talent::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}