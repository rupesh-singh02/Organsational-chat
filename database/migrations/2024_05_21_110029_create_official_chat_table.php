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
        Schema::create('official_chat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_staff_id');
            $table->unsignedBigInteger('to_staff_id');
            $table->unsignedBigInteger('reply_id')->nullable();
            $table->string('message_type');
            $table->tinyInteger('view_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_chat');
    }
};
