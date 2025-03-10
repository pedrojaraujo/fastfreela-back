<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciais inválidas!'], 401);
        }

        $user = Auth::user();

        //dd($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'=> 'Login feito com sucesso!',
            'token' => $token
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete(); // Apaga todos os tokens do usuário autenticado
        return response()->json(['message' => 'Deslogado com sucesso!']);
    }
}
