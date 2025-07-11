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
    Schema::create('event_services', function (Blueprint $table) {
        $table->id();
        $table->foreignId('event_id')->constrained('events');
        $table->foreignId('service_id')->constrained('services');
        $table->integer('quantity')->default(1);
        $table->decimal('price', 10, 2);
        $table->decimal('total', 10, 2);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_services');
    }
};
