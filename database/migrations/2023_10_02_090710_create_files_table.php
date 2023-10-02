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
        Schema::create('core_files', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('fileable_type')->nullable();
            $table->integer('fileable_id')->nullable();
            $table->string('file_url');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_files');
    }
};
