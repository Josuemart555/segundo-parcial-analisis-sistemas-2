<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Estados de cita</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('estados-cita.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">Nuevo estado</a>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-2">Orden</th>
                            <th class="px-4 py-2">Color</th>
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Slug</th>
                            <th class="px-4 py-2">Terminal</th>
                            <th class="px-4 py-2">Bloquea horario</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($estados as $estado)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $estado->orden }}</td>
                                <td class="px-4 py-2"><span class="inline-block w-4 h-4 rounded-full" style="background: {{ $estado->color }}"></span></td>
                                <td class="px-4 py-2">{{ $estado->nombre }}</td>
                                <td class="px-4 py-2 text-gray-500">{{ $estado->slug }}</td>
                                <td class="px-4 py-2">{{ $estado->es_terminal ? 'Sí' : 'No' }}</td>
                                <td class="px-4 py-2">{{ $estado->bloquea_horario ? 'Sí' : 'No' }}</td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <a href="{{ route('estados-cita.edit', $estado) }}" class="text-blue-600">Editar</a>
                                    <form action="{{ route('estados-cita.destroy', $estado) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este estado?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-4 text-gray-500" colspan="7">No hay estados registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
