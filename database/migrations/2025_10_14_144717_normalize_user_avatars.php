<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove leading '/storage/' from any avatar that starts with it
        DB::table('users')
          ->where('avatar', 'like', '/storage/%')
          ->update(['avatar' => DB::raw("TRIM(LEADING '/storage/' FROM avatar)")]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add '/storage/' to normalized avatars that start with 'avatars/'
        DB::table('users')
          ->where('avatar', 'like', 'avatars/%')
          ->update(['avatar' => DB::raw("CONCAT('/storage/', avatar)")]);
    }
};
