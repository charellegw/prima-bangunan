<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            StoreSeeder::class,
            ItemCategorySeeder::class,
            SupplierSeeder::class,
            ItemSeeder::class,
            SupplierPriceSeeder::class,
            InvoiceSeeder::class,
            InvoiceSupplierSeeder::class,
        ]);
    }
}
