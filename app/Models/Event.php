<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'slug',
        'cover_image',
        'summary',
        'content',
        'servicios_especializados',
        'preguntas_frecuentes',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'orden',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'servicios_especializados' => 'array',
        'preguntas_frecuentes' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->slug)) {
                $event->slug = static::buildSlug($event->title);
            }
        });
    }

    public static function buildSlug(string $title, string $ciudad = 'cdmx'): string
    {
        return 'organizacion-de-' . Str::slug($title) . '-en-' . Str::slug($ciudad);
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EventImage::class)->orderBy('order');
    }

    public function videos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EventVideo::class)->orderBy('order');
    }

    public function contacts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EventContact::class)->latest();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}