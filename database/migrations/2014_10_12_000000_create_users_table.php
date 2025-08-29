<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('email_confirmation')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('type', ['user', 'provider', 'admin'])->default('user');
            $table->enum('provider_type', ['individual', 'company'])->nullable();
            $table->boolean('is_approved')->default(false);
            $table->string('otp')->nullable();
            $table->json('services')->nullable();
            $table->json('specializations')->nullable();
            $table->text('description')->nullable();
            $table->string('profile_image')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};