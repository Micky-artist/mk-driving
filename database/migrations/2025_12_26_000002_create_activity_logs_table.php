<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity_type'); // login, quiz_started, quiz_completed, etc.
            $table->integer('points_awarded');
            $table->json('metadata')->nullable(); // Additional context like quiz_id, question_id, etc.
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index('activity_type');
            $table->index(['created_at', 'points_awarded']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
