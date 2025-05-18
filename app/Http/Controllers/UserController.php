<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="FastFreela API",
 *     description="Documentação da API do FastFreela",
 *     @OA\Contact(
 *         email="pedroa990@gmail.com"
 *     )
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Usuário"},
     *     summary="Login do usuário",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="pedro@email.com"),
     *             @OA\Property(property="password", type="string", format="password", example="12345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login bem-sucedido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login feito com sucesso!"),
     *             @OA\Property(property="token", type="string", example="1|abcde12345..."),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Pedro Joaquim"),
     *                 @OA\Property(property="email", type="string", example="pedro@email.com"),
     *                 @OA\Property(property="user_type", type="string", enum={"freelancer", "contractor"}, example="freelancer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Credenciais inválidas!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro de validação"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties=@OA\Property(type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro interno do servidor")
     *         )
     *     )
     * )
     */
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
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                ]
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

    /**
     * @OA\Post(
     *   path="/api/logout",
     *   tags={"Usuário"},
     *   summary="Efetua logout",
     *   @OA\Response(response=200, description="Logout bem-sucedido")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete(); // Apaga todos os tokens do usuário autenticado
        return response()->json(['message' => 'Deslogado com sucesso!']);
    }

    /**
     * @OA\Post(
     *   path="/api/registerUser",
     *   tags={"Usuário"},
     *   summary="Registra novo usuário",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name","email","password","user_type"},
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="email", type="string", format="email"),
     *       @OA\Property(property="password", type="string", format="password"),
     *       @OA\Property(property="user_type", type="string", enum={"freelancer","contractor"})
     *     )
     *   ),
     *   @OA\Response(response=201, description="Usuário criado com sucesso"),
     *   @OA\Response(response=422, description="Erro de validação")
     * )
     */


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

    /**
     * @OA\Delete(
     *   path="/api/deleteUser/{id}",
     *   tags={"Usuário"},
     *   summary="Deleta usuário",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(response=200, description="Usuário deletado"),
     *   @OA\Response(response=404, description="Usuário não encontrado")
     * )
     */
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

    /**
     * @OA\Put(
     *   path="/api/updateUser/{id}",
     *   tags={"Usuário"},
     *   summary="Atualiza dados do usuário",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="email", type="string", format="email"),
     *       @OA\Property(property="password", type="string", format="password"),
     *       @OA\Property(property="user_type", type="string", enum={"freelancer","contractor"})
     *     )
     *   ),
     *   @OA\Response(response=200, description="Usuário atualizado"),
     *   @OA\Response(response=404, description="Usuário não encontrado"),
     *   @OA\Response(response=422, description="Erro de validação")
     * )
     */
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
