<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::query()
            ->select(['id', 'name', 'email', 'role', 'created_at'])
            ->orderByDesc('id')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|string|in:admin,consultor,conductor',
            'password' => 'required|string|min:4',
        ]);

        $user = User::create($data);

        return response()->json([
            'status' => 'ok',
            'user' => $user,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:admin,consultor,conductor',
        ]);

        if ((int) $request->user()->id === (int) $user->id && $data['role'] !== 'admin') {
            return response()->json([
                'message' => 'No podes quitarte el rol admin a tu propio usuario.',
            ], 422);
        }

        $user->update($data);

        return response()->json([
            'status' => 'ok',
            'user' => $user->fresh(['tokens']),
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
}