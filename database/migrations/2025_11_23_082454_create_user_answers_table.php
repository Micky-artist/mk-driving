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
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('quiz_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('answer_id')->nullable()->constrained()->onDelete('set null');
            $table->text('answer_text')->nullable(); // For free-form text answers
            $table->boolean('is_correct')->default(false);
            $table->integer('score')->default(0);
            $table->json('details')->nullable(); // For storing additional answer details if needed
            $table->timestamps();
            
            // Composite unique key to prevent duplicate answers
            $table->unique(['attempt_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
