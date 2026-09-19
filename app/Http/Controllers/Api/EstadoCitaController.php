<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EstadoCitaResource;
use App\Models\EstadoCita;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EstadoCitaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return EstadoCitaResource::collection(
            EstadoCita::orderBy('orden')->get()
        );
    }
}
