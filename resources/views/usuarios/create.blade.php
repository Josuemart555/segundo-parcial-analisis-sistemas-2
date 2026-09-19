<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo usuario</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('usuarios.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf
                @include('usuarios._form')

                <div class="flex justify-end gap-3">
                    <a href="{{ route('usuarios.index') }}" class="px-4 py-2 text-sm text-gray-600">Cancelar</a>
                    <x-primary-button>Guardar</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
