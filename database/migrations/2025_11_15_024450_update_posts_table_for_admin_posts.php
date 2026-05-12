<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            // Only add is_admin_post if it doesn't exist
            if (!Schema::hasColumn('posts', 'is_admin_post')) {
                $table->boolean('is_admin_post')->default(false)->after('user_id');
            }
            
            // Only add image_url if it doesn't exist
            if (!Schema::hasColumn('posts', 'image_url')) {
                $table->string('image_url', 500)->nullable()->after('content');
            }
            
            // Only add media_type if it doesn't exist
            if (!Schema::hasColumn('posts', 'media_type')) {
                $table->string('media_type', 50)->nullable()->after('image_url');
            }
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'is_admin_post')) {
                $table->dropColumn('is_admin_post');
            }
            if (Schema::hasColumn('posts', 'image_url')) {
                $table->dropColumn('image_url');
            }
            if (Schema::hasColumn('posts', 'media_type')) {
                $table->dropColumn('media_type');
            }
        });
    }
};