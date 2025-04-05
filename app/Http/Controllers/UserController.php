<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Psy\Util\Json;

class UserController extends Controller
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

    public function registerUser(Request $request)
    {
        try {

            $allInputs = $request->all();
            $allowedFields = ['name', 'email', 'password', 'user_type'];
            $extraFields = array_diff(array_keys($allInputs), $allowedFields);

            if (!empty($extraFields)) {
                return response()->json([
                    'message' => 'Campos não permitidos na requisição',
                    'extra_fields' => $extraFields
                ], status: 400);
            }


            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'user_type' => 'required|string|in:freelancer,contractor',
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type' => $data['user_type'],
            ]);

            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuário criado com sucesso!',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function destroyUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'message' => 'Usuário deletado com sucesso!',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Usuário não encontrado',
            ], 404);
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

    public function updateUser(Request $request, $id)
    {
        try {

            $allInputs = $request->all();
            $allowedFields = ['name', 'email', 'password', 'user_type'];
            $extraFields = array_diff(array_keys($allInputs), $allowedFields);

            if (!empty($extraFields)) {
                return response()->json([
                    'message' => 'Campos não permitidos na requisição',
                    'extra_fields' => $extraFields
                ], status: 400);
            }

            $user = User::findOrFail($id);

            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:6',
                'user_type' => 'sometimes|string|in:freelancer,contractor',
            ]);

            // Verifica se a senha foi enviada e faz o hash
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            return response()->json([
                'message' => 'Usuário atualizado com sucesso!',
                'user' => $user,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Usuário não encontrado',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
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
}
