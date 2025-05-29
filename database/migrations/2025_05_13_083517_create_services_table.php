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
    Schema::create('services', function (Blueprint $table) {
        $table->id();
        $table->foreignId('provider_id')->constrained('users');
        $table->enum('type', ['hall', 'food', 'dj', 'photographer', 'car', 'singer', 'performer']);
        $table->string('name');
        $table->text('description');
        $table->decimal('price', 10, 2);
        $table->boolean('is_approved')->default(false);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
