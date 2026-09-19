<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva especialidad</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('especialidades.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf

                <div>
                    <x-input-label for="nombre" value="Nombre" />
                    <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" value="{{ old('nombre') }}" required autofocus />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="activa" name="activa" value="1" checked>
                    <x-input-label for="activa" value="Activa" />
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('especialidades.index') }}" class="px-4 py-2 text-sm text-gray-600">Cancelar</a>
                    <x-primary-button>Guardar</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
