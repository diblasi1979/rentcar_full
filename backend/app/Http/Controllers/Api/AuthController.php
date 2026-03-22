<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:4',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $user = $this->resolveDriverRelation($user);

        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user->load('driver.assignedVehicle'),
            'token' => $token
        ];
    }

    public function me(Request $request)
    {
        $user = $this->resolveDriverRelation($request->user());

        return $user->load('driver.assignedVehicle');
    }

    private function resolveDriverRelation(User $user): User
    {
        if ($user->role !== 'conductor' || $user->driver_id || !$user->email) {
            return $user;
        }

        $driver = Driver::query()
            ->where('email', $user->email)
            ->first();

        if (!$driver) {
            return $user;
        }

        $alreadyAssigned = User::query()
            ->where('driver_id', $driver->id)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($alreadyAssigned) {
            return $user;
        }

        $user->update([
            'driver_id' => $driver->id,
        ]);

        return $user->fresh();
    }
}
