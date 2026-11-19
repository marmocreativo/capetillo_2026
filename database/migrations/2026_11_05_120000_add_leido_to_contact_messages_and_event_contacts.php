<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['contact_messages', 'event_contacts'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->timestamp('leido')->nullable()->index();
                $t->foreignId('leido_por')->nullable()->constrained('users')->nullOnDelete();
            });

            // Los registros existentes se consideran ya leídos (sin usuario asignado).
            DB::table($table)->update(['leido' => now()]);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropConstrainedForeignId('leido_por');
                $t->dropColumn('leido');
            });
        }
    }
};
