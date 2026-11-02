<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number')->unique();
            $table->foreignUuid('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignUuid('payment_id')->unique()->constrained('payments')->onDelete('cascade');
            $table->datetime('date');
            $table->string('client_name');
            $table->enum('client_type', ['orang', 'retail'])->default('orang');
            $table->text('note')->nullable();
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['store_id', 'date']);
            $table->index('client_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
