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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->dateTime('sale_date');
            $table->decimal('total_amount', 10, 2);
            $table->string('invoice_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->enum('status', ['paid', 'refunded'])->default('paid');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('queue_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
