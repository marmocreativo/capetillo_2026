<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;


class Roster extends Model
{
    use HasFactory;

    protected $table = 'rosters';

    protected $fillable = [
        'name',
        'intro_text',
        'outro_text',
        'logos',
        'separar_por_categoria',
        'mostrar_honorarios',
        'talentos_por_pagina',
        'datos_contacto',
    ];

    protected $casts = [
        'logos' => 'array',
        'separar_por_categoria' => 'boolean',
        'mostrar_honorarios' => 'boolean',
        'talentos_por_pagina' => 'integer',
        'datos_contacto' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Roster $roster) {
            if (empty($roster->public_token)) {
                $roster->public_token = Str::random(32);
            }
        });
    }

    public function rosterTalents(): HasMany
    {
        return $this->hasMany(RosterTalent::class)->orderBy('orden');
    }
}