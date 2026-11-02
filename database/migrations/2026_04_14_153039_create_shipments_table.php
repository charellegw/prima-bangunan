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
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('UUID()'));
            $table->foreignUuid('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->datetime('datetime')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('address');
            $table->decimal('fee', 15, 2)->default(0);
            $table->enum('status', ['pending', 'shipping', 'delivered', 'returned'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
