<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Comediantes',
            'Standuperos',
            'Youtuberos',
            'Conductores',
            'Artistas Musicales y Atractivo Visual',
            'Grupos y Bandas Musicales',
            'Banda',
            'Trios románticos',
            'Mariachis',
            'Música Pop, Romántica, Rock y Urbana',
            'Conferencistas',
            'Actores y Actrices',
            'Regional Mexicano y Ranchero',
        ];

        foreach ($categories as $order => $name) {
            Category::firstOrCreate(
                ['name' => $name],
                ['order' => $order, 'is_active' => true]
            );
        }
    }
}