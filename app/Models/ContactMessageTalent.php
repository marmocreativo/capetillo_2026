<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessageTalent extends Model
{
    use HasFactory;

    protected $table = 'contact_message_talent';

    protected $fillable = [
        'contact_message_id',
        'talent_id',
        'nombre',
        'imagen',
        'honorarios',
        'incluye',
        'condiciones_pago',
        'orden',
    ];

    protected $casts = [
        'honorarios' => 'decimal:2',
    ];

    public function contactMessage(): BelongsTo
    {
        return $this->belongsTo(ContactMessage::class);
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }
}