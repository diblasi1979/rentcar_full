<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::query()
            ->with('driver:id,name,email')
            ->select(['id', 'name', 'email', 'role', 'driver_id', 'created_at'])
            ->orderByDesc('id')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|string|in:admin,consultor,conductor',
            'driver_id' => 'nullable|integer|exists:drivers,id',
            'password' => 'required|string|min:4',
        ]);

        $this->validateDriverAssignment($data['role'], $data['driver_id'] ?? null);

        if ($data['role'] !== 'conductor') {
            $data['driver_id'] = null;
        }

        $user = User::create($data);

        return response()->json([
            'status' => 'ok',
            'user' => $user->load('driver:id,name,email'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:admin,consultor,conductor',
            'driver_id' => 'nullable|integer|exists:drivers,id',
        ]);

        $this->validateDriverAssignment($data['role'], $data['driver_id'] ?? null, $user->id);

        if ((int) $request->user()->id === (int) $user->id && $data['role'] !== 'admin') {
            return response()->json([
                'message' => 'No podes quitarte el rol admin a tu propio usuario.',
            ], 422);
        }

        if ($data['role'] !== 'conductor') {
            $data['driver_id'] = null;
        }

        $user->update($data);

        return response()->json([
            'status' => 'ok',
            'user' => $user->fresh('driver:id,name,email'),
        ]);
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'password' => 'required|string|min:4',
        ]);

        $user->update([
            'password' => $data['password'],
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Contrasena actualizada correctamente.',
        ]);
    }

    private function validateDriverAssignment(string $role, ?int $driverId, ?int $ignoreUserId = null): void
    {
        if ($role !== 'conductor') {
            return;
        }

        if (!$driverId) {
            abort(response()->json([
                'message' => 'Debes vincular un conductor para los usuarios con rol conductor.',
                'errors' => [
                    'driver_id' => ['Debes vincular un conductor para los usuarios con rol conductor.'],
                ],
            ], 422));
        }

        $assignedDriver = User::query()
            ->where('driver_id', $driverId)
            ->when($ignoreUserId, fn ($query) => $query->where('id', '!=', $ignoreUserId))
            ->first();

        if (!$assignedDriver) {
            return;
        }

        $driver = Driver::find($driverId);

        abort(response()->json([
            'message' => 'Ese conductor ya esta vinculado a otro usuario.',
            'errors' => [
                'driver_id' => ['El conductor ' . ($driver?->name ?? '#' . $driverId) . ' ya esta vinculado a otro usuario.'],
            ],
        ], 422));
    }
}