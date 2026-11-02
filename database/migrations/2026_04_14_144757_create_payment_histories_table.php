<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('payment_id')->constrained('payments')->onDelete('cascade');
            $table->datetime('date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('method')->default('cash'); // cash, transfer, qris, etc.
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index('payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
    }
};
