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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->double('balance')->default(0);
            $table->enum('gender', ["male", "female"]);
            $table->date('birth_date');
            $table->string('avatar', 110)->nullable();
            $table->string('phone_code', 7);
            $table->string('phone', 20);
            $table->string('password');
            $table->string('email')->nullable();
            $table->boolean('email_offers')->default(false);
            $table->enum('role', ["customer", "salon_owner", "admin", "staff"]);
            $table->boolean('is_active')->default(true);
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->string('otp')->nullable();
            $table->dateTime('otp_expire_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('language', 10)->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->enum('added_by', ['admin', 'salon', 'register'])->default('register');
            $table->timestamp('register_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
