<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number')->unique();
            $table->foreignUuid('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignUuid('payment_id')->unique()->constrained('payments')->onDelete('cascade');
            $table->datetime('date');
            $table->text('note')->nullable();
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['store_id', 'supplier_id']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_suppliers');
    }
};
