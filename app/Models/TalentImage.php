<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TalentImage extends Model
{
    use HasFactory;

    protected $table = 'talent_images';

    protected $fillable = ['talent_id', 'path', 'order'];

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }
}