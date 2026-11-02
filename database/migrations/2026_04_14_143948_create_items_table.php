<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('item_categories')->nullOnDelete();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('price', 15, 2)->default(0); // Harga untuk orang/konsumen umum
            $table->decimal('retail_price', 15, 2)->default(0); // Harga untuk toko/retail
            $table->string('image_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['store_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
