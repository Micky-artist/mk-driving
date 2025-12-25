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
        Schema::table('users', function (Blueprint $table) {
            // Add leaderboard and gamification fields
            $table->integer('points')->default(0)->after('subscription_plan_id');
            $table->integer('streak_days')->default(0)->after('points');
            $table->timestamp('last_activity_date')->nullable()->after('streak_days');
            $table->integer('quiz_completion_streak')->default(0)->after('last_activity_date');
            $table->integer('forum_contributions')->default(0)->after('quiz_completion_streak');
            $table->integer('helpful_answers')->default(0)->after('forum_contributions');
            $table->integer('current_rank')->default(0)->after('helpful_answers');
            $table->integer('previous_rank')->default(0)->after('current_rank');
            $table->json('achievement_badges')->nullable()->after('previous_rank');
            $table->timestamp('last_streak_date')->nullable()->after('achievement_badges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'points',
                'streak_days',
                'last_activity_date',
                'quiz_completion_streak',
                'forum_contributions',
                'helpful_answers',
                'current_rank',
                'previous_rank',
                'achievement_badges',
                'last_streak_date'
            ]);
        });
    }
};
