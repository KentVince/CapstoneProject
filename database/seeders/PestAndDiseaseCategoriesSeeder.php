<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PestAndDiseaseCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table('pest_and_disease_categories')->insert([
            [
                'type' => 'Pest',
                'name' => 'Coffee Bean Borer',
                'description' => 'Attacks coffee beans.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Pest',
                'name' => 'Aphids',
                'description' => 'Sucks plant sap.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Pest',
                'name' => 'Mealybugs',
                'description' => 'Causes leaf discoloration.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Disease',
                'name' => 'Coffee Rust',
                'description' => 'Fungal infection.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Disease',
                'name' => 'Cercospora Leaf Spot',
                'description' => 'Leaf spots on coffee plants.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Disease',
                'name' => 'Sooty Molds',
                'description' => 'Black mold on leaves.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
