<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function login(Request $request): JsonResponse
    {
        try {

            // Verifica se há campos extras não permitidos
            $allInputs = $request->all();
            $allowedFields = ['email', 'password'];
            $extraFields = array_diff(array_keys($allInputs), $allowedFields);

            if (!empty($extraFields)) {
                return response()->json([
                    'message' => 'Campos não permitidos na requisição',
                    'extra_fields' => $extraFields
                ], status: 400);
            }

            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Credenciais inválidas!'], 401);
            }

            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Erro ao recuperar dados do usuário'], 500);
            }

            //dd($user);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login feito com sucesso!',
                'token' => $token
            ], status: 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], status: 422);
        } catch (\Exception $e) {
            if (app()->environment('production')) {
                return response()->json([
                    'message' => 'Erro interno do servidor'
                ], 500);
            }

            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete(); // Apaga todos os tokens do usuário autenticado
        return response()->json(['message' => 'Deslogado com sucesso!']);
    }
}
