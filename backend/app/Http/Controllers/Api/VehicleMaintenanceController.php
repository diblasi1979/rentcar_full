<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleMaintenanceController extends Controller
{
    public function index()
    {
        return VehicleMaintenance::with('vehicle')->orderByDesc('maintenance_date')->orderByDesc('id')->get();
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $data = $this->storeReceipt($request, $data);

        $maintenance = VehicleMaintenance::create($data);

        return response()->json($maintenance->load('vehicle'), 201);
    }

    public function update(Request $request, $id)
    {
        $maintenance = VehicleMaintenance::findOrFail($id);
        $data = $this->validateRequest($request, true);
        $data = $this->storeReceipt($request, $data, $maintenance);

        $maintenance->update($data);

        return response()->json($maintenance->fresh('vehicle'));
    }

    public function destroy($id)
    {
        $maintenance = VehicleMaintenance::findOrFail($id);

        if ($maintenance->receipt) {
            Storage::disk('public')->delete($maintenance->receipt);
        }

        $maintenance->delete();

        return response()->json(['status' => 'ok']);
    }

    private function validateRequest(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'vehicle_id' => 'required|integer|exists:vehicles,id',
            'maintenance_date' => 'required|date',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'mileage' => 'nullable|integer|min:0',
            'next_service_mileage' => 'nullable|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:PENDIENTE,REALIZADO',
            'receipt' => $isUpdate ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);
    }

    private function storeReceipt(Request $request, array $data, ?VehicleMaintenance $maintenance = null): array
    {
        if ($request->hasFile('receipt')) {
            if ($maintenance && $maintenance->receipt) {
                Storage::disk('public')->delete($maintenance->receipt);
            }

            $data['receipt'] = $request->file('receipt')->store('maintenances/receipts', 'public');
        }

        return $data;
    }
}