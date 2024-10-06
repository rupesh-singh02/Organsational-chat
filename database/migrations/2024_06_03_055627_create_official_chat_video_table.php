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
        Schema::create('official_chat_video', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('official_chat_id');
            $table->json('content');
            $table->json('type');
            $table->json('size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_chat_video');
    }
};
