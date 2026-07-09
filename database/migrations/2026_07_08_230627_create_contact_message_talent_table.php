<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_message_talent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_message_id')->constrained('contact_messages')->cascadeOnDelete();
            $table->foreignId('talent_id')->nullable()->constrained('talents')->nullOnDelete();
            $table->string('nombre')->nullable();
            $table->string('imagen')->nullable();
            $table->decimal('honorarios', 10, 2)->nullable();
            $table->text('incluye')->nullable();
            $table->text('condiciones_pago')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_message_talent');
    }
};