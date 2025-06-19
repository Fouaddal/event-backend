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
        Schema::table('provider_requests', function (Blueprint $table) {
            // Add services if it doesn't exist (nullable)
            if (!Schema::hasColumn('provider_requests', 'services')) {
                $table->json('services')->nullable()->after('password');
            } else {
                $table->json('services')->nullable()->change();
            }

            // Add specializations if it doesn't exist (nullable)
            if (!Schema::hasColumn('provider_requests', 'specializations')) {
                $table->json('specializations')->nullable()->after('services');
            } else {
                $table->json('specializations')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_requests', function (Blueprint $table) {
            // Only drop columns if they exist
            if (Schema::hasColumn('provider_requests', 'specializations')) {
                $table->dropColumn('specializations');
            }
            
            // Optional: Only drop services if you want to fully revert
            // if (Schema::hasColumn('provider_requests', 'services')) {
            //     $table->dropColumn('services');
            // }
        });
    }
};