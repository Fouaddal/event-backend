<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_provider', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_event_id')
                  ->constrained('user_events')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_provider');
    }
};
