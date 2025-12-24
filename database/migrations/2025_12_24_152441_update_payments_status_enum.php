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
        Schema::table('payments', function (Blueprint $table) {
            // Update status enum to include all MTN API statuses + existing values
            $table->enum('status', [
                'PENDING',
                'SUCCESSFUL',
                'COMPLETED',
                'FAILED',
                'CANCELLED',
                'EXPIRED',
                'REJECTED',
                'TIMEOUT',
                'NOT_FOUND',
                'ERROR',
                'REFUNDED'
            ])->default('PENDING')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert to original status values
            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED'])->default('PENDING')->change();
        });
    }
};
