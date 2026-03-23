<?php

use App\Models\ServiceRequest;
use App\Models\VehicleMaintenance;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->foreignId('vehicle_maintenance_id')
                ->nullable()
                ->after('vehicle_id')
                ->constrained('vehicle_maintenances')
                ->nullOnDelete();
        });

        DB::transaction(function () {
            ServiceRequest::query()
                ->whereNull('vehicle_maintenance_id')
                ->whereNotNull('vehicle_id')
                ->orderBy('id')
                ->each(function (ServiceRequest $serviceRequest) {
                    $maintenance = VehicleMaintenance::create([
                        'vehicle_id' => $serviceRequest->vehicle_id,
                        'maintenance_date' => optional($serviceRequest->created_at)->toDateString() ?? now()->toDateString(),
                        'type' => $serviceRequest->title,
                        'description' => $serviceRequest->description,
                        'status' => $serviceRequest->status === 'RESUELTA' ? 'REALIZADO' : 'PENDIENTE',
                    ]);

                    $serviceRequest->update([
                        'vehicle_maintenance_id' => $maintenance->id,
                    ]);
                });
        });
    }

    public function down(): void
    {
        $linkedIds = ServiceRequest::query()
            ->whereNotNull('vehicle_maintenance_id')
            ->pluck('vehicle_maintenance_id')
            ->filter()
            ->unique()
            ->values();

        ServiceRequest::query()->update([
            'vehicle_maintenance_id' => null,
        ]);

        if ($linkedIds->isNotEmpty()) {
            VehicleMaintenance::query()
                ->whereIn('id', $linkedIds)
                ->delete();
        }

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vehicle_maintenance_id');
        });
    }
};