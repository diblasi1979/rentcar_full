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

    Route::get('/drivers', [DriverController::class, 'index']);
    Route::post('/drivers', [DriverController::class, 'store']);
    Route::put('/drivers/{id}', [DriverController::class, 'update']);
    Route::patch('/drivers/{id}/toggle', [DriverController::class, 'toggle']);

    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::post('/vehicles', [VehicleController::class, 'store']);

    Route::get('/insurance-coverages', [InsuranceCoverageController::class, 'index']);
    Route::post('/insurance-coverages', [InsuranceCoverageController::class, 'store']);
    Route::put('/insurance-coverages/{id}', [InsuranceCoverageController::class, 'update']);
    Route::delete('/insurance-coverages/{id}', [InsuranceCoverageController::class, 'destroy']);

    Route::get('/traffic-infractions', [TrafficInfractionController::class, 'index']);
    Route::post('/traffic-infractions', [TrafficInfractionController::class, 'store']);
    Route::put('/traffic-infractions/{id}', [TrafficInfractionController::class, 'update']);
    Route::delete('/traffic-infractions/{id}', [TrafficInfractionController::class, 'destroy']);

    Route::get('/vehicle-maintenances', [VehicleMaintenanceController::class, 'index']);
    Route::post('/vehicle-maintenances', [VehicleMaintenanceController::class, 'store']);
    Route::put('/vehicle-maintenances/{id}', [VehicleMaintenanceController::class, 'update']);
    Route::delete('/vehicle-maintenances/{id}', [VehicleMaintenanceController::class, 'destroy']);

    Route::get('/rentals', [RentalController::class, 'index']);
    Route::post('/rentals', [RentalController::class, 'store']);
    Route::put('/rentals/{id}', [RentalController::class, 'update']);
    Route::delete('/rentals/{id}', [RentalController::class, 'destroy']);
    Route::get('/rentals/{id}/debt', [RentalController::class, 'debt']);

    // Payments
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::put('/payments/{id}', [PaymentController::class, 'update']);
    Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);
    Route::post('/payments/send-receipt', [PaymentController::class, 'sendReceipt']);
});