<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->first();

        Store::firstOrCreate(
            ['name' => 'Prima Bangunan'],
            [
                'id' => (string) Str::uuid(),
                'owner_id' => $admin?->id,
                'timezone' => 'Asia/Jakarta',
                'address' => 'Jl. Raya Industri Bangunan No. 88, Jakarta Barat',
                'phone' => '021-5558899',
            ]
        );
    }
}
