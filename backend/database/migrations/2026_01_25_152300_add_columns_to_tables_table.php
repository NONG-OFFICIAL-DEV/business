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
        Schema::table('tables', function (Blueprint $table) {
            // Relations
            $table->string('area')->nullable()->after('is_available');
            $table->foreignId('current_order_id')
                ->nullable()
                ->after('table_number')
                ->constrained('orders')
                ->nullOnDelete();

            // Layout / UI
            $table->unsignedInteger('floor')
                ->nullable()
                ->after('area');

            $table->unsignedInteger('position')
                ->nullable()
                ->after('floor');

            // Reservation
            $table->timestamp('reserved_at')
                ->nullable()
                ->after('status');

            $table->timestamp('reserved_until')
                ->nullable()
                ->after('reserved_at');

            // QR / Extra
            $table->string('qr_code')
                ->nullable()
                ->after('reserved_until');
            $table->integer('capacity')
                ->nullable()
                ->after('qr_code');

            // Status
            $table->enum('status', [
                'available',
                'occupied',
                'reserved',
                'disabled'
            ])->default('available')->after('capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('tables', 'current_order_id')) {
                $table->dropForeign(['current_order_id']);
                $table->dropColumn('current_order_id');
            }

            // Drop other columns if they exist
            foreach (['capacity', 'floor', 'position', 'reserved_at', 'reserved_until', 'qr_code'] as $col) {
                if (Schema::hasColumn('tables', $col)) {
                    $table->dropColumn($col);
                }
            }

            // Remove deleted_at only if it exists
            if (Schema::hasColumn('tables', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};
