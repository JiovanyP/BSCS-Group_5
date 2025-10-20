<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all users and manually clean up avatars
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            if (str_starts_with($user->avatar, '/storage/')) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'avatar' => substr($user->avatar, strlen('/storage/')),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add '/storage/' to avatars that start with 'avatars/'
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            if (str_starts_with($user->avatar, 'avatars/')) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'avatar' => '/storage/' . $user->avatar,
                    ]);
            }
        }
    }
};
