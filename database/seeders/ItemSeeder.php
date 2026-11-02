<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::first();
        if (!$store) return;

        $catSemen = ItemCategory::where('name', 'Semen & Mortar')->first();
        $catCat = ItemCategory::where('name', 'Cat & Pelapis')->first();
        $catPipa = ItemCategory::where('name', 'Pipa & Sanitari')->first();
        $catBesi = ItemCategory::where('name', 'Besi & Baja')->first();
        $catPompa = ItemCategory::where('name', 'Pompa Air & Aksesoris')->first();
        $catAlat = ItemCategory::where('name', 'Alat Pertukangan & Hardware')->first();

        $items = [
            [
                'name' => 'Pompa Air Shimizu PS-128 BIT',
                'category_id' => $catPompa?->id,
                'brand' => 'Shimizu',
                'stock' => 15,
                'price' => 520000,       // Harga orang (eceran)
                'retail_price' => 485000, // Harga retail (grosir/toko)
                'description' => 'Pompa air sumur dangkal otomatis hemat listrik daya 125 Watt',
                'image_url' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=400',
            ],
            [
                'name' => 'Pompa Air Booster Wasser PB-218 EA',
                'category_id' => $catPompa?->id,
                'brand' => 'Wasser',
                'stock' => 8,
                'price' => 890000,
                'retail_price' => 840000,
                'description' => 'Pompa pendorong daya dorong tinggi untuk instalasi water heater dan shower',
                'image_url' => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?w=400',
            ],
            [
                'name' => 'Semen Gresik PPC 50kg',
                'category_id' => $catSemen?->id,
                'brand' => 'Semen Gresik',
                'stock' => 120,
                'price' => 68000,
                'retail_price' => 64000,
                'description' => 'Semen Portland Pozzolan berkualitas SNI untuk plesteran dan cor beton',
                'image_url' => 'https://images.unsplash.com/photo-1590069261209-f8e9b8642343?w=400',
            ],
            [
                'name' => 'Semen Tiga Roda 50kg',
                'category_id' => $catSemen?->id,
                'brand' => 'Tiga Roda',
                'stock' => 95,
                'price' => 70000,
                'retail_price' => 66000,
                'description' => 'Semen serbaguna kuat tekan tinggi dan daya rekat prima',
                'image_url' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400',
            ],
            [
                'name' => 'Cat Tembok Avitex White 5kg',
                'category_id' => $catCat?->id,
                'brand' => 'Avian',
                'stock' => 40,
                'price' => 125000,
                'retail_price' => 115000,
                'description' => 'Cat tembok interior bermutu tinggi dengan formula anti jamur dan lumut',
                'image_url' => 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=400',
            ],
            [
                'name' => 'Cat Pelapis Anti Bocor No Drop 4kg',
                'category_id' => $catCat?->id,
                'brand' => 'Avian',
                'stock' => 30,
                'price' => 180000,
                'retail_price' => 168000,
                'description' => 'Pelapis kedap air elastis tahan cuaca dan anti rembes',
                'image_url' => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=400',
            ],
            [
                'name' => 'Pipa PVC Rucika AW 1/2 inch (4 Meter)',
                'category_id' => $catPipa?->id,
                'brand' => 'Rucika',
                'stock' => 85,
                'price' => 38000,
                'retail_price' => 34000,
                'description' => 'Pipa air bersih tekanan tinggi standard JIS',
                'image_url' => 'https://images.unsplash.com/photo-1607400201889-565b1dd75f8e?w=400',
            ],
            [
                'name' => 'Pipa PVC Rucika D 3 inch (4 Meter)',
                'category_id' => $catPipa?->id,
                'brand' => 'Rucika',
                'stock' => 50,
                'price' => 98000,
                'retail_price' => 90000,
                'description' => 'Pipa saluran air buangan limbah rumah tangga anti korosi',
                'image_url' => 'https://images.unsplash.com/photo-1542013936693-884638332954?w=400',
            ],
            [
                'name' => 'Besi Beton Polos 8mm (12 Meter SNI)',
                'category_id' => $catBesi?->id,
                'brand' => 'Krakatau Steel',
                'stock' => 200,
                'price' => 58000,
                'retail_price' => 53000,
                'description' => 'Besi tulangan struktur bangunan standar SNI sertifikat asli',
                'image_url' => 'https://images.unsplash.com/photo-1504917599217-d4dc5ebe6122?w=400',
            ],
            [
                'name' => 'Cangkul Baja Gagang Kayu Super',
                'category_id' => $catAlat?->id,
                'brand' => 'Banteng',
                'stock' => 25,
                'price' => 85000,
                'retail_price' => 75000,
                'description' => 'Cangkul baja pande besi tahan karat dan awet',
                'image_url' => 'https://images.unsplash.com/photo-1616401784845-180882ba9ba8?w=400',
            ],
        ];

        foreach ($items as $data) {
            Item::firstOrCreate(
                ['store_id' => $store->id, 'name' => $data['name']],
                array_merge($data, ['id' => (string) Str::uuid()])
            );
        }
    }
}
