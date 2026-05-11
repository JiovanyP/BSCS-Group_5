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
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('business_name');
            $table->text('description');
            $table->string('contact_number');
            $table->string('address');
            $table->text('services_offered');
            $table->string('logo_url')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable()->index('services_user_id_foreign');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
