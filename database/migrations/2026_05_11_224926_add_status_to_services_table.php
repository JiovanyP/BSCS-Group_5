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
    Schema::table('services', function (Blueprint $table) {
        // Track who submitted it
        $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
        
        // Track the approval status (defaults to pending)
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            //
        });
    }
};
