<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); // Event creator
            $table->string('title');
            $table->enum('type', [
                'Creative & Cultural',
                'Social Celebrations',
                'Music & Performance',
                'Wellness & Lifestyle',
                'Entertainment & Fun',
                'Media & Content',
                'Educational & Academic',
                'Training & Development'
            ]);
            $table->boolean('is_public')->default(false);
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('location');
            $table->string('invitation_code')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Workflow status
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_events');
    }
};
