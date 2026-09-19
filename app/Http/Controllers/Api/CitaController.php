<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Http\Resources\CitaResource;
use App\Models\Cita;
use App\Services\CitaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CitaController extends Controller
{
    public function __construct(private readonly CitaService $citaService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $citas = $this->citaService->listar(
            $request->only(['doctor_id', 'paciente_id', 'desde', 'hasta'])
        );

        return CitaResource::collection($citas);
    }

    public function store(StoreCitaRequest $request): JsonResponse
    {
        $cita = $this->citaService->crear($request->validated());

        return (new CitaResource($cita->load(['doctor', 'paciente'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Cita $cita): CitaResource
    {
        return new CitaResource($cita->load(['doctor', 'paciente']));
    }

    public function update(UpdateCitaRequest $request, Cita $cita): CitaResource
    {
        $cita = $this->citaService->reprogramar($cita, $request->validated());

        return new CitaResource($cita);
    }
}
