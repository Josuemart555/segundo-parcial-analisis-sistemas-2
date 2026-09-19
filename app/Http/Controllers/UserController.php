<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        return view('usuarios.index', [
            'usuarios' => User::with(['roles', 'especialidad'])->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('usuarios.create', [
            'roles' => Role::orderBy('name')->pluck('name'),
            'especialidades' => Especialidad::where('activa', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $user = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'especialidad_id' => $datos['role'] === 'doctor' ? $datos['especialidad_id'] : null,
            'telefono' => $datos['telefono'] ?? null,
            'activo' => $datos['activo'] ?? true,
        ]);

        $user->syncRoles([$datos['role']]);

        return redirect()->route('usuarios.index')->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): View
    {
        return view('usuarios.edit', [
            'usuario' => $usuario,
            'roles' => Role::orderBy('name')->pluck('name'),
            'especialidades' => Especialidad::where('activa', true)->orderBy('nombre')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $usuario): RedirectResponse
    {
        $datos = $request->validated();

        $usuario->fill([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'especialidad_id' => $datos['role'] === 'doctor' ? $datos['especialidad_id'] : null,
            'telefono' => $datos['telefono'] ?? null,
            'activo' => $datos['activo'] ?? true,
        ]);

        if (! empty($datos['password'])) {
            $usuario->password = $datos['password'];
        }

        $usuario->save();
        $usuario->syncRoles([$datos['role']]);

        return redirect()->route('usuarios.index')->with('status', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('status', 'Usuario eliminado.');
    }
}
