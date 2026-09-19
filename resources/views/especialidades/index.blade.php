<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Especialidades</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('especialidades.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">Nueva especialidad</a>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Activa</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($especialidades as $especialidad)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $especialidad->nombre }}</td>
                                <td class="px-4 py-2">{{ $especialidad->activa ? 'Sí' : 'No' }}</td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <a href="{{ route('especialidades.edit', $especialidad) }}" class="text-blue-600">Editar</a>
                                    <form action="{{ route('especialidades.destroy', $especialidad) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta especialidad?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-4 text-gray-500" colspan="3">No hay especialidades registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
