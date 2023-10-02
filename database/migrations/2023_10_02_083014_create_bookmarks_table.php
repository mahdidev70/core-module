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
        Schema::create('core_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('bookmarkable_id');
            $table->string('bookmarkable_type');
            $table->unique(['user_id', 'bookmarkable_id', 'bookmarkable_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_bookmarks');
    }
};
