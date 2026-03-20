<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

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
            'active' => 'nullable|boolean',
        ]);

        return Rental::create($data);
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

