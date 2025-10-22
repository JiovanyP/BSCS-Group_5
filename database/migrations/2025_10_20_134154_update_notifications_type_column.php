<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support MODIFY or ENUM, so use string instead
        Schema::table('notifications', function (Blueprint $table) {
            // If the column doesn't exist, create it
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->nullable();
            } else {
                // Otherwise, change its type to string (text)
                $table->text('type')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the column if you want to revert
            $table->dropColumn('type');
        });
    }
};
