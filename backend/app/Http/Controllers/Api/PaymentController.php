<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function store(Request $request)
    {

        $data = $request->validate([
            'rental_id' => 'required|integer|exists:rentals,id',
            'amount' => 'required|numeric',
            'payment_date' => 'nullable|date',
            'km_reported' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        // Si no viene payment_date, usar la fecha actual
        if (empty($data['payment_date'])) {
            $data['payment_date'] = now();
        }

        $payment = Payment::create($data);

        $previous = Payment::where('rental_id', $payment->rental_id)
            ->where('id', '<', $payment->id)
            ->orderBy('id', 'desc')
            ->first();

        $km = null;

        if ($previous && $payment->km_reported) {
            $km = $payment->km_reported - $previous->km_reported;
        }

        return [
            'payment' => $payment,
            'km_traveled' => $km
        ];
    }

public function index()
{
    return Payment::with('rental.driver', 'rental.vehicle')->get();
}
}
