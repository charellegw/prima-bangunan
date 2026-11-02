<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use App\Models\Store;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::first();
        if (!$store) return;

        $categories = [
            'Semen & Mortar',
            'Cat & Pelapis',
            'Pipa & Sanitari',
            'Besi & Baja',
            'Kayu & Papan',
            'Alat Pertukangan & Hardware',
            'Kelistrikan',
            'Pompa Air & Aksesoris',
        ];

        foreach ($categories as $cat) {
            ItemCategory::firstOrCreate(
                ['store_id' => $store->id, 'name' => $cat]
            );
        }
    }
}
