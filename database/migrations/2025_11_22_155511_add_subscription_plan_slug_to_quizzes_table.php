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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('subscription_plan_slug')->nullable()->after('creator_id');
            $table->foreign('subscription_plan_slug')
                  ->references('slug')
                  ->on('subscription_plans')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_slug']);
            $table->dropColumn('subscription_plan_slug');
        });
    }
};
