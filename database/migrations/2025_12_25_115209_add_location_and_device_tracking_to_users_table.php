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
        Schema::table('users', function (Blueprint $table) {
            // Location fields
            $table->string('country', 2)->nullable()->comment('ISO 3166-1 alpha-2 country code');
            $table->string('city')->nullable()->comment('City name');
            $table->string('timezone')->nullable()->comment('User timezone');
            
            // Device tracking fields
            $table->string('device_fingerprint', 128)->nullable()->comment('Unique device identifier');
            $table->string('registration_ip', 45)->nullable()->comment('IP address at registration');
            $table->text('registration_user_agent')->nullable()->comment('User agent at registration');
            $table->string('registration_device_type', 50)->nullable()->comment('Device type at registration');
            $table->string('registration_browser')->nullable()->comment('Browser at registration');
            $table->string('registration_platform')->nullable()->comment('Platform at registration');
            
            // Timestamps for tracking
            $table->timestamp('registered_at')->nullable()->comment('Registration timestamp');
            $table->timestamp('last_seen_at')->nullable()->comment('Last activity timestamp');
            
            // Indexes for performance
            $table->index(['country', 'city']);
            $table->index(['device_fingerprint']);
            $table->index(['registration_ip']);
            $table->index(['last_seen_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['country', 'city']);
            $table->dropIndex(['device_fingerprint']);
            $table->dropIndex(['registration_ip']);
            $table->dropIndex(['last_seen_at']);
            
            $table->dropColumn([
                'country',
                'city', 
                'timezone',
                'device_fingerprint',
                'registration_ip',
                'registration_user_agent',
                'registration_device_type',
                'registration_browser',
                'registration_platform',
                'registered_at',
                'last_seen_at'
            ]);
        });
    }
};
