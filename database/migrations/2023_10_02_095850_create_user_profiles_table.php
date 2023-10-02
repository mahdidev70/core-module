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
        Schema::create('core_user_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('description')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('active');
            $table->string('seller_panel_foreign_id')->nullable();
            $table->dateTime('seller_panel_last_update')->nullable();
            $table->dateTime('seller_panel_last_sync')->nullable();
            $table->string('registration_phone_number');
            $table->string('notification_phone_number')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_suspended')->default(false);
            $table->string('avatar_url')->nullable();
            $table->string('shop_name')->nullable();
            $table->string('shop_website')->nullable();
            $table->string('shop_logo_url')->nullable();
            $table->float('rating')->nullable();
            $table->string('password')->nullable();
            $table->timestamp('last_logged_at')->nullable();
            $table->timestamp('email_verified')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_user_profiles');
    }
};
