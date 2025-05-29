<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsApprovedAndOtpVerifiedToProviderRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('provider_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('provider_requests', 'is_approved')) {
                $table->boolean('is_approved')->default(false);
            }
            if (!Schema::hasColumn('provider_requests', 'otp_verified')) {
                $table->boolean('otp_verified')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('provider_requests', function (Blueprint $table) {
            $table->dropColumn(['is_approved', 'otp_verified']);
        });
    }
}
