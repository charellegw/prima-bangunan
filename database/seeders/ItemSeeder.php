<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\ItemCategory;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $store = Store::where('name', 'Prima Bangunan')->first();
        $categorySemen = ItemCategory::where('name', 'Semen')->first();
        $categoryCat = ItemCategory::where('name', 'Cat')->first();
        $categoryPompaAir = ItemCategory::where('name', 'Pompa Air')->first();

        $items = [
            [
                'category_id' => $categorySemen->id,
                'store_id' => $store->id,
                'name' => 'Semen Tonasa',
                'brand' => 'Tonasa',
                'stock' => 50,
                'description' => 'Semen berkualitas tinggi untuk konstruksi',
                'price' => 320000,
                'reseller_price' => 310000,
            ],
            [
                'category_id' => $categoryCat->id,
                'store_id' => $store->id,
                'name' => 'Cat Tembok Salted Egg 1kg',
                'brand' => 'Avian',
                'stock' => 25,
                // 'description' => 'Cat tembok dengan warna salted egg',
                'price' => 90000,
                'reseller_price' => 87000,
            ],
            [
                'category_id' => $categorySemen->id,
                'store_id' => $store->id,
                'name' => 'Semen Tonasa 50kg',
                'brand' => 'Tonasa',
                'stock' => 50,
                'description' => 'Semen berkualitas tinggi untuk konstruksi',
                'price' => 250000,
                'reseller_price' => 200000,
            ],
            [
                'category_id' => $categoryPompaAir->id,
                'store_id' => $store->id,
                'name' => 'Pompa Air Sanyo PWH-137C',
                'brand' => 'Sanyo',
                'stock' => 50,
                'description' => '',
                'price' => 250000,
                'reseller_price' => 200000,
            ],
            [
                'category_id' => $categoryPompaAir->id,
                'store_id' => $store->id,
                'name' => 'Pompa Air Shimizu PS-135 E',
                'brand' => 'Shimizu',
                'stock' => 50,
                // 'description' => 'Semen berkualitas tinggi untuk konstruksi',
                'price' => 250000,
                'reseller_price' => 200000,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
