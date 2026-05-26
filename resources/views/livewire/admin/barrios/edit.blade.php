@section('page-title', 'Editar Barrio')
@section('page-description', 'Actualización de datos del Barrio')

<div>
    @if(session()->has('message'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
        <i class="fas fa-check-circle text-green-500 mr-3"></i>
        <p class="text-green-800 font-medium">{{ session('message') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- ── FORMULARIO ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">

            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">
                    <i class="fas fa-edit mr-2 text-blue-500"></i>Información del Barrio
                </h2>
                <p class="text-sm text-gray-500">Modifica los datos y el área geográfica</p>
            </div>

            <form wire:submit="update" class="space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <span class="text-red-500">*</span> Identificación GeoPis
                    </label>
                    <input wire:model="id_DMQ" type="text"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition" />
                    @error('id_DMQ')
                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <span class="text-red-500">*</span> Nombre del Barrio
                    </label>
                    <input wire:model="nombre" type="text"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition" />
                    @error('nombre')
                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Sector
                    </label>
                    <input wire:model="sector" type="text"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition" />
                    @error('sector')
                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Parroquia
                    </label>
                    <input wire:model="parroquia" type="text"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition" />
                    @error('parroquia')
                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Coordenadas (solo lectura) --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i> Latitud
                        </label>
                        <input wire:model="lat" type="text" readonly
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 cursor-not-allowed text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i> Longitud
                        </label>
                        <input wire:model="lng" type="text" readonly
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 cursor-not-allowed text-sm" />
                    </div>
                </div>

                {{-- Info polígono --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-draw-polygon text-green-500 mr-1"></i> Polígono
                    </label>
                    <div id="polygon-info-edit"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-500 text-sm min-h-[48px]">
                        @if(!empty($barrio->polygon))
                        {{ count($barrio->polygon) }} puntos cargados
                        @else
                        Sin polígono definido
                        @endif
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex gap-3">
                        <a href="{{ route('barrios.index') }}"
                            class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg text-center transition">
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </a>
                        <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="update"
                            class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="update">
                                <i class="fas fa-save mr-2"></i>Actualizar
                            </span>
                            <span wire:loading wire:target="update">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Guardando...
                            </span>
                        </button>
                    </div>
                </div>

            </form>
        </div>

        {{-- ── MAPA ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">

            <div class="mb-3">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">
                    <i class="fas fa-map mr-2 text-green-500"></i>Mapa del Barrio
                </h2>
                <p class="text-sm text-gray-500 mb-3">
                    <i class="fas fa-mouse-pointer text-blue-500"></i> Click derecho → centroide &nbsp;|&nbsp;
                    <i class="fas fa-draw-polygon text-green-500"></i> Redibujar para actualizar polígono
                </p>
            </div>

            <div class="flex gap-2 mb-3">
                <button type="button" id="btn-draw-edit"
                    class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-draw-polygon mr-1"></i>Redibujar Polígono
                </button>
                <button type="button" id="btn-clear-edit"
                    class="px-3 py-2 bg-gray-500 text-white text-sm rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-trash mr-1"></i>Limpiar
                </button>
            </div>

            <div id="map-edit" class="w-full rounded-lg border border-gray-200" style="height: 480px;"></div>
        </div>
    </div>

    @if($errors->any())
    <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
            <i class="fas fa-exclamation-triangle text-red-400 mt-0.5 mr-3"></i>
            <div>
                <h3 class="text-sm font-medium text-red-800">Por favor corrija los siguientes errores:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
</div>

@php
// Preparar el polígono como JSON seguro para pasar a JavaScript
$polygonForJs = json_encode(json_decode($polygonJson, true) ?: []);
$edit_lat = $lat ?? null;
$edit_lng = $lng ?? null;
@endphp

{{-- ── DATOS DEL BARRIO PARA EL MAPA (renderizados por Blade/PHP) ── --}}
<script>
    // Estos valores los escribe PHP — no son @this ni $wire
    const EDIT_LAT = {
        $edit_lat
    };
    const EDIT_LNG = {
        $edit_lng
    };
    const EDIT_POLYGON = {
        $polygonForJs
    };

    const DEFAULT_LAT_E = -0.1807;
    const DEFAULT_LNG_E = -78.4678;

    let mapEdit, drawingManagerEdit, currentPolygonEdit, centroidMarkerEdit;

    // Helper: obtiene la instancia Livewire 4 del componente
    function getWireEdit() {
        return window.Livewire.find(
            document.querySelector('[wire\\:id]').getAttribute('wire:id')
        );
    }

    function initMapEdit() {
        const centerLat = EDIT_LAT ?? DEFAULT_LAT_E;
        const centerLng = EDIT_LNG ?? DEFAULT_LNG_E;

        mapEdit = new google.maps.Map(document.getElementById('map-edit'), {
            center: {
                lat: centerLat,
                lng: centerLng
            },
            zoom: 15,
            mapTypeId: 'roadmap',
            streetViewControl: false,
            fullscreenControl: true,
        });

        drawingManagerEdit = new google.maps.drawing.DrawingManager({
            drawingMode: null,
            drawingControl: false,
            polygonOptions: {
                fillColor: '#3B82F6',
                fillOpacity: 0.25,
                strokeColor: '#2563EB',
                strokeWeight: 2,
                editable: true,
            },
        });
        drawingManagerEdit.setMap(mapEdit);

        // Cargar polígono existente
        if (EDIT_POLYGON && EDIT_POLYGON.length > 0) {
            loadExistingPolygon(EDIT_POLYGON);
        }

        // Cargar centroide existente
        if (EDIT_LAT !== null && EDIT_LNG !== null) {
            placeCentroidEdit(EDIT_LAT, EDIT_LNG, false);
        }

        // Nuevo polígono dibujado
        google.maps.event.addListener(drawingManagerEdit, 'polygoncomplete', function(polygon) {
            if (currentPolygonEdit) currentPolygonEdit.setMap(null);
            currentPolygonEdit = polygon;
            drawingManagerEdit.setDrawingMode(null);
            syncPolygonEdit(polygon);

            polygon.getPath().addListener('set_at', () => syncPolygonEdit(polygon));
            polygon.getPath().addListener('insert_at', () => syncPolygonEdit(polygon));
            polygon.getPath().addListener('remove_at', () => syncPolygonEdit(polygon));
        });

        // Click derecho → centroide
        mapEdit.addListener('rightclick', function(e) {
            placeCentroidEdit(e.latLng.lat(), e.latLng.lng(), true);
        });

        // Botón redibujar
        document.getElementById('btn-draw-edit').addEventListener('click', function() {
            if (currentPolygonEdit) currentPolygonEdit.setMap(null);
            currentPolygonEdit = null;
            drawingManagerEdit.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
        });

        // Botón limpiar
        document.getElementById('btn-clear-edit').addEventListener('click', function() {
            if (currentPolygonEdit) {
                currentPolygonEdit.setMap(null);
                currentPolygonEdit = null;
            }
            if (centroidMarkerEdit) {
                centroidMarkerEdit.setMap(null);
                centroidMarkerEdit = null;
            }
            document.getElementById('polygon-info-edit').textContent = 'Sin polígono definido';
            getWireEdit().call('updatePolygon', '[]');
            getWireEdit().set('lat', null);
            getWireEdit().set('lng', null);
        });
    }

    function loadExistingPolygon(points) {
        const path = points.map(p => ({
            lat: parseFloat(p.lat),
            lng: parseFloat(p.lng)
        }));

        currentPolygonEdit = new google.maps.Polygon({
            paths: path,
            fillColor: '#3B82F6',
            fillOpacity: 0.25,
            strokeColor: '#2563EB',
            strokeWeight: 2,
            editable: true,
            map: mapEdit,
        });

        document.getElementById('polygon-info-edit').textContent = points.length + ' puntos cargados';

        // Ajustar zoom al área del polígono
        const bounds = new google.maps.LatLngBounds();
        path.forEach(p => bounds.extend(p));
        mapEdit.fitBounds(bounds);

        // Sincronizar edición de vértices
        currentPolygonEdit.getPath().addListener('set_at', () => syncPolygonEdit(currentPolygonEdit));
        currentPolygonEdit.getPath().addListener('insert_at', () => syncPolygonEdit(currentPolygonEdit));
        currentPolygonEdit.getPath().addListener('remove_at', () => syncPolygonEdit(currentPolygonEdit));
    }

    function placeCentroidEdit(lat, lng, syncToLivewire) {
        if (centroidMarkerEdit) centroidMarkerEdit.setMap(null);

        centroidMarkerEdit = new google.maps.Marker({
            position: {
                lat,
                lng
            },
            map: mapEdit,
            title: 'Centroide',
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: '#EF4444',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2,
            },
            draggable: true,
        });

        centroidMarkerEdit.addListener('dragend', function(e) {
            getWireEdit().call('updateCoordenadas', e.latLng.lat(), e.latLng.lng());
        });

        if (syncToLivewire) {
            getWireEdit().call('updateCoordenadas', lat, lng);
        }
    }

    function syncPolygonEdit(polygon) {
        const path = polygon.getPath();
        const points = [];
        for (let i = 0; i < path.getLength(); i++) {
            const p = path.getAt(i);
            points.push({
                lat: p.lat(),
                lng: p.lng()
            });
        }
        document.getElementById('polygon-info-edit').textContent = points.length + ' puntos definidos';
        getWireEdit().call('updatePolygon', JSON.stringify(points));
    }
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=drawing&callback=initMapEdit">
</script>