<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCoverage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InsuranceCoverageController extends Controller
{
    public function index()
    {
        return InsuranceCoverage::with('vehicle')->orderByDesc('id')->get();
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $data = $this->storeFiles($request, $data);

        $coverage = InsuranceCoverage::create($data);

        return response()->json($coverage->load('vehicle'), 201);
    }

    public function update(Request $request, $id)
    {
        $coverage = InsuranceCoverage::findOrFail($id);
        $data = $this->validateRequest($request, $id);
        $data = $this->storeFiles($request, $data, $coverage);

        $coverage->update($data);

        return response()->json($coverage->fresh('vehicle'));
    }

    public function destroy($id)
    {
        $coverage = InsuranceCoverage::findOrFail($id);

        if ($coverage->policy_pdf) {
            Storage::disk('public')->delete($coverage->policy_pdf);
        }

        if ($coverage->credential_image) {
            Storage::disk('public')->delete($coverage->credential_image);
        }

        $coverage->delete();

        return response()->json(['status' => 'ok']);
    }

    private function validateRequest(Request $request, $id = null): array
    {
        return $request->validate([
            'vehicle_id' => 'required|integer|exists:vehicles,id',
            'policy_number' => 'required|string|max:255',
            'endorsement_number' => 'nullable|string|max:255',
            'insured_last_name' => 'required|string|max:255',
            'insured_first_name' => 'required|string|max:255',
            'insured_document_type' => 'required|string|max:50',
            'insured_document_number' => 'required|string|max:50',
            'insurance_company' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'electronic_payment_code' => 'nullable|string|max:255',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
            'policy_pdf' => $id ? 'nullable|file|mimes:pdf|max:20480' : 'required|file|mimes:pdf|max:20480',
            'credential_image' => $id ? 'nullable|file|image|max:20480' : 'required|file|image|max:20480',
        ]);
    }

    private function storeFiles(Request $request, array $data, ?InsuranceCoverage $coverage = null): array
    {
        if ($request->hasFile('policy_pdf')) {
            if ($coverage && $coverage->policy_pdf) {
                Storage::disk('public')->delete($coverage->policy_pdf);
            }

            $data['policy_pdf'] = $request->file('policy_pdf')->store('insurance/policies', 'public');
        }

        if ($request->hasFile('credential_image')) {
            if ($coverage && $coverage->credential_image) {
                Storage::disk('public')->delete($coverage->credential_image);
            }

            $data['credential_image'] = $request->file('credential_image')->store('insurance/credentials', 'public');
        }

        return $data;
    }
}
