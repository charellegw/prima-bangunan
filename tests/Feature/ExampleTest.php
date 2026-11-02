<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard_and_modules(): void
    {
        $user = User::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Admin Prima',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('prima3003'),
            'status' => 'verified',
            'otp_verified' => true,
        ]);

        Store::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'owner_id' => $user->id,
            'name' => 'Prima Bangunan',
        ]);

        $this->actingAs($user);

        // Dashboard
        $response = $this->get('/');
        $response->assertStatus(200);

        // Items
        $response = $this->get('/items');
        $response->assertStatus(200);

        // Invoices
        $response = $this->get('/invoices');
        $response->assertStatus(200);

        // Invoice Suppliers
        $response = $this->get('/invoice-suppliers');
        $response->assertStatus(200);

        // Payments
        $response = $this->get('/payments');
        $response->assertStatus(200);

        // Shipments
        $response = $this->get('/shipments');
        $response->assertStatus(200);

        // Suppliers
        $response = $this->get('/suppliers');
        $response->assertStatus(200);
    }
}
