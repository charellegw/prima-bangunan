<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemCategory;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Bahan Bangunan
            ['name' => 'Semen'],
            ['name' => 'Tripleks'],
            ['name' => 'Kayu'],
            ['name' => 'Besi & Baja'],
            ['name' => 'Kawat'],
            ['name' => 'Paku'],
            ['name' => 'Skrup/Baut/Mur'],
            ['name' => 'Lem'],
            ['name' => 'Lakban/Isolasi'],

            // Lantai
            ['name' => 'Keramik & Granit'],
            
            // Cat & Tembok
            ['name' => 'Cat'],
            ['name' => 'Tinner'],
            ['name' => 'Kuas & Roller'],
            ['name' => 'Plester Tembok'],
            ['name' => 'Raskam'],

            // Pipa
            ['name' => 'Pipa'],
            ['name' => 'Sambungan Pipa'],
            ['name' => 'Fitting'],

            // Kamar Mandi & Air
            ['name' => 'Kloset'],
            ['name' => 'Keran & Shower'],
            ['name' => 'Wastafel'],
            ['name' => 'Pompa Air'],
            ['name' => 'Tangki Air'],

            // Alat & Perlengkapan
            ['name' => 'Gergaji'],
            ['name' => 'Gurinda'],
            ['name' => 'Palu'],
            ['name' => 'Sekop'],
            ['name' => 'Sikat'],
            ['name' => 'Alat Ukur'],
            ['name' => "Meteran"],
            ['name' => 'Sarung Tangan'],
            ['name' => "Sendok Semen"],
            ['name' => "Pahat"],
            ['name' => "Amplas"],
            ['name' => "Bor"],
            ['name' => "Tang"],
            ['name' => "Obeng"],
            ['name' => "Kunci Inggris"],
            ['name' => "Ember"],
            ['name' => "Gerobak"],
            ['name' => "Helm Las"],
            ['name' => 'Kapi/Kape Dempul'],
            
            // Ruangan
            ['name' => "Lampu"],
            ['name' => "Saklar & Stop Kontak"],
            ['name' => "Kabel"],
            ['name' => 'Plafon & Gypsum'],

            // Pintu
            ['name' => 'Gagang Pintu'],
            ['name' => 'Engsel'],
            ['name' => 'Kunci & Gembok'],

            // Atap & Rangka
            ['name' => 'Seng'],

            // Bahan Kimia
            ['name' => 'Spirtus'],
        ];

        foreach ($categories as $category) {
            ItemCategory::create($category);
        }
    }
}
