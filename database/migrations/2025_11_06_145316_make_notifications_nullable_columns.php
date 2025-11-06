<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'post_id')) {
                $table->unsignedBigInteger('post_id')->nullable()->change();
            }
            if (Schema::hasColumn('notifications', 'comment_id')) {
                $table->unsignedBigInteger('comment_id')->nullable()->change();
            }
            if (Schema::hasColumn('notifications', 'data')) {
                $table->longText('data')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'post_id')) {
                $table->unsignedBigInteger('post_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('notifications', 'comment_id')) {
                $table->unsignedBigInteger('comment_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('notifications', 'data')) {
                $table->longText('data')->nullable(false)->change();
            }
        });
    }
};
