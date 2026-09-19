@php
    $esEdicion = isset($usuario);
    $rolActual = old('role', $esEdicion ? $usuario->roles->first()?->name : null);
@endphp

<div>
    <x-input-label for="name" value="Nombre" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $usuario->name ?? '') }}" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="email" value="Email" />
    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $usuario->email ?? '') }}" required />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div>
    <x-input-label for="password" :value="$esEdicion ? 'Nueva contraseña (dejar vacío para no cambiarla)' : 'Contraseña'" />
    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" :required="! $esEdicion" />
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<div>
    <x-input-label for="password_confirmation" value="Confirmar contraseña" />
    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" :required="! $esEdicion" />
</div>

<div>
    <x-input-label for="role" value="Rol" />
    <select id="role" name="role" class="mt-1 block w-full border-gray-300 rounded-md" required onchange="document.getElementById('campo-especialidad').classList.toggle('hidden', this.value !== 'doctor')">
        <option value="">Selecciona un rol</option>
        @foreach ($roles as $rol)
            <option value="{{ $rol }}" @selected($rolActual === $rol)>{{ ucfirst($rol) }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('role')" class="mt-2" />
</div>

<div id="campo-especialidad" class="{{ $rolActual === 'doctor' ? '' : 'hidden' }}">
    <x-input-label for="especialidad_id" value="Especialidad" />
    <select id="especialidad_id" name="especialidad_id" class="mt-1 block w-full border-gray-300 rounded-md">
        <option value="">Selecciona una especialidad</option>
        @foreach ($especialidades as $especialidad)
            <option value="{{ $especialidad->id }}" @selected(old('especialidad_id', $usuario->especialidad_id ?? null) == $especialidad->id)>{{ $especialidad->nombre }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('especialidad_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="telefono" value="Teléfono" />
    <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" value="{{ old('telefono', $usuario->telefono ?? '') }}" />
    <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
</div>

<div class="flex items-center gap-2">
    <input type="checkbox" id="activo" name="activo" value="1" @checked(old('activo', $usuario->activo ?? true))>
    <x-input-label for="activo" value="Activo" />
</div>
