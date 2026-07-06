<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->unsignedInteger('orden')->default(0)->after('is_active');
            $table->boolean('destacado')->default(false)->after('orden');
            $table->decimal('honorarios_default', 10, 2)->nullable()->after('destacado');
        });
    }

    public function down(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->dropColumn(['orden', 'destacado', 'honorarios_default']);
        });
    }
};