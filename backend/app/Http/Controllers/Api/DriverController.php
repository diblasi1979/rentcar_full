<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        return Driver::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:255',
            'license_expiration' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'enabled' => 'boolean',
            'documents' => 'nullable|array|max:4',
            'documents.*' => 'file|mimes:jpg,jpeg,png,gif,pdf|max:20480',
        ]);

        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('drivers/documents', 'public');
                $documents[] = $path;
            }
        }
        $data['documents'] = $documents;

        $driver = Driver::create($data);
        // Retornar URLs completas para frontend
        $driver->documents = collect($documents)->map(fn($path) => asset('storage/'.$path));
        return response()->json($driver, 201);
    }

    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:255',
            'license_expiration' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'enabled' => 'boolean',
            'documents' => 'nullable|array|max:4',
            'documents.*' => 'file|mimes:jpg,jpeg,png,gif,pdf|max:20480',
        ]);

        $documents = $driver->documents ?? [];
        // Si se suben nuevos documentos, los agregamos a los existentes (máx 4)
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('drivers/documents', 'public');
                $documents[] = $path;
            }
            // Limitar a 4 documentos
            $documents = array_slice($documents, 0, 4);
        }
        $data['documents'] = $documents;
        $driver->update($data);
        // Retornar URLs completas para frontend
        $driver->documents = collect($documents)->map(fn($path) => asset('storage/'.$path));
        return $driver;
    }

    public function toggle($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->enabled = !$driver->enabled;
        $driver->save();
        return $driver;
    }
}
