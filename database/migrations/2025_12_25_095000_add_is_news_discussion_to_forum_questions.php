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
        Schema::table('forum_questions', function (Blueprint $table) {
            $table->boolean('is_news_discussion')->default(false)->after('views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_questions', function (Blueprint $table) {
            $table->dropColumn(['is_news_discussion']);
        });
    }
};
