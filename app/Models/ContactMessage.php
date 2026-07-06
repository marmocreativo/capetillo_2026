<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';

    protected $fillable = [
        'type',
        'talent_id',
        'talent_name',
        'name',
        'email',
        'phone',
        'message',
        'estado_republica',
        'aforo_esperado',
        'venue',
        'cotizacion_final',
        'status',
    ];

    protected $casts = [
        'aforo_esperado' => 'integer',
        'cotizacion_final' => 'decimal:2',
    ];

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }
}