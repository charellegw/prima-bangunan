<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('UUID()'));
            $table->foreignUuid('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignUuid('payment_id')->constrained('payments')->onDelete('cascade');
            $table->datetime('datetime')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('status', ['unpaid - progress', 'paid - progress', 'completed', 'refunded', 'canceled'])->default('unpaid - progress');
            $table->text('notes')->nullable();
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_suppliers');
    }
};
