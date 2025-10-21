<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update the enum to include 'location_alert'
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('upvote', 'downvote', 'comment', 'reply', 'location_alert')");
    }

    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('upvote', 'downvote', 'comment', 'reply')");
    }
};