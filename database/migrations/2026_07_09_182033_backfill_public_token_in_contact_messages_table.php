<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('contact_messages')
            ->whereNull('public_token')
            ->orderBy('id')
            ->each(function ($row) {
                DB::table('contact_messages')
                    ->where('id', $row->id)
                    ->update(['public_token' => Str::random(32)]);
            });
    }

    public function down(): void
    {
        // No revertimos: quitar tokens rompería enlaces ya compartidos.
    }
};