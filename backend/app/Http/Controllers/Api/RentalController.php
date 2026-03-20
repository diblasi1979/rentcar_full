<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use App\Services\RentalPaymentCalculator;

class RentalController extends Controller
{
    public function index()
    {
        return Rental::with(['driver', 'vehicle', 'payments'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'driver_id' => 'required|integer|exists:drivers,id',
            'vehicle_id' => 'required|integer|exists:vehicles,id',
            'type' => 'required|in:semanal,quincenal,mensual',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'contract_from' => 'required|date',
            'contract_to' => 'required|date|after_or_equal:contract_from',
            'active' => 'nullable|boolean',
        ]);
        
        // Calcular cuotas y monto por cuota
        $cuotas = RentalPaymentCalculator::calcularCuotas(
            $data['type'],
            $data['amount'],
            $data['contract_from'],
            $data['contract_to']
        );

        return Rental::create($data);
        return response()->json([
            'rental' => $rental,
            'cuotas' => $cuotas['cuotas'],
            'monto_por_cuota' => $cuotas['monto_por_cuota'],
        ]);
    }

    public function debt($id)
    {
        $rental = Rental::with(['payments', 'driver', 'vehicle'])->findOrFail($id);

        $start = \Carbon\Carbon::parse($rental->start_date);
        $now = \Carbon\Carbon::now();

        $weeks = $start->diffInWeeks($now);
        $expected = 0;

        if ($rental->type === 'semanal') {
            $expected = $weeks * $rental->amount;
        }

        if ($rental->type === 'quincenal') {
            $expected = floor($weeks / 2) * $rental->amount;
        }

        if ($rental->type === 'mensual') {
            $months = $start->diffInMonths($now);
            $expected = $months * $rental->amount;
        }

        $paid = $rental->payments->sum('amount');

        return [
            'expected' => $expected,
            'paid' => $paid,
            'debt' => $expected - $paid,
        ];
    }
}

