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
        Schema::table('core_statics', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('text')->nullable()->change();
            $table->string('file_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_statics', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->string('text')->nullable(false)->change();
            $table->string('file_url')->nullable(false)->change();
        });
    }
};
