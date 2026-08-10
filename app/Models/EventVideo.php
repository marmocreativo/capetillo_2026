<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventVideo extends Model
{
    use HasFactory;

    protected $table = 'event_videos';

    protected $fillable = ['event_id', 'youtube_id', 'order'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}