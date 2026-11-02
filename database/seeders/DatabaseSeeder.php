<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            StoreSeeder::class,
            ItemCategorySeeder::class,
            ItemSeeder::class,
            SupplierSeeder::class,
            SupplierPriceSeeder::class,
            InvoiceSeeder::class,
            InvoiceDetailSeeder::class,
            InvoiceSupplierSeeder::class,
            InvoiceSupplierDetailSeeder::class,
            ShipmentSeeder::class,
            ShipmentDetailSeeder::class,
            PaymentSeeder::class,
            PaymentHistorySeeder::class,
        ]);
    }
}
