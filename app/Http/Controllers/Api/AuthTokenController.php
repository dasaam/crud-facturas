<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credencialesValidadas = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'nombreDispositivo' => ['nullable', 'string', 'max:120'],
        ]);

        $usuario = User::query()->where('email', $credencialesValidadas['email'])->first();

        if (!$usuario || !Hash::check($credencialesValidadas['password'], $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas.'],
            ]);
        }

        $nombreDispositivo = $credencialesValidadas['nombreDispositivo'] ?? 'dispositivo-api';
        $tokenAcceso = $usuario->createToken($nombreDispositivo)->plainTextToken;

        return response()->json([
            'token' => $tokenAcceso,
            'tokenType' => 'Bearer',
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Sesión API cerrada correctamente.',
        ]);
    }
}
