<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add status and timestamps for moderation actions
            $table->string('status')->default('active')->after('password');
            $table->timestamp('suspended_at')->nullable()->after('status');
            $table->timestamp('banned_at')->nullable()->after('suspended_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'suspended_at', 'banned_at']);
        });
    }
};
