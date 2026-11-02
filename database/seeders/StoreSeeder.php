<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();

        Store::create([
            'owner_id' => $adminUser->id,
            'name' => 'Prima Bangunan',
            'timezone' => 'Asia/Jayapura',
        ]);
    }
}
