<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use App\Services\RentalPaymentCalculator;
use Illuminate\Support\Facades\Storage;

class RentalController extends Controller
{
    // Actualizar alquiler
    public function update(Request $request, $id)
    {
        $rental = Rental::findOrFail($id);
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'active' => 'nullable|boolean',
            'contract_pdf' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        if ($request->hasFile('contract_pdf')) {
            if ($rental->contract_pdf) {
                Storage::disk('public')->delete($rental->contract_pdf);
            }

            $data['contract_pdf'] = $request->file('contract_pdf')->store('rentals/contracts', 'public');
        }

        $rental->update($data);

        return response()->json([
            'status' => 'ok',
            'rental' => $rental->fresh(['driver', 'vehicle', 'payments']),
        ]);
    }

    // Eliminar alquiler
    public function destroy($id)
    {
        $rental = Rental::findOrFail($id);

        if ($rental->contract_pdf) {
            Storage::disk('public')->delete($rental->contract_pdf);
        }

        $rental->delete();
        return response()->json(['status' => 'ok']);
    }
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
            'contract_pdf' => 'nullable|file|mimes:pdf|max:20480',
        ]);
        
        RentalPaymentCalculator::calcularCuotas(
            $data['type'],
            $data['amount'],
            $data['contract_from'],
            $data['contract_to']
        );

        if ($request->hasFile('contract_pdf')) {
            $data['contract_pdf'] = $request->file('contract_pdf')->store('rentals/contracts', 'public');
        }

        $rental = Rental::create($data);

        return response()->json($rental->load(['driver', 'vehicle', 'payments']), 201);
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

