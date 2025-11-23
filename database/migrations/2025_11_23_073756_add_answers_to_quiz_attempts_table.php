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
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Add columns for tracking answers and time
            $table->json('answers')->nullable()->after('score');
            $table->integer('time_taken')->default(0)->after('answers');
            $table->integer('total_questions')->default(0)->after('time_taken');
            $table->decimal('percentage', 5, 2)->nullable()->after('total_questions');
            $table->boolean('passed')->default(false)->after('percentage');
            
            // Add index for better query performance
            $table->index(['user_id', 'quiz_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn(['answers', 'time_taken', 'total_questions', 'percentage', 'passed']);
            $table->dropIndex(['user_id', 'quiz_id', 'status']);
        });
    }
};
