<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->restrictOnDelete();
            $table->integer('quantity')->default(1);
             $table->decimal('price', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('note')->nullable(); // no spicy, extra cheese
            $table->enum('kitchen_status', [
                'pending',     // order received, not started
                'accepted',    // kitchen accepted the order (optional but useful)
                'preparing',   // cooking in progress
                'ready',       // ready to serve / pickup
                'served',      // served to customer / picked up
                'cancelled'    // cancelled before preparation
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
