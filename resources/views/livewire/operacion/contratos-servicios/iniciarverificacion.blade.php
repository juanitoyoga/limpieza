<div>
    @if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
    @endif

    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Servicios listos para verificación</h2>
        <p class="text-sm text-gray-500">
            El contratista ya registró ANTES y DESPUÉS. Al iniciar la verificación se crea el
            hito de control y arranca el proceso de trazabilidad.
        </p>
    </div>

    @if ($this->detalles->isEmpty())
    <div class="rounded-md border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
        No hay servicios pendientes de iniciar verificación en tu barrio por ahora.
    </div>
    @else
    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">Servicio</th>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">Contrato</th>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">Contratista</th>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">ANTES</th>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">DESPUÉS</th>
                    <th class="px-4 py-2 text-right text-xs font-medium uppercase text-gray-500">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach ($this->detalles as $detalle)
                @php
                $antes = $detalle->evidenciaAntes();
                $despues = $detalle->evidenciaDespues();
                @endphp
                <tr wire:key="detalle-{{ $detalle->id }}">
                    <td class="px-4 py-3 text-sm text-gray-900">
                        {{ $detalle->catalogoServicio->nombre ?? "Servicio #{$detalle->id}" }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $detalle->contratoServicio->codigo }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $antes->capturadoPor->full_name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $antes->capturado_en_campo_at?->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $despues->capturado_en_campo_at?->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button
                            type="button"
                            wire:click="iniciarVerificacion({{ $detalle->id }})"
                            wire:confirm="¿Iniciar verificación de este servicio? Se creará el hito de control."
                            class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            Iniciar verificación
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>