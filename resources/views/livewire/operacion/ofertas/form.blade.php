@csrf
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Información de la Oferta</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Resolución -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Resolución *</label>
            <select name="resolucion_id" id="resolucion_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500">
                <option value="">-- Seleccionar Resolución --</option>
                @foreach ($resoluciones as $res)
                <option value="{{ $res->id }}" {{ old('resolucion_id', $oferta->resolucion_id ?? '') == $res->id ? 'selected' : '' }}>
                    {{ $res->codigo ?? "Resolución #{$res->id}" }} - {{ $res->titulo }}
                </option>
                @endforeach
            </select>
            @error('resolucion_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Proveedor -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Proveedor *</label>
            <select name="proveedor_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500">
                <option value="">-- Seleccionar Proveedor --</option>
                @foreach ($proveedores as $prov)
                <option value="{{ $prov->id }}" {{ old('proveedor_id', $oferta->proveedor_id ?? '') == $prov->id ? 'selected' : '' }}>
                    {{ $prov->razon_social }} (RUC: {{ $prov->ruc }})
                </option>
                @endforeach
            </select>
            @error('proveedor_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Código -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Código Oferta</label>
            <input type="text" name="codigo" value="{{ old('codigo', $oferta->codigo ?? '') }}" placeholder="Ej: OFR-2026-001" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @error('codigo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Estado -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Estado *</label>
            <select name="estado" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @foreach (\App\Models\Oferta::ESTADOS as $est)
                <option value="{{ $est }}" {{ old('estado', $oferta->estado ?? '') == $est ? 'selected' : '' }}>{{ $est }}</option>
                @endforeach
            </select>
            @error('estado') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Observaciones Generales -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Observaciones Generales</label>
            <textarea name="observaciones" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('observaciones', $oferta->observaciones ?? '') }}</textarea>
        </div>
    </div>
</div>

<!-- Servicios Ofertados (Detalle Pivote) -->
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Servicios Incluidos en la Oferta</h2>
        <button type="button" id="btn-add-servicio" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700">
            + Agregar Servicio
        </button>
    </div>

    @error('servicios')
    <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="tabla-servicios">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Catálogo Servicio *</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Res. Servicio ID</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Cantidad</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Costo Unit.</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Subtotal</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Observación</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-12">Acción</th>
                </tr>
            </thead>
            <tbody id="container-servicios" class="divide-y divide-gray-200">
                @php
                $serviciosDetalle = old('servicios', isset($oferta) ? $oferta->ofertaServicios : []);
                @endphp

                @foreach ($serviciosDetalle as $index => $det)
                <tr class="item-servicio">
                    @if (isset($det['id']) || (is_object($det) && $det->id))
                    <input type="hidden" name="servicios[{{ $index }}][id]" value="{{ is_object($det) ? $det->id : $det['id'] }}">
                    @endif

                    <td class="p-2">
                        <select name="servicios[{{ $index }}][catalogo_servicio_id]" required class="catalogo-select border-gray-300 rounded text-sm w-full">
                            <option value="">-- Servicio --</option>
                            @foreach ($catalogoServicios as $cs)
                            @php $csId = is_object($det) ? $det->catalogo_servicio_id : $det['catalogo_servicio_id']; @endphp
                            <option value="{{ $cs->id }}" data-costo="{{ $cs->costo_referencial }}" {{ $csId == $cs->id ? 'selected' : '' }}>
                                {{ $cs->codigo }} - {{ $cs->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </td>

                    <td class="p-2">
                        <input type="number" name="servicios[{{ $index }}][resolucion_servicio_id]" value="{{ is_object($det) ? $det->resolucion_servicio_id : ($det['resolucion_servicio_id'] ?? '') }}" placeholder="Opcional" class="border-gray-300 rounded text-sm w-full">
                    </td>

                    <td class="p-2">
                        <input type="number" name="servicios[{{ $index }}][cantidad]" value="{{ is_object($det) ? $det->cantidad : $det['cantidad'] }}" min="1" required class="input-cantidad border-gray-300 rounded text-sm w-full">
                    </td>

                    <td class="p-2">
                        <input type="number" step="0.01" name="servicios[{{ $index }}][costo_unitario]" value="{{ is_object($det) ? $det->costo_unitario : $det['costo_unitario'] }}" required class="input-costo border-gray-300 rounded text-sm w-full">
                    </td>

                    <td class="p-2">
                        <input type="text" readonly class="input-subtotal bg-gray-100 border-gray-300 rounded text-sm w-full text-right font-semibold" value="$0.00">
                    </td>

                    <td class="p-2">
                        <input type="text" name="servicios[{{ $index }}][observaciones]" value="{{ is_object($det) ? $det->observaciones : ($det['observaciones'] ?? '') }}" class="border-gray-300 rounded text-sm w-full">
                    </td>

                    <td class="p-2 text-center">
                        <button type="button" class="btn-remove-row text-red-600 font-bold hover:text-red-800">✕</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Total de la Oferta -->
    <div class="flex justify-end mt-4 pt-4 border-t">
        <div class="text-right">
            <span class="text-gray-600 font-medium">Monto Total Oferta:</span>
            <span id="monto-total-display" class="text-2xl font-bold text-gray-900 ml-2">$0.00</span>
        </div>
    </div>
</div>

<!-- Plantilla JS para dinámicos -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Antes: "let indexCounter = { { count($serviciosDetalle) } };" -> esto era JS inválido
        // (llaves sueltas), rompía el script completo y nada de la tabla dinámica funcionaba.
        let indexCounter = {
            {
                count($serviciosDetalle)
            }
        };

        const container = document.getElementById('container-servicios');
        const btnAdd = document.getElementById('btn-add-servicio');
        const totalDisplay = document.getElementById('monto-total-display');

        function actualizarBotonesEliminar() {
            const filas = container.querySelectorAll('tr.item-servicio');
            filas.forEach(tr => {
                const btn = tr.querySelector('.btn-remove-row');
                if (btn) btn.disabled = filas.length <= 1;
                if (btn) btn.classList.toggle('opacity-30', filas.length <= 1);
                if (btn) btn.classList.toggle('cursor-not-allowed', filas.length <= 1);
            });
        }

        // Calcular Totales
        function calcularTotales() {
            let total = 0;
            container.querySelectorAll('tr').forEach(tr => {
                const qty = parseFloat(tr.querySelector('.input-cantidad')?.value) || 0;
                const cost = parseFloat(tr.querySelector('.input-costo')?.value) || 0;
                const subtotal = qty * cost;

                const inputSubtotal = tr.querySelector('.input-subtotal');
                if (inputSubtotal) inputSubtotal.value = '$' + subtotal.toFixed(2);

                total += subtotal;
            });
            totalDisplay.textContent = '$' + total.toFixed(2);
        }

        function nuevaFila() {
            const tr = document.createElement('tr');
            tr.className = 'item-servicio';
            tr.innerHTML = `
                <td class="p-2">
                    <select name="servicios[${indexCounter}][catalogo_servicio_id]" required class="catalogo-select border-gray-300 rounded text-sm w-full">
                        <option value="">-- Servicio --</option>
                        @foreach ($catalogoServicios as $cs)
                            <option value="{{ $cs->id }}" data-costo="{{ $cs->costo_referencial }}">
                                {{ $cs->codigo }} - {{ $cs->nombre }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="p-2"><input type="number" name="servicios[${indexCounter}][resolucion_servicio_id]" placeholder="Opcional" class="border-gray-300 rounded text-sm w-full"></td>
                <td class="p-2"><input type="number" name="servicios[${indexCounter}][cantidad]" value="1" min="1" required class="input-cantidad border-gray-300 rounded text-sm w-full"></td>
                <td class="p-2"><input type="number" step="0.01" name="servicios[${indexCounter}][costo_unitario]" value="0.00" required class="input-costo border-gray-300 rounded text-sm w-full"></td>
                <td class="p-2"><input type="text" readonly class="input-subtotal bg-gray-100 border-gray-300 rounded text-sm w-full text-right font-semibold" value="$0.00"></td>
                <td class="p-2"><input type="text" name="servicios[${indexCounter}][observaciones]" class="border-gray-300 rounded text-sm w-full"></td>
                <td class="p-2 text-center"><button type="button" class="btn-remove-row text-red-600 font-bold hover:text-red-800">✕</button></td>
            `;

            container.appendChild(tr);
            indexCounter++;
            calcularTotales();
            actualizarBotonesEliminar();
        }

        btnAdd.addEventListener('click', nuevaFila);

        // Eventos Delegados para dinámicos
        container.addEventListener('change', function(e) {
            if (e.target.classList.contains('catalogo-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const costoRef = selectedOption.getAttribute('data-costo');
                const tr = e.target.closest('tr');
                if (costoRef && tr) {
                    tr.querySelector('.input-costo').value = parseFloat(costoRef).toFixed(2);
                }
            }
            calcularTotales();
        });

        container.addEventListener('input', function(e) {
            if (e.target.classList.contains('input-cantidad') || e.target.classList.contains('input-costo')) {
                calcularTotales();
            }
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-row')) {
                if (container.querySelectorAll('tr.item-servicio').length <= 1) return; // mínimo 1 servicio
                e.target.closest('tr').remove();
                calcularTotales();
                actualizarBotonesEliminar();
            }
        });

        // Si es una oferta nueva sin servicios, arranca con una fila lista para llenar
        if (container.querySelectorAll('tr.item-servicio').length === 0) {
            nuevaFila();
        }

        // Cálculo inicial
        calcularTotales();
        actualizarBotonesEliminar();
    });
</script>