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
       Schema::create('event_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_event_id')->constrained('user_events')->onDelete('cascade');
            $table->string('name'); // Name of the person responding
            $table->enum('response', ['coming', 'not_coming'])->default('coming');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_responses');
    }
};
