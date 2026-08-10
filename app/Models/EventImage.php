<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventImage extends Model
{
    use HasFactory;

    protected $table = 'event_images';

    protected $fillable = ['event_id', 'path', 'order'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}