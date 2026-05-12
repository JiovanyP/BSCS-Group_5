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
        // Check if the column exists first
        if (!Schema::hasColumn('post_likes', 'vote_type')) {
            Schema::table('post_likes', function (Blueprint $table) {
                $table->enum('vote_type', ['up', 'down'])->default('up');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the column exists before trying to drop it
        if (Schema::hasColumn('post_likes', 'vote_type')) {
            Schema::table('post_likes', function (Blueprint $table) {
                $table->dropColumn('vote_type');
            });
        }
    }
};