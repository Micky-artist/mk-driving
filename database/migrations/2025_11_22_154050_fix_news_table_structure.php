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
        // First, drop the news table if it exists
        Schema::dropIfExists('news');
        
        // Recreate the news table with the correct structure
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->json('title')->comment('Localized news title in JSON format');
            $table->string('slug')->unique();
            $table->json('content')->comment('Localized news content in JSON format');
            $table->string('image_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->json('meta_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the news table
        Schema::dropIfExists('news');
        
        // Recreate the original news table structure
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->json('title')->comment('Localized news title in JSON format');
            $table->json('content')->comment('Localized news content in JSON format');
            $table->string('image_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
