<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['general', 'contratacion'])->default('general');

            $table->foreignId('talent_id')->nullable()->constrained('talents')->nullOnDelete();
            $table->string('talent_name')->nullable();

            $table->string('name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->text('message');

            $table->string('estado_republica')->nullable();
            $table->unsignedInteger('aforo_esperado')->nullable();
            $table->string('venue')->nullable();

            $table->decimal('cotizacion_final', 10, 2)->nullable();
            $table->enum('status', ['contacto_inicial', 'seguimiento', 'contrato_cerrado', 'contrato_pagado'])
                ->default('contacto_inicial');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};