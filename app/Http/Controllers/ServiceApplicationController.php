<?php

namespace App\Http\Controllers;

use App\Models\ServiceApplication;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ServiceApplicationController extends Controller
{

    /**
     * @OA\Post(
     *   path="/api/service-applications",
     *   tags={"Aplicações de Serviço"},
     *   summary="Cria nova aplicação",
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"service_id"},
     *       @OA\Property(property="service_id", type="integer"),
     *       @OA\Property(property="message", type="string", nullable=true)
     *     )
     *   ),
     *   @OA\Response(response=201, description="Aplicação criada com sucesso"),
     *   @OA\Response(response=409, description="Conflito: aplicação já existe"),
     *   @OA\Response(response=422, description="Erro de validação"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function store(Request $request)

    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'message' => 'nullable|string',
        ]);

        $freelancerId = Auth::id();

        $alreadyApplied = ServiceApplication::where('service_id', $validated['service_id'])
            ->where('freelancer_id', $freelancerId)
            ->exists();

        if ($alreadyApplied) {
            return response()->json(['message' => 'Você já aplicou para este serviço.'], 409);
        }

        $application = ServiceApplication::create([
            'service_id' => $validated['service_id'],
            'freelancer_id' => $freelancerId,
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json($application, 201);
    }

    /**
     * @OA\Put(
     *   path="/api/service-applications/{id}",
     *   tags={"Aplicações de Serviço"},
     *   summary="Atualiza status de uma aplicação",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"status"},
     *       @OA\Property(property="status", type="string", enum={"accepted","rejected"})
     *     )
     *   ),
     *   @OA\Response(response=200, description="Status atualizado"),
     *   @OA\Response(response=403, description="Ação não autorizada"),
     *   @OA\Response(response=404, description="Aplicação não encontrada"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $application = ServiceApplication::findOrFail($id);

        // Verifica se o user atual é o dono do serviço
        if ($application->services->contractor_id !== Auth::id()) {
            return response()->json(['message' => 'Ação não autorizada.'], 403);
        }

        $application->status = $validated['status'];
        $application->save();

        return response()->json(['message' => 'Status atualizado.', 'data' => $application]);
    }

    // Freelancer lista as aplicações feitas

    /**
     * @OA\Get(
     *   path="/api/my-applications",
     *   tags={"Aplicações de Serviço"},
     *   summary="Lista as aplicações realizadas pelo freelancer autenticado",
     *   @OA\Response(response=200, description="Lista de aplicações do freelancer"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function myApplications()
    {
        $applications = ServiceApplication::where('freelancer_id', Auth::id())
            ->with('services')
            ->get();

        return response()->json($applications);
    }

    // Contratante lista aplicações para um serviço que ele criou

    /**
     * @OA\Get(
     *   path="/api/services/{serviceId}/applications",
     *   tags={"Aplicações de Serviço"},
     *   summary="Lista as aplicações para um serviço criado pelo contratante autenticado",
     *   @OA\Parameter(
     *     name="serviceId",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(response=200, description="Lista de aplicações"),
     *   @OA\Response(response=404, description="Serviço não encontrado ou não pertence ao contratante"),
     *   @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function applicationsForService($serviceId): JsonResponse
    {
        $service = Services::where('id', $serviceId)
            ->where('contractor_id', Auth::id())
            ->firstOrFail();

        $applications = $service->applications()->with('freelancer')->get();

        return response()->json($applications);
    }
}
