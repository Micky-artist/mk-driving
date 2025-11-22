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
            $table->timestamp('published_at')->nullable()->after('content');
            $table->string('featured_image')->nullable()->after('is_published');
            $table->json('meta_description')->nullable()->after('featured_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['published_at', 'featured_image', 'meta_description']);
        });
    }
};
