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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who receives the notification
            $table->foreignId('actor_id')->constrained('users')->onDelete('cascade'); // Who performed the action
            $table->foreignId('post_id')->constrained()->onDelete('cascade'); // Related post
            $table->foreignId('comment_id')->nullable()->constrained()->onDelete('cascade'); // Related comment (if applicable)
            $table->enum('type', ['upvote', 'downvote', 'comment', 'reply']); // Type of notification
            $table->boolean('is_read')->default(false); // Read status
            $table->timestamps();
            
            // Add index for faster queries
            $table->index(['user_id', 'is_read', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};