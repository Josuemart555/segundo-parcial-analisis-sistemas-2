<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo estado de cita</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('estados-cita.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf

                <div>
                    <x-input-label for="nombre" value="Nombre" />
                    <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" value="{{ old('nombre') }}" required autofocus />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="slug" value="Slug (identificador único, sin espacios)" />
                    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" value="{{ old('slug') }}" required />
                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="color" value="Color" />
                    <input id="color" name="color" type="color" class="mt-1 block h-10 w-20" value="{{ old('color', '#94a3b8') }}">
                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="orden" value="Orden" />
                    <x-text-input id="orden" name="orden" type="number" min="0" class="mt-1 block w-full" value="{{ old('orden', 0) }}" required />
                    <x-input-error :messages="$errors->get('orden')" class="mt-2" />
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="es_terminal" name="es_terminal" value="1">
                    <x-input-label for="es_terminal" value="Es terminal (no permite cambiar de estado después)" />
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="bloquea_horario" name="bloquea_horario" value="1" checked>
                    <x-input-label for="bloquea_horario" value="Bloquea el horario del doctor (cuenta para el conflicto de citas)" />
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('estados-cita.index') }}" class="px-4 py-2 text-sm text-gray-600">Cancelar</a>
                    <x-primary-button>Guardar</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
