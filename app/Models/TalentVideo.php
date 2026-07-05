<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TalentVideo extends Model
{
    use HasFactory;

    protected $table = 'talent_videos';

    protected $fillable = ['talent_id', 'youtube_id', 'order'];

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }
}