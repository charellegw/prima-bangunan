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
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('UUID()'));
            $table->foreignUuid('payment_id')->constrained('payments')->onDelete('cascade');
            $table->datetime('datetime')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('method', ['cash', 'bank_transfer', 'e-wallet', 'qris']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
    }
};
