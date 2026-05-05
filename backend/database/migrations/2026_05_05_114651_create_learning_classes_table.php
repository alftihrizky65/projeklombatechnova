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
        Schema::create('learning_classes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category'); // e.g., Salam, Alfabet
            $table->string('difficulty')->default('beginner');
            $table->string('thumbnail_url')->nullable();
            $table->integer('required_practical_count')->default(1); // Jumlah praktik AI yang harus diselesaikan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_classes');
    }
};
