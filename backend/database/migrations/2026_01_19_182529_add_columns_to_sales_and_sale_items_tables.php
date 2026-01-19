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
        Schema::table('sales', function (Blueprint $table) {
            // Add column first as nullable, without unique
            $table->string('invoice_no', 100)->nullable();

            // Other columns
            $table->text('notes')->nullable();
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->enum('status', ['paid', 'refunded'])->default('paid');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('cascade');
        });


        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('discount', 12, 2)->default(0)->after('price');
            $table->decimal('tax', 12, 2)->default(0)->after('discount');
            $table->decimal('subtotal', 12, 2)->default(0)->after('tax'); // qty * price
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'notes', 'invoice_no', 'discount', 'status', 'cashier_id']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['discount', 'tax', 'subtotal']);
        });
    }
};
