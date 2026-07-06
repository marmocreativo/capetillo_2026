<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roster_talent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roster_id')->constrained('rosters')->cascadeOnDelete();
            $table->foreignId('talent_id')->constrained('talents')->cascadeOnDelete();
            $table->string('nombre')->nullable();
            $table->string('resumen_corto', 500)->nullable();
            $table->decimal('honorarios', 10, 2)->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();

            $table->unique(['roster_id', 'talent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roster_talent');
    }
};