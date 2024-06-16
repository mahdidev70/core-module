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
        Schema::table('core_banners', function (Blueprint $table) {
            $table->dateTime('date')->after('image_url')->nullable();
            $table->integer('price')->nullable()->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_banners', function (Blueprint $table) {
            $table->dropColumn(['date', 'price']);
        });
    }
};
