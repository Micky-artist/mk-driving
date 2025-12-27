<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('point_configurations');
        
        Schema::create('point_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('activity_type')->unique();
            $table->integer('points')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('conditions')->nullable(); // Additional conditions like cooldowns, limits, etc.
            $table->timestamps();

            $table->index('activity_type');
        });

        // Insert default point configurations
        DB::table('point_configurations')->insert([
            ['activity_type' => 'login', 'points' => 5, 'is_active' => true, 'conditions' => json_encode(['cooldown_hours' => 1]), 'created_at' => now(), 'updated_at' => now()],
            ['activity_type' => 'quiz_started', 'points' => 5, 'is_active' => true, 'conditions' => null, 'created_at' => now(), 'updated_at' => now()],
            ['activity_type' => 'quiz_completed', 'points' => 5, 'is_active' => true, 'conditions' => null, 'created_at' => now(), 'updated_at' => now()],
            ['activity_type' => 'quiz_passed', 'points' => 5, 'is_active' => true, 'conditions' => null, 'created_at' => now(), 'updated_at' => now()],
            ['activity_type' => 'quiz_perfect', 'points' => 10, 'is_active' => true, 'conditions' => null, 'created_at' => now(), 'updated_at' => now()],
            ['activity_type' => 'question_asked', 'points' => 5, 'is_active' => true, 'conditions' => json_encode(['cooldown_hours' => 1]), 'created_at' => now(), 'updated_at' => now()],
            ['activity_type' => 'question_answered', 'points' => 5, 'is_active' => true, 'conditions' => json_encode(['cooldown_hours' => 0.5]), 'created_at' => now(), 'updated_at' => now()],
            ['activity_type' => 'best_answer_selected', 'points' => 5, 'is_active' => true, 'conditions' => null, 'created_at' => now(), 'updated_at' => now()],
            ['activity_type' => 'daily_visit', 'points' => 5, 'is_active' => true, 'conditions' => json_encode(['cooldown_hours' => 24]), 'created_at' => now(), 'updated_at' => now()],
            ['activity_type' => 'account_created', 'points' => 50, 'is_active' => true, 'conditions' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('point_configurations');
    }
};
