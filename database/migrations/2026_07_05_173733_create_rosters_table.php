<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rosters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('intro_text')->nullable();
            $table->text('outro_text')->nullable();
            $table->json('logos')->nullable();
            $table->boolean('separar_por_categoria')->default(false);
            $table->unsignedTinyInteger('talentos_por_pagina')->default(4);
            $table->json('datos_contacto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rosters');
    }
};