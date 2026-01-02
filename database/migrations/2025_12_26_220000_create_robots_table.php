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
        Schema::create('robots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('english_name');
            $table->string('kinyarwanda_name');
            $table->json('personality_traits')->comment('Robot personality characteristics');
            $table->decimal('skill_level', 3, 2)->default(0.5)->comment('0.0 to 1.0 skill level');
            $table->integer('daily_test_target')->default(3)->comment('Target tests per day');
            $table->integer('weekly_test_target')->default(15)->comment('Target tests per week');
            $table->json('activity_schedule')->comment('Preferred activity hours');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('robot_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('robot_id')->constrained()->onDelete('cascade');
            $table->enum('activity_type', ['TEST_STARTED', 'TEST_COMPLETED', 'ACHIEVEMENT_UNLOCKED', 'LEADERBOARD_POSITION']);
            $table->json('activity_data')->comment('Details about the activity');
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('robot_activities');
        Schema::dropIfExists('robots');
    }
};
