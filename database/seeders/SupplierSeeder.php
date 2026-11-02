<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::first();
        if (!$store) return;

        $suppliers = [
            [
                'name' => 'PT Semen Gresik Distribusi',
                'location' => 'Kawasan Industri Gresik, Jawa Timur',
                'phone' => '031-3981234',
            ],
            [
                'name' => 'CV Avian Mitra Abadi',
                'location' => 'Rungkut Industri, Surabaya, Jawa Timur',
                'phone' => '031-8432111',
            ],
            [
                'name' => 'PT Rucika Nusantara Jaya',
                'location' => 'Daan Mogot KM 12, Cengkareng, Jakarta Barat',
                'phone' => '021-5401122',
            ],
            [
                'name' => 'UD Baja Perkasa Makmur',
                'location' => 'Kawasan Industri Jatake, Tangerang, Banten',
                'phone' => '021-5908877',
            ],
            [
                'name' => 'PT Shimizu Pump Indonesia',
                'location' => 'Kawasan Industri Pulo Gadung, Jakarta Timur',
                'phone' => '021-4603344',
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(
                ['store_id' => $store->id, 'name' => $sup['name']],
                [
                    'location' => $sup['location'],
                    'phone' => $sup['phone'],
                ]
            );
        }
    }
}
