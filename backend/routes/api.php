<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\RentalController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\InsuranceCoverageController;
use App\Http\Controllers\Api\TrafficInfractionController;
use App\Http\Controllers\Api\VehicleMaintenanceController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Illuminate\Http\Request $request) {
        return $request->user();
    });
});

Route::middleware(['auth:sanctum', 'role:admin,consultor'])->group(function () {
    Route::get('/drivers', [DriverController::class, 'index']);
    Route::get('/vehicles', [VehicleController::class, 'index']);

    Route::get('/insurance-coverages', [InsuranceCoverageController::class, 'index']);

    Route::get('/traffic-infractions', [TrafficInfractionController::class, 'index']);

    Route::get('/vehicle-maintenances', [VehicleMaintenanceController::class, 'index']);

    Route::get('/rentals', [RentalController::class, 'index']);
    Route::get('/rentals/{id}/debt', [RentalController::class, 'debt']);

    Route::get('/payments', [PaymentController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'role:admin,consultor'])->group(function () {
    Route::post('/drivers', [DriverController::class, 'store']);
    Route::put('/drivers/{id}', [DriverController::class, 'update']);
    Route::patch('/drivers/{id}/toggle', [DriverController::class, 'toggle']);

    Route::post('/vehicles', [VehicleController::class, 'store']);

    Route::post('/insurance-coverages', [InsuranceCoverageController::class, 'store']);
    Route::put('/insurance-coverages/{id}', [InsuranceCoverageController::class, 'update']);

    Route::post('/traffic-infractions', [TrafficInfractionController::class, 'store']);
    Route::put('/traffic-infractions/{id}', [TrafficInfractionController::class, 'update']);

    Route::post('/vehicle-maintenances', [VehicleMaintenanceController::class, 'store']);
    Route::put('/vehicle-maintenances/{id}', [VehicleMaintenanceController::class, 'update']);

    Route::post('/rentals', [RentalController::class, 'store']);
    Route::put('/rentals/{id}', [RentalController::class, 'update']);

    Route::post('/payments', [PaymentController::class, 'store']);
    Route::put('/payments/{id}', [PaymentController::class, 'update']);
    Route::post('/payments/send-receipt', [PaymentController::class, 'sendReceipt']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::delete('/insurance-coverages/{id}', [InsuranceCoverageController::class, 'destroy']);
    Route::delete('/traffic-infractions/{id}', [TrafficInfractionController::class, 'destroy']);
    Route::delete('/vehicle-maintenances/{id}', [VehicleMaintenanceController::class, 'destroy']);
    Route::delete('/rentals/{id}', [RentalController::class, 'destroy']);
    Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);
});