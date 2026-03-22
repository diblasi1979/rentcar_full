<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $drivers = DB::table('drivers')
            ->whereNull('assigned_vehicle_id')
            ->select('id')
            ->get();

        foreach ($drivers as $driver) {
            $rental = DB::table('rentals')
                ->where('driver_id', $driver->id)
                ->orderByDesc('active')
                ->orderByDesc('start_date')
                ->select(['vehicle_id'])
                ->first();

            if (!$rental?->vehicle_id) {
                continue;
            }

            DB::table('drivers')
                ->where('id', $driver->id)
                ->update(['assigned_vehicle_id' => $rental->vehicle_id]);
        }
    }

    public function down(): void
    {
    }
};