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
            // Add reference if it doesn't exist
            if (!Schema::hasColumn('payments', 'reference')) {
                $table->string('reference')->unique()->after('id');
            }
            
            // Add phone_number if it doesn't exist
            if (!Schema::hasColumn('payments', 'phone_number')) {
                $table->string('phone_number')->after('reference');
            }
            
            // Add plan_id if it doesn't exist
            if (!Schema::hasColumn('payments', 'plan_id')) {
                $table->foreignId('plan_id')
                    ->nullable()
                    ->constrained('subscription_plans')
                    ->onDelete('cascade')
                    ->after('user_id');
            }
            
            // Update status enum if needed
            if (Schema::hasColumn('payments', 'status')) {
                $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED', 'CANCELLED'])
                    ->default('PENDING')
                    ->change();
            }
            
            // Add completed_at if it doesn't exist
            if (!Schema::hasColumn('payments', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Only drop columns that were added in this migration
            if (Schema::hasColumn('payments', 'reference')) {
                $table->dropColumn('reference');
            }
            
            if (Schema::hasColumn('payments', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
            
            if (Schema::hasColumn('payments', 'plan_id')) {
                $table->dropForeign(['plan_id']);
                $table->dropColumn('plan_id');
            }
            
            if (Schema::hasColumn('payments', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            
            // Revert status to original values if needed
            if (Schema::hasColumn('payments', 'status')) {
                $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED'])
                    ->default('PENDING')
                    ->change();
            }
        });
    }
};
