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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 64)->unique()->comment('Unique identifier for anonymous visitors');
            $table->string('ip_address', 45)->nullable()->comment('IP address of visitor');
            $table->string('user_agent', 500)->nullable()->comment('Browser user agent string');
            $table->string('device_type', 100)->nullable()->comment('Device type: mobile, tablet, desktop, bot');
            $table->string('device_name', 100)->nullable()->comment('Device name: iPhone, Android, etc.');
            $table->string('browser', 100)->nullable()->comment('Browser name and version');
            $table->string('platform', 100)->nullable()->comment('Operating system');
            $table->string('country', 2)->nullable()->comment('Country code from IP geolocation');
            $table->string('city', 100)->nullable()->comment('City from IP geolocation');
            $table->boolean('is_registered_user')->default(false)->comment('Whether visitor is a registered user');
            $table->unsignedBigInteger('user_id')->nullable()->comment('User ID if registered');
            $table->timestamp('first_visit_at')->comment('First time this visitor was seen');
            $table->timestamp('last_visit_at')->comment('Most recent visit time');
            $table->integer('total_visits')->default(1)->comment('Total number of visits');
            $table->timestamps();

            $table->index(['ip_address', 'last_visit_at']);
            $table->index(['visitor_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
