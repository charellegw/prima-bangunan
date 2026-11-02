<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\SupplierPrice;
use Illuminate\Database\Seeder;

class SupplierPriceSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all()->keyBy('name');
        $items = Item::all()->keyBy('name');

        $priceMatrix = [
            'Pompa Air Shimizu PS-128 BIT' => [
                'PT Shimizu Pump Indonesia' => ['unit_price' => 430000, 'lusin_price' => 4950000],
                'UD Baja Perkasa Makmur' => ['unit_price' => 445000, 'lusin_price' => 5100000],
            ],
            'Pompa Air Booster Wasser PB-218 EA' => [
                'PT Shimizu Pump Indonesia' => ['unit_price' => 740000, 'lusin_price' => 8500000],
            ],
            'Semen Gresik PPC 50kg' => [
                'PT Semen Gresik Distribusi' => ['unit_price' => 57000, 'lusin_price' => 660000],
                'UD Baja Perkasa Makmur' => ['unit_price' => 58500, 'lusin_price' => 680000],
            ],
            'Semen Tiga Roda 50kg' => [
                'PT Semen Gresik Distribusi' => ['unit_price' => 59000, 'lusin_price' => 690000],
            ],
            'Cat Tembok Avitex White 5kg' => [
                'CV Avian Mitra Abadi' => ['unit_price' => 98000, 'lusin_price' => 1120000],
            ],
            'Cat Pelapis Anti Bocor No Drop 4kg' => [
                'CV Avian Mitra Abadi' => ['unit_price' => 145000, 'lusin_price' => 1650000],
            ],
            'Pipa PVC Rucika AW 1/2 inch (4 Meter)' => [
                'PT Rucika Nusantara Jaya' => ['unit_price' => 28000, 'lusin_price' => 320000],
            ],
            'Pipa PVC Rucika D 3 inch (4 Meter)' => [
                'PT Rucika Nusantara Jaya' => ['unit_price' => 75000, 'lusin_price' => 860000],
            ],
            'Besi Beton Polos 8mm (12 Meter SNI)' => [
                'UD Baja Perkasa Makmur' => ['unit_price' => 46000, 'lusin_price' => 530000],
            ],
            'Cangkul Baja Gagang Kayu Super' => [
                'UD Baja Perkasa Makmur' => ['unit_price' => 62000, 'lusin_price' => 710000],
            ],
        ];

        foreach ($priceMatrix as $itemName => $supList) {
            $item = $items->get($itemName);
            if (!$item) continue;

            foreach ($supList as $supName => $pricing) {
                $supplier = $suppliers->get($supName);
                if (!$supplier) continue;

                SupplierPrice::updateOrCreate(
                    [
                        'item_id' => $item->id,
                        'supplier_id' => $supplier->id,
                    ],
                    [
                        'unit_price' => $pricing['unit_price'],
                        'lusin_price' => $pricing['lusin_price'],
                    ]
                );
            }
        }
    }
}
