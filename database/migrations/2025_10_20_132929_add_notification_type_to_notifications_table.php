<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('notification_type', ['priority', 'general', 'social'])->default('social');
            $table->string('accident_type')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['notification_type', 'accident_type', 'distance_km']);
        });
    }
};