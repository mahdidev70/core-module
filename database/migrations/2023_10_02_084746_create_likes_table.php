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
        Schema::create('core_likes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('likeable_type');
            $table->integer('likeable_id');
            $table->enum('action',['like','dislike']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_likes');
    }
};
