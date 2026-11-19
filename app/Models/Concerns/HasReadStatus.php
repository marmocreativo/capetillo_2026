<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasReadStatus
{
    public function initializeHasReadStatus(): void
    {
        $this->mergeCasts(['leido' => 'datetime']);
    }

    public function lector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leido_por');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('leido');
    }

    public function isUnread(): bool
    {
        return $this->leido === null;
    }

    /** Marca como leído (solo la primera vez) con la fecha y el usuario que lo abrió. */
    public function markAsRead(?int $userId): void
    {
        if ($this->leido !== null) {
            return;
        }

        $this->forceFill(['leido' => now(), 'leido_por' => $userId])->saveQuietly();
    }
}
