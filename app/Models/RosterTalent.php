<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RosterTalent extends Model
{
    use HasFactory;

    protected $table = 'roster_talent';

    protected $fillable = [
        'roster_id',
        'talent_id',
        'nombre',
        'resumen_corto',
        'honorarios',
        'orden',
    ];

    protected $casts = [
        'honorarios' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (RosterTalent $rosterTalent) {
            if ($rosterTalent->talent) {
                $rosterTalent->nombre = $rosterTalent->nombre ?? $rosterTalent->talent->name;
                $rosterTalent->resumen_corto = $rosterTalent->resumen_corto ?? $rosterTalent->talent->summary;
                $rosterTalent->honorarios = $rosterTalent->honorarios ?? $rosterTalent->talent->honorarios_default;
            }
        });
    }

    public function roster(): BelongsTo
    {
        return $this->belongsTo(Roster::class);
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }
}