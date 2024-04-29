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
        Schema::table('core_user_profiles', function (Blueprint $table) {
            $table->integer('national_code')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_user_profiles', function (Blueprint $table) {
            $table->dropColumn('national_code');
        });
    }
};
