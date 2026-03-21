<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traffic_infractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->date('infraction_date');
            $table->string('report_number')->nullable();
            $table->string('type');
            $table->text('description');
            $table->string('location')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('status', 30)->default('ADEUDADA');
            $table->date('payment_date')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traffic_infractions');
    }
};
