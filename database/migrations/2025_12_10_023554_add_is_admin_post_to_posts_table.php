<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Check if the column exists first
        if (!Schema::hasColumn('posts', 'is_admin_post')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->boolean('is_admin_post')->default(0)->after('user_id');
            });
        }
    }
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('is_admin_post');
        });
    }
};
