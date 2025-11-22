<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new columns to subscription_plans table
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Add max_quizzes column with default value 0
            if (!Schema::hasColumn('subscription_plans', 'max_quizzes')) {
                $table->integer('max_quizzes')->default(0)->after('price');
            }
            
            // Add duration column with default value 30 (for 30 days)
            if (!Schema::hasColumn('subscription_plans', 'duration')) {
                $table->integer('duration')->default(30)->after('max_quizzes');
            }
            
            // Add color column for UI theming
            if (!Schema::hasColumn('subscription_plans', 'color')) {
                $table->string('color', 20)->nullable()->after('features');
            }
            
            // Rename duration_days to duration_in_days for clarity if it exists
            if (Schema::hasColumn('subscription_plans', 'duration_days') && !Schema::hasColumn('subscription_plans', 'duration_in_days')) {
                $table->renameColumn('duration_days', 'duration_in_days');
            }
        });
        
        // Update the quizzes table to make subscription_plan_id nullable if it exists
        if (Schema::hasTable('quizzes') && Schema::hasColumn('quizzes', 'subscription_plan_id')) {
            // Just make the column nullable - the foreign key is already set up in the base schema
            Schema::table('quizzes', function (Blueprint $table) {
                $table->unsignedBigInteger('subscription_plan_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a one-way migration due to potential data loss when reversing
        // the column type changes. In a production environment, you would want
        // to create a backup before running this migration.
        
        // Note: The down method is intentionally left empty as a safety measure.
        // Rolling back these changes could cause data loss and should be handled
        // with extreme care, possibly requiring a custom rollback script.
    }
};
