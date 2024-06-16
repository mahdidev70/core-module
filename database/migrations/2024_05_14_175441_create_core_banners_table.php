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
        Schema::create('core_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('link_url')->nullable();
            $table->string('image_url');
            $table->enum('type', ['evant', 'banner'])->nullable();
            $table->enum('status', ['published', 'draft', 'deleted'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_banners', function (Blueprint $table) {
            $table->dropColumn('core_banners');
        });
    }
};
