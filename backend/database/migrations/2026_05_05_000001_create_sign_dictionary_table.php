<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sign_dictionary', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->string('video_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('category')->default('umum');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sign_dictionary');
    }
};
