<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_accidents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accidents', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable(); // Optional as requested
            $table->string('location');
            $table->string('accident_type');
            $table->text('description');
            $table->string('photo_path')->nullable(); // Store file path instead of binary data
            $table->enum('urgency', ['low', 'medium', 'high'])->default('low');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accidents');
    }
};