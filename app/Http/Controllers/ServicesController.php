<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicesRequest;
use App\Http\Requests\UpdateServicesRequest;
use App\Models\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;


class ServicesController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/services",
     *   tags={"Serviços"},
     *   summary="Lista todos os serviços",
     *   @OA\Response(response=200, description="Lista de serviços"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(Services::all(), 200);
    }

    /**
     * @OA\Post(
     *   path="/api/services",
     *   tags={"Serviços"},
     *   summary="Cria novo serviço",
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="titulo", type="string", example="Novo serviço"),
     *       @OA\Property(property="descricao", type="string", example="Descrição do serviço"),
     *       @OA\Property(property="category_id", type="integer", example=1)
     *     )
     *   ),
     *   @OA\Response(response=201, description="Serviço criado com sucesso"),
     *   @OA\Response(response=422, description="Erro de validação"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function store(StoreServicesRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validated();
            $data['contractor_id'] = Auth::id();

            $service = Services::create($data);

            return response()->json([
                'message' => 'Serviço criado com sucesso!',
                'service' => $service,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/services/{id}",
     *   tags={"Serviços"},
     *   summary="Exibe dados de um serviço",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(response=200, description="Dados do serviço"),
     *   @OA\Response(response=404, description="Serviço não encontrado"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */

    public function show(Services $service)
    {
        try {
            return response()->json($service, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/my-services",
     *   tags={"Serviços"},
     *   summary="Exibe dados dos serviços cadastrados pelo usuário logado.",
     *   @OA\Response(response=200, description="Dados dos serviços cadastrados pelo usuário."),
     *   @OA\Response(response=204, description="Requisição feita com sucesso, porém o usuário não possui serviços cadastrados."),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function showMyServices(Services $service)
    {
        try {

            $userId = Auth::id();

            $service = Services::where('contractor_id', $userId)->get();

            if ($service->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum serviço encontrado.'
                ], 204);
            }

            return response()->json($service, 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *   path="/api/services/{id}",
     *   tags={"Serviços"},
     *   summary="Atualiza um serviço",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="titulo", type="string", example="Serviço atualizado"),
     *       @OA\Property(property="descricao", type="string", example="Nova descrição"),
     *       @OA\Property(property="category_id", type="integer", example=1)
     *     )
     *   ),
     *   @OA\Response(response=200, description="Serviço atualizado com sucesso"),
     *   @OA\Response(response=422, description="Erro de validação"),
     *   @OA\Response(response=404, description="Serviço não encontrado"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function update(UpdateServicesRequest $request, Services $service)
    {
        try {
            $service->update($request->validated());

            return response()->json([
                'message' => 'Serviço atualizado com sucesso!',
                'service' => $service,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/services/{id}",
     *   tags={"Serviços"},
     *   summary="Remove um serviço",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(response=200, description="Serviço deletado com sucesso"),
     *   @OA\Response(response=404, description="Serviço não encontrado"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function destroy(Services $service)
    {
        try {
            $service->delete();
            return response()->json(['message' => 'Serviço deletado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
