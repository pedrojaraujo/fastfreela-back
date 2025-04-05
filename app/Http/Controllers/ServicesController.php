<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicesRequest;
use App\Http\Requests\UpdateServicesRequest;
use App\Models\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Services::with('category')->get(), 200);
    }

    public function store(StoreServicesRequest $request)
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

    public function show(Services $service)
    {
        try {
            return response()->json($service->load('category'), 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

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
