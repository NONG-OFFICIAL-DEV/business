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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            // $table->foreignId('product_id')->constrained()->onDelete('cascade');
            // Polymorphic relation
            $table->morphs('sellable');
            // creates:
            // sellable_id
            // sellable_type
            $table->integer('quantity');
            $table->string('name')->nullable(); // snapshot (important!)
            $table->decimal('price', 10, 2); // unit price
            $table->decimal('total', 10, 2);
            $table->decimal('discount', 12, 2)->default(0)->after('price');
            $table->decimal('tax', 12, 2)->default(0)->after('discount');
            $table->decimal('subtotal', 12, 2)->default(0)->after('tax'); // qty * price
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
