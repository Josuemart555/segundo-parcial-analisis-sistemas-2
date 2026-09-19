<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEspecialidadRequest;
use App\Http\Requests\UpdateEspecialidadRequest;
use App\Models\Especialidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EspecialidadController extends Controller
{
    public function index(): View
    {
        return view('especialidades.index', [
            'especialidades' => Especialidad::orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('especialidades.create');
    }

    public function store(StoreEspecialidadRequest $request): RedirectResponse
    {
        Especialidad::create($request->validated());

        return redirect()->route('especialidades.index')->with('status', 'Especialidad creada correctamente.');
    }

    public function edit(Especialidad $especialidad): View
    {
        return view('especialidades.edit', ['especialidad' => $especialidad]);
    }

    public function update(UpdateEspecialidadRequest $request, Especialidad $especialidad): RedirectResponse
    {
        $especialidad->update($request->validated());

        return redirect()->route('especialidades.index')->with('status', 'Especialidad actualizada correctamente.');
    }

    public function destroy(Especialidad $especialidad): RedirectResponse
    {
        $especialidad->delete();

        return redirect()->route('especialidades.index')->with('status', 'Especialidad eliminada.');
    }
}
