<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrafficInfraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrafficInfractionController extends Controller
{
    public function index()
    {
        return TrafficInfraction::with(['vehicle', 'driver'])->orderByDesc('infraction_date')->orderByDesc('id')->get();
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $data = $this->storeAttachment($request, $data);

        $infraction = TrafficInfraction::create($data);

        return response()->json($infraction->load(['vehicle', 'driver']), 201);
    }

    public function update(Request $request, $id)
    {
        $infraction = TrafficInfraction::findOrFail($id);
        $data = $this->validateRequest($request, true);
        $data = $this->storeAttachment($request, $data, $infraction);

        $infraction->update($data);

        return response()->json($infraction->fresh(['vehicle', 'driver']));
    }

    public function destroy($id)
    {
        $infraction = TrafficInfraction::findOrFail($id);

        if ($infraction->attachment) {
            Storage::disk('public')->delete($infraction->attachment);
        }

        if ($infraction->payment_receipt) {
            Storage::disk('public')->delete($infraction->payment_receipt);
        }

        $infraction->delete();

        return response()->json(['status' => 'ok']);
    }

    private function validateRequest(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'vehicle_id' => 'required|integer|exists:vehicles,id',
            'driver_id' => 'nullable|integer|exists:drivers,id',
            'infraction_date' => 'required|date',
            'report_number' => 'nullable|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:ADEUDADA,PAGADA',
            'payment_date' => 'nullable|date',
            'attachment' => $isUpdate ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'payment_receipt' => $isUpdate ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);
    }

    private function storeAttachment(Request $request, array $data, ?TrafficInfraction $infraction = null): array
    {
        if ($request->hasFile('attachment')) {
            if ($infraction && $infraction->attachment) {
                Storage::disk('public')->delete($infraction->attachment);
            }

            $data['attachment'] = $request->file('attachment')->store('infractions/attachments', 'public');
        }

        if ($request->hasFile('payment_receipt')) {
            if ($infraction && $infraction->payment_receipt) {
                Storage::disk('public')->delete($infraction->payment_receipt);
            }

            $data['payment_receipt'] = $request->file('payment_receipt')->store('infractions/payment-receipts', 'public');
        }

        return $data;
    }
}
