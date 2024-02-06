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
            $table->string('city')->after('avatar_url')->nullable();
            $table->string('state')->after('city')->nullable();
            $table->string('street')->after('state')->nullable();
            $table->string('postal_code')->after('street')->nullable();
            $table->string('unit')->after('postal_code')->nullable();
            $table->string('block')->after('unit')->nullable();
            $table->date('birthday')->nullable()->after('block');
            $table->string('job')->after('birthday')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_user_profiles', function (Blueprint $table) {
            $table->dropColumn(['city', 'state', 'street', 'postal_code', 'unit', 'block', 'birthday', 'job']);
        });
    }
};
