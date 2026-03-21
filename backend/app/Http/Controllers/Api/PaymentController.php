<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Mail\PaymentReceipt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function sendReceipt(Request $request)
    {
        $data = $request->validate([
            'payment_id' => 'required|integer|exists:payments,id',
            'email' => 'required|email',
        ]);

        $payment = Payment::with('rental.driver', 'rental.vehicle')->findOrFail($data['payment_id']);
        $rental = $payment->rental;
        $driver = $rental->driver;
        $vehicle = $rental->vehicle;

        Mail::to($data['email'])->send(new PaymentReceipt($payment, $rental, $driver, $vehicle));

        return response()->json(['status' => 'ok']);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'rental_id' => 'required|integer|exists:rentals,id',
            'amount' => 'required|numeric',
            'payment_date' => 'nullable|date',
            'km_reported' => 'nullable|integer',
            'notes' => 'nullable|string',
            'payment_receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        if (empty($data['payment_date'])) {
            $data['payment_date'] = now()->toDateString();
        }

        if ($request->hasFile('payment_receipt')) {
            $data['payment_receipt'] = $request->file('payment_receipt')->store('payments/receipts', 'public');
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
            'payment' => $payment->load('rental.driver', 'rental.vehicle'),
            'km_traveled' => $km
        ];
    }

    public function index()
    {
        return Payment::with('rental.driver', 'rental.vehicle')->get();
    }

    // Actualizar pago
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $data = $request->validate([
            'amount' => 'required|numeric',
            'km_reported' => 'nullable|integer',
            'payment_receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        if ($request->hasFile('payment_receipt')) {
            if ($payment->payment_receipt) {
                Storage::disk('public')->delete($payment->payment_receipt);
            }

            $data['payment_receipt'] = $request->file('payment_receipt')->store('payments/receipts', 'public');
        }

        $payment->update($data);
        return response()->json(['status' => 'ok', 'payment' => $payment->fresh('rental.driver', 'rental.vehicle')]);
    }

    // Anular (eliminar) pago
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->payment_receipt) {
            Storage::disk('public')->delete($payment->payment_receipt);
        }

        $payment->delete();
        return response()->json(['status' => 'ok']);
    }
}
