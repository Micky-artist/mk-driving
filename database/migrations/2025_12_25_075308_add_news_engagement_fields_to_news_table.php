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
        Schema::table('news', function (Blueprint $table) {
            // Add engagement tracking fields
            $table->integer('views')->default(0)->after('image_url');
            $table->integer('likes_count')->default(0)->after('views');
            $table->integer('comments_count')->default(0)->after('likes_count');
            $table->integer('shares_count')->default(0)->after('comments_count');
            $table->json('engagement_metrics')->nullable()->after('shares_count');
            
            // Add forum discussion link
            $table->foreignId('forum_question_id')->nullable()->after('engagement_metrics');
            $table->foreign('forum_question_id')->references('id')->on('forum_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['forum_question_id']);
            $table->dropColumn([
                'views',
                'likes_count',
                'comments_count',
                'shares_count',
                'engagement_metrics',
                'forum_question_id'
            ]);
        });
    }
};
