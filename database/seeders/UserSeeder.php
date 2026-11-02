<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Admin Prima Bangunan',
                'password' => Hash::make('prima3003'),
                'status' => 'verified',
                'otp_verified' => true,
            ]
        );
    }
}
