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
        // Drop all tables if they exist (in reverse order of dependency)
        $tables = [
            'messages',
            'conversations',
            'quiz_attempt_answers',
            'quiz_attempts',
            'options',
            'questions',
            'quizzes',
            'forum_answers',
            'forum_questions',
            'news',
            'blogs',
            'payments',
            'subscriptions',
            'subscription_plans',
            'predefined_qa',
            'failed_jobs',
            'job_batches',
            'sessions',
            'password_reset_tokens',
            'cache',
            'cache_locks',
            'users'
        ];

        $tables = array_merge($tables, [
            'conversations',
            'messages',
            'predefined_qa',
            'job_batches',
            'failed_jobs',
            'cache',
            'cache_locks',
            'password_reset_tokens',
            'sessions',
            'personal_access_tokens',
            'answers'
        ]);

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        // Create users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('role', ['USER', 'ADMIN', 'INSTRUCTOR'])->default('USER');
            $table->string('profile_image')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('has_attempted_guest_quiz')->default(false);
            $table->string('subscription_plan_id')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create subscription_plans table
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->json('name')->comment('Localized plan name in JSON format');
            $table->json('description')->nullable()->comment('Localized plan description in JSON format');
            $table->decimal('price', 10, 2);
            $table->string('slug')->unique();
            $table->json('features')->nullable()->comment('Localized features in JSON format');
            $table->integer('duration_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Create subscriptions table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['ACTIVE', 'EXPIRED', 'CANCELLED', 'PENDING'])->default('PENDING');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create quizzes table
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->json('title')->comment('Localized quiz title in JSON format');
            $table->json('description')->nullable()->comment('Localized quiz description in JSON format');
            $table->json('topics')->nullable()->comment('Array of topics in JSON format');
            $table->integer('time_limit_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_guest_quiz')->default(false);
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create questions table
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->json('text')->comment('Localized question text in JSON format');
            $table->string('image_url')->nullable();
            $table->string('type')->default('multiple_choice');
            $table->integer('points')->default(1);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('correct_option_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create options table
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->json('option_text')->comment('Localized option text in JSON format');
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Create quiz_attempts table
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['IN_PROGRESS', 'COMPLETED'])->default('IN_PROGRESS');
            $table->integer('score')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create quiz_attempt_answers table
        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('option_id')->constrained()->onDelete('cascade');
            $table->boolean('is_correct');
            $table->integer('points_earned')->default(0);
            $table->timestamps();
        });

        // Create forum_questions table
        Schema::create('forum_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('title')->comment('Localized question title in JSON format');
            $table->json('content')->comment('Localized question content in JSON format');
            $table->integer('views')->default(0);
            $table->integer('votes')->default(0);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Create forum_answers table
        Schema::create('forum_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('forum_questions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('content')->comment('Localized answer content in JSON format');
            $table->integer('votes')->default(0);
            $table->boolean('is_accepted')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Create news table
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->json('title')->comment('Localized news title in JSON format');
            $table->json('content')->comment('Localized news content in JSON format');
            $table->string('image_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create blogs table
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->json('title')->comment('Localized blog title in JSON format');
            $table->string('slug')->unique();
            $table->json('content')->comment('Localized blog content in JSON format');
            $table->json('meta_description')->nullable()->comment('Localized meta description in JSON format');
            $table->string('featured_image')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('RWF');
            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED'])->default('PENDING');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create conversations table
        Schema::create('conversations', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('conversation_id');
            $table->foreign('conversation_id')
                ->references('id')
                ->on('conversations')
                ->onDelete('cascade');
            $table->json('content');
            $table->boolean('is_from_user')->default(true);
            $table->timestamps();
        });

        // Create predefined_qa table
        Schema::create('predefined_qa', function (Blueprint $table) {
            $table->id();
            $table->json('question')->comment('Localized question in JSON format');
            $table->json('answer')->comment('Localized answer in JSON format');
            $table->string('category')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create job_batches table
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        // Create failed_jobs table
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Create cache table
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // Create cache_locks table
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // Create password_reset_tokens table
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Create sessions table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Create personal_access_tokens table
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });

        // Create answers table
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->json('answer_text')->comment('Localized answer text in JSON format');
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Add foreign key for correct_option_id in questions table
        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('correct_option_id')->references('id')->on('options')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order of creation to respect foreign key constraints
        $tables = [
            'messages',
            'conversations',
            'quiz_attempt_answers',
            'quiz_attempts',
            'answers',
            'options',
            'questions',
            'quizzes',
            'forum_answers',
            'forum_questions',
            'news',
            'blogs',
            'payments',
            'subscriptions',
            'subscription_plans',
            'predefined_qa',
            'personal_access_tokens',
            'failed_jobs',
            'job_batches',
            'sessions',
            'password_reset_tokens',
            'cache',
            'cache_locks',
            'users'
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};