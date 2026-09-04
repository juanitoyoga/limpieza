@section('page-title', 'Contratos de Servicio')
@section('page-description', 'Gestión de proveedores y contratistas')


<div>
    @if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
    @endif

    @error('asignacion')
    <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
        {{ $message }}
    </div>
    @enderror
    {{-- Encabezado --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-xl font-bold">{{ $contrato->codigo }} — {{ $contrato->titulo }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $contrato->proveedor->nombre_comercial ?? '—' }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-white {{ $contrato->estadoColor() }}">
                {{ $contrato->estadoLabel() }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-400">Fecha inicio</p>
                <p class="font-medium">{{ $contrato->fecha_inicio?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-400">Fecha fin estimada</p>
                <p class="font-medium">{{ $contrato->fecha_fin_estimada?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-400">Monto total</p>
                <p class="font-medium">${{ number_format($contrato->monto_total, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-400">Oferta origen</p>
                <p class="font-medium">{{ $contrato->oferta->codigo ?? '—' }}</p>
            </div>
        </div>


    </div>
    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">Contacto</th>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">Cargo</th>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">Estado</th>
                    <th class="px-4 py-2 text-right text-xs font-medium uppercase text-gray-500">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach ($this->contactos as $contacto)
                @php
                $contratista = $contacto->contratista;
                $asignacionActual = $contratista?->asignaciones->first(); // ya viene filtrada a este contrato
                $asignadoAEsteContrato = $asignacionActual?->is_active ?? false;
                @endphp
                <tr wire:key="contacto-{{ $contacto->id }}">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $contacto->nombre_completo }}</div>
                        <div class="text-xs text-gray-500">{{ $contacto->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $contacto->cargo }}</td>
                    <td class="px-4 py-3">
                        @if ($asignadoAEsteContrato)
                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                            Asignado a este contrato
                        </span>
                        @elseif ($contratista && ! $contratista->is_active)
                        <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-700">
                            Contratista inactivo
                        </span>
                        @elseif ($contratista)
                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                            Contratista (otro contrato)
                        </span>
                        @else
                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-500">
                            Sin cuenta
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if ($asignadoAEsteContrato)
                        <button
                            type="button"
                            wire:click="revocar({{ $asignacionActual->id }})"
                            wire:confirm="¿Revocar el acceso de {{ $contacto->nombre_completo }} a este contrato?"
                            class="text-sm font-medium text-red-600 hover:text-red-800">
                            Revocar
                        </button>
                        @else
                        <button
                            type="button"
                            wire:click="asignar({{ $contacto->id }})"
                            class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            {{ $contratista ? 'Activar y asignar' : 'Generar usuario y asignar' }}
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>