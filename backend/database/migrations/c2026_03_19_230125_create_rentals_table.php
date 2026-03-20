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
    Schema::create('rentals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
        $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
        $table->enum('type', ['semanal', 'quincenal', 'mensual']);
        $table->decimal('amount', 10, 2);
        $table->date('start_date');
        $table->date('contract_from');
        $table->date('contract_to');
        $table->boolean('active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
