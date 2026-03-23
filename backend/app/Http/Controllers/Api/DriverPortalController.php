<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCoverage;
use App\Models\Rental;
use App\Models\ServiceRequest;
use App\Models\TrafficInfraction;
use App\Models\VehicleMaintenance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DriverPortalController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        $user = $request->user()->load('driver.assignedVehicle');
        $driver = $user->driver;

        if (!$driver) {
            return response()->json([
                'message' => 'Tu usuario no tiene un conductor vinculado.',
            ], 422);
        }

        $rentals = Rental::with(['vehicle', 'payments'])
            ->where('driver_id', $driver->id)
            ->orderByDesc('active')
            ->orderByDesc('start_date')
            ->get();

        $activeRental = $rentals->firstWhere('active', true) ?? $rentals->first();
        $assignedVehicle = $driver->assignedVehicle;

        $infractions = TrafficInfraction::with('vehicle')
            ->where('driver_id', $driver->id)
            ->orderByDesc('infraction_date')
            ->orderByDesc('id')
            ->get();

        $insurance = null;

        if ($activeRental?->vehicle_id) {
            $insurance = InsuranceCoverage::with('vehicle')
                ->where('vehicle_id', $activeRental->vehicle_id)
                ->orderByDesc('valid_to')
                ->orderByDesc('id')
                ->first();
        } elseif ($assignedVehicle?->id) {
            $insurance = InsuranceCoverage::with('vehicle')
                ->where('vehicle_id', $assignedVehicle->id)
                ->orderByDesc('valid_to')
                ->orderByDesc('id')
                ->first();
        }

        $serviceRequests = ServiceRequest::with(['vehicle', 'rental', 'vehicleMaintenance'])
            ->where('driver_id', $driver->id)
            ->orderByDesc('created_at')
            ->get();

        $rentalDebts = $rentals
            ->map(function (Rental $rental) {
                $expected = $this->calculateExpectedAmount($rental);
                $paid = (float) $rental->payments->sum('amount');
                $debt = max($expected - $paid, 0);

                return [
                    'id' => $rental->id,
                    'vehicle' => $rental->vehicle?->plate,
                    'type' => $rental->type,
                    'expected' => round($expected, 2),
                    'paid' => round($paid, 2),
                    'debt' => round($debt, 2),
                    'start_date' => $rental->start_date,
                    'active' => (bool) $rental->active,
                ];
            })
            ->filter(fn (array $item) => $item['debt'] > 0)
            ->values();

        $infractionDebts = $infractions
            ->filter(fn (TrafficInfraction $infraction) => $infraction->status === 'ADEUDADA')
            ->map(fn (TrafficInfraction $infraction) => [
                'id' => $infraction->id,
                'date' => $infraction->infraction_date,
                'vehicle' => $infraction->vehicle?->plate,
                'type' => $infraction->type,
                'location' => $infraction->location,
                'status' => $infraction->status,
                'amount' => round((float) $infraction->amount, 2),
            ])
            ->values();

        return response()->json([
            'driver' => [
                'id' => $driver->id,
                'name' => $driver->name,
                'email' => $driver->email,
                'phone' => $driver->phone,
                'license_number' => $driver->license_number,
                'license_expiration' => $driver->license_expiration,
                'assigned_vehicle' => $assignedVehicle,
            ],
            'active_rental' => $activeRental,
            'insurance' => $insurance,
            'balance' => [
                'rental_items' => $rentalDebts,
                'infraction_items' => $infractionDebts,
                'rental_total' => round($rentalDebts->sum('debt'), 2),
                'infraction_total' => round($infractionDebts->sum('amount'), 2),
                'total' => round($rentalDebts->sum('debt') + $infractionDebts->sum('amount'), 2),
            ],
            'infractions' => $infractions,
            'service_requests' => $serviceRequests,
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        $user = $request->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'message' => 'La contrasena actual es incorrecta.',
                'errors' => [
                    'current_password' => ['La contrasena actual es incorrecta.'],
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'message' => 'Contrasena actualizada correctamente.',
        ]);
    }

    public function storeServiceRequest(Request $request): JsonResponse
    {
        $user = $request->user()->load('driver.assignedVehicle');
        $driver = $user->driver;

        if (!$driver) {
            return response()->json([
                'message' => 'Tu usuario no tiene un conductor vinculado.',
            ], 422);
        }

        $activeRental = Rental::query()
            ->where('driver_id', $driver->id)
            ->where('active', true)
            ->latest('start_date')
            ->first();

        $vehicleId = $activeRental?->vehicle_id ?? $driver->assigned_vehicle_id;

        if (!$activeRental && !$vehicleId) {
            return response()->json([
                'message' => 'No tenes un alquiler activo ni un vehiculo asignado para generar una solicitud de servicio.',
            ], 422);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        $serviceRequest = DB::transaction(function () use ($driver, $activeRental, $vehicleId, $data) {
            $maintenance = VehicleMaintenance::create([
                'vehicle_id' => $vehicleId,
                'maintenance_date' => now()->toDateString(),
                'type' => $data['title'],
                'description' => $data['description'],
                'status' => 'PENDIENTE',
            ]);

            return ServiceRequest::create([
                'driver_id' => $driver->id,
                'rental_id' => $activeRental?->id,
                'vehicle_id' => $vehicleId,
                'vehicle_maintenance_id' => $maintenance->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => 'PENDIENTE',
            ]);
        });

        return response()->json($serviceRequest->load(['vehicle', 'rental', 'vehicleMaintenance']), 201);
    }

    private function calculateExpectedAmount(Rental $rental): float
    {
        if (!$rental->start_date || !$rental->type) {
            return 0;
        }

        $start = Carbon::parse($rental->start_date);
        $now = now();

        if ($start->isFuture()) {
            return 0;
        }

        $days = $start->diffInDays($now);
        $periods = 0;

        if ($rental->type === 'semanal') {
            $periods = intdiv($days, 7);
        } elseif ($rental->type === 'quincenal') {
            $periods = intdiv($days, 15);
        } elseif ($rental->type === 'mensual') {
            $periods = $start->diffInMonths($now);

            if ($now->day < $start->day) {
                $periods = max($periods - 1, 0);
            }
        }

        return $periods * (float) $rental->amount;
    }
}