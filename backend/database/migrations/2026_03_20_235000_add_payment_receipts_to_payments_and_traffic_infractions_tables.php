<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_receipt')->nullable()->after('km_reported');
        });

        Schema::table('traffic_infractions', function (Blueprint $table) {
            $table->string('payment_receipt')->nullable()->after('payment_date');
        });
    }

    public function down(): void
    {
        Schema::table('traffic_infractions', function (Blueprint $table) {
            $table->dropColumn('payment_receipt');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_receipt');
        });
    }
};