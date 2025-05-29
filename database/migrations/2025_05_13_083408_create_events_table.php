<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users');
        $table->foreignId('provider_id')->nullable()->constrained('users');
        $table->string('title');
        $table->enum('type', ['user_created', 'ready_made']);
        $table->boolean('is_public')->default(false);
        $table->dateTime('date');
        $table->string('location');
        $table->string('invitation_code')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
