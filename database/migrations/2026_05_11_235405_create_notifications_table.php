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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('actor_id')->index('notifications_actor_id_foreign');
            $table->unsignedBigInteger('post_id')->nullable()->index('notifications_post_id_foreign');
            $table->unsignedBigInteger('comment_id')->nullable()->index('notifications_comment_id_foreign');
            $table->text('type')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->enum('notification_type', ['priority', 'general', 'social'])->default('social');
            $table->string('accident_type')->nullable();
            $table->decimal('distance_km')->nullable();

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
