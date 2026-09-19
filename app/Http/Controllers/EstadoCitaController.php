<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEstadoCitaRequest;
use App\Http\Requests\UpdateEstadoCitaRequest;
use App\Models\EstadoCita;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EstadoCitaController extends Controller
{
    public function index(): View
    {
        return view('estados-cita.index', [
            'estados' => EstadoCita::orderBy('orden')->get(),
        ]);
    }

    public function create(): View
    {
        return view('estados-cita.create');
    }

    public function store(StoreEstadoCitaRequest $request): RedirectResponse
    {
        EstadoCita::create($request->validated());

        return redirect()->route('estados-cita.index')->with('status', 'Estado creado correctamente.');
    }

    public function edit(EstadoCita $estado_cita): View
    {
        return view('estados-cita.edit', ['estado' => $estado_cita]);
    }

    public function update(UpdateEstadoCitaRequest $request, EstadoCita $estado_cita): RedirectResponse
    {
        $estado_cita->update($request->validated());

        return redirect()->route('estados-cita.index')->with('status', 'Estado actualizado correctamente.');
    }

    public function destroy(EstadoCita $estado_cita): RedirectResponse
    {
        if ($estado_cita->citas()->exists()) {
            return redirect()->route('estados-cita.index')->with('error', 'No se puede eliminar un estado que tiene citas asociadas.');
        }

        $estado_cita->delete();

        return redirect()->route('estados-cita.index')->with('status', 'Estado eliminado.');
    }
}
