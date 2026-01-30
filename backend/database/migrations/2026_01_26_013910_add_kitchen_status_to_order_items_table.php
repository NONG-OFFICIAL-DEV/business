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
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('kitchen_status', [
                'pending',     // order received, not started
                'accepted',    // kitchen accepted the order (optional but useful)
                'preparing',   // cooking in progress
                'ready',       // ready to serve / pickup
                'served',      // served to customer / picked up
                'cancelled'    // cancelled before preparation
            ])->default('pending')->after('id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['kitchen_status']);
            $table->dropColumn('kitchen_status');
        });
    }
};
