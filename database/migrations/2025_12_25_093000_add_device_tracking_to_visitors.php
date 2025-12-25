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
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('device_fingerprint', 128)->nullable()->after('visitor_id')->comment('Unique device identifier');
            $table->string('session_id', 128)->nullable()->after('device_fingerprint')->comment('Browser session identifier');
            $table->boolean('is_known_device')->default(false)->after('is_registered_user')->comment('Device previously associated with registered user');
            $table->timestamp('device_first_seen_at')->nullable()->after('first_visit_at')->comment('When this device was first identified');
            
            // Add indexes for better performance
            $table->index(['device_fingerprint', 'last_visit_at']);
            $table->index(['session_id', 'last_visit_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex(['device_fingerprint', 'last_visit_at']);
            $table->dropIndex(['session_id', 'last_visit_at']);
            $table->dropColumn(['device_fingerprint', 'session_id', 'is_known_device', 'device_first_seen_at']);
        });
    }
};
