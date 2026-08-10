<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SearchHistory extends Model
{
    use HasFactory;

    protected $table = 'search_histories';

    protected $fillable = [
        'query',
        'normalized_query',
        'results_count',
        'ip_address',
    ];

    protected $casts = [
        'results_count' => 'integer',
    ];

    public static function log(string $query, int $resultsCount, ?string $ip = null): void
    {
        $query = trim($query);

        if ($query === '') {
            return;
        }

        static::create([
            'query' => $query,
            'normalized_query' => Str::of($query)->lower()->squish(),
            'results_count' => $resultsCount,
            'ip_address' => $ip,
        ]);
    }
}