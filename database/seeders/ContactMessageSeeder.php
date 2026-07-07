<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\Talent;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    public function run(): void
    {
        $estados = config('estados_mexico');
        $statuses = ['contacto_inicial', 'seguimiento', 'contrato_cerrado', 'contrato_pagado'];

        $talents = Talent::inRandomOrder()->limit(20)->get();

        if ($talents->isEmpty()) {
            $this->command->warn('No hay talentos en la BD, se generarán mensajes sin talent_id.');
        }

        // 15 mensajes generales
        for ($i = 0; $i < 15; $i++) {
            ContactMessage::create([
                'type' => 'general',
                'talent_id' => null,
                'talent_name' => fake()->boolean(50)
                    ? $talents->isNotEmpty() ? $talents->random()->name : fake()->name()
                    : null,
                'name' => fake()->name(),
                'email' => fake()->safeEmail(),
                'phone' => fake()->boolean(80) ? fake()->numerify('55########') : null,
                'message' => fake()->realText(180),
                'estado_republica' => null,
                'aforo_esperado' => null,
                'venue' => null,
                'cotizacion_final' => fake()->boolean(30) ? fake()->randomFloat(2, 5000, 80000) : null,
                'status' => fake()->randomElement($statuses),
                'created_at' => fake()->dateTimeBetween('-2 months', 'now'),
            ]);
        }

        // 25 mensajes de contratación
        for ($i = 0; $i < 25; $i++) {
            $talent = $talents->isNotEmpty() ? $talents->random() : null;

            ContactMessage::create([
                'type' => 'contratacion',
                'talent_id' => $talent?->id,
                'talent_name' => $talent?->name ?? fake()->name(),
                'name' => fake()->name(),
                'email' => fake()->safeEmail(),
                'phone' => fake()->numerify('55########'),
                'message' => 'Me interesa contratar a ' . ($talent?->name ?? 'este talento') . ' para un evento. ' . fake()->realText(120),
                'estado_republica' => fake()->randomElement($estados),
                'aforo_esperado' => fake()->boolean(70) ? fake()->numberBetween(50, 5000) : null,
                'venue' => fake()->boolean(60) ? fake()->company() . ' - Salón de Eventos' : null,
                'cotizacion_final' => fake()->boolean(50) ? fake()->randomFloat(2, 15000, 250000) : null,
                'status' => fake()->randomElement($statuses),
                'created_at' => fake()->dateTimeBetween('-2 months', 'now'),
            ]);
        }

        $this->command->info('Se generaron 40 mensajes de contacto de prueba.');
    }
}