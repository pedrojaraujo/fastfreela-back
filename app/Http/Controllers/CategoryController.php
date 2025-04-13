<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/categories",
     *   tags={"Categorias"},
     *   summary="Lista todas as categorias",
     *   @OA\Response(response=200, description="Lista de categorias"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * @OA\Post(
     *   path="/api/categories",
     *   tags={"Categorias"},
     *   summary="Cria nova categoria",
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="nome", type="string", example="Categoria X")
     *     )
     *   ),
     *   @OA\Response(response=201, description="Categoria criada com sucesso"),
     *   @OA\Response(response=422, description="Erro de validação"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function store(StoreCategoryRequest $request)
    {
        try {

            $data = $request->validated();
            $category = Category::create($data);

            return response()->json([
                'message' => 'Categoria criada com sucesso!',
                'category' => $category,
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
     * @OA\Get(
     *   path="/api/categories/{id}",
     *   tags={"Categorias"},
     *   summary="Exibe dados de uma categoria",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(response=200, description="Dados da categoria"),
     *   @OA\Response(response=404, description="Categoria não encontrada"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function show(Category $category)
    {
        try {
            return response()->json($category, 200);
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
     *   path="/api/categories/{id}",
     *   tags={"Categorias"},
     *   summary="Atualiza uma categoria",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="nome", type="string", example="Categoria X atualizada")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Categoria atualizada com sucesso"),
     *   @OA\Response(response=422, description="Erro de validação"),
     *   @OA\Response(response=404, description="Categoria não encontrada"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $category->update($request->validated());

            return response()->json([
                'message' => 'Categoria atualizada com sucesso!',
                'category' => $category,
            ], 200);
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
     *   path="/api/categories/{id}",
     *   tags={"Categorias"},
     *   summary="Remove uma categoria",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(response=200, description="Categoria deletada com sucesso"),
     *   @OA\Response(response=404, description="Categoria não encontrada"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return response()->json([
                'message' => 'Categoria deletada com sucesso!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
