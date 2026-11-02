<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('shipment_number')->unique();
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->foreignUuid('invoice_supplier_id')->nullable()->constrained('invoice_suppliers')->onDelete('cascade');
            $table->enum('type', ['outgoing', 'incoming'])->default('outgoing'); // outgoing = pengiriman ke pelanggan, incoming = kiriman masuk dari supplier
            $table->datetime('date');
            $table->string('address');
            $table->decimal('fee', 15, 2)->default(0);
            $table->enum('status', ['pending', 'shipping', 'delivered', 'returned'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('invoice_supplier_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
