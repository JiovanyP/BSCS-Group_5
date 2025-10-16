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
        Schema::table('posts', function (Blueprint $table) {
            // Add a nullable 'image' column after 'content'
            // This stores paths to uploaded media (image, GIF, video)
            if (!Schema::hasColumn('posts', 'image')) {
                $table->string('image')->nullable()->after('content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Drop the column only if it exists
            if (Schema::hasColumn('posts', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};
