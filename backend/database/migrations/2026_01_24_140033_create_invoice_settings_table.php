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
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('My Store');
            $table->string('logo_url')->nullable();
            $table->string('address')->nullable();       // Street, city
            $table->string('phone')->nullable();         // Telephone number
            $table->string('follow_us')->nullable();         // Email for contact
            $table->string('tax_number')->nullable();    // VAT/Tax ID
            $table->string('footer_text')->nullable();   // Thank you / notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};
