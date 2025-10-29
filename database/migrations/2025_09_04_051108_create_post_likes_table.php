<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('post_likes', 'vote_type')) {
            Schema::table('post_likes', function (Blueprint $table) {
                $table->enum('vote_type', ['up', 'down'])->default('up');
            });
        }
    }

    public function down(): void
    {
        Schema::table('post_likes', function (Blueprint $table) {
            $table->dropColumn('vote_type');
        });
    }
};
