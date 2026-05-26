@section('page-title', 'Nuevo Barrio')
@section('page-description', 'Complete el formulario y dibuje el área del barrio en el mapa')

<div id="componente-create-barrio">
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
                <p class="text-sm text-gray-500">Los campos marcados con * son obligatorios</p>
            </div>

            <form wire:submit="store" class="space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <span class="text-red-500">*</span> Identificación GeoPis
                    </label>
                    <input wire:model="id_DMQ" type="text" placeholder="Ej: DMQ-001"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition" />
                    @error('id_DMQ')
                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <span class="text-red-500">*</span> Nombre del Barrio
                    </label>
                    <input wire:model="nombre" type="text" placeholder="Ej: Centro Histórico"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition" />
                    @error('nombre')
                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <span class="text-red-500">*</span> Sector
                    </label>
                    <input wire:model="sector" type="text" placeholder="Ej: Norte"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition" />
                    @error('sector')
                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <span class="text-red-500">*</span> Parroquia
                    </label>
                    <input wire:model="parroquia" type="text" placeholder="Ej: San Sebastián"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition" />
                    @error('parroquia')
                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Coordenadas (solo lectura, se llenan desde el mapa) --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i> Latitud
                        </label>
                        <input wire:model="lat" type="text" readonly placeholder="Click derecho en mapa"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 cursor-not-allowed text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i> Longitud
                        </label>
                        <input wire:model="lng" type="text" readonly placeholder="Click derecho en mapa"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 cursor-not-allowed text-sm" />
                    </div>
                </div>

                {{-- Info polígono --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-draw-polygon text-green-500 mr-1"></i> Polígono
                    </label>
                    <div id="polygon-info-create"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-500 text-sm min-h-[48px]">
                        Sin polígono dibujado
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="store"
                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="store">
                            <i class="fas fa-save mr-2"></i>Registrar Barrio
                        </span>
                        <span wire:loading wire:target="store">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Procesando...
                        </span>
                    </button>
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
                    <i class="fas fa-draw-polygon text-green-500"></i> Botón → dibujar polígono
                </p>
            </div>

            <div class="flex gap-2 mb-3">
                <button type="button" id="btn-draw-create"
                    class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-draw-polygon mr-1"></i>Dibujar Polígono
                </button>
                <button type="button" id="btn-clear-create"
                    class="px-3 py-2 bg-gray-500 text-white text-sm rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-trash mr-1"></i>Limpiar
                </button>
            </div>

            <div id="map-create" class="w-full rounded-lg border border-gray-200" style="height: 480px;"></div>
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

{{-- ── GOOGLE MAPS — Livewire 4 usa $wire (no @this) ── --}}
<script>
    const DEFAULT_LAT_C = -0.1807;
    const DEFAULT_LNG_C = -78.4678;

    let mapCreate, drawingManagerCreate, currentPolygonCreate, centroidMarkerCreate;

    // Función segura para Livewire 4 usando el ID que pusimos en el paso 1
    function getLivewireComponent() {
        const el = document.getElementById('componente-create-barrio');
        if (el && window.Livewire) {
            return window.Livewire.find(el);
        }
        return null;
    }

    function initMapCreate() {
        mapCreate = new google.maps.Map(document.getElementById('map-create'), {
            center: {
                lat: DEFAULT_LAT_C,
                lng: DEFAULT_LNG_C
            },
            zoom: 13,
            mapTypeId: 'roadmap',
            streetViewControl: false,
            fullscreenControl: true,
        });

        drawingManagerCreate = new google.maps.drawing.DrawingManager({
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
        drawingManagerCreate.setMap(mapCreate);

        // Polígono completado
        google.maps.event.addListener(drawingManagerCreate, 'polygoncomplete', function(polygon) {
            if (currentPolygonCreate) currentPolygonCreate.setMap(null);
            currentPolygonCreate = polygon;
            drawingManagerCreate.setDrawingMode(null);
            syncPolygonCreate(polygon);

            polygon.getPath().addListener('set_at', () => syncPolygonCreate(polygon));
            polygon.getPath().addListener('insert_at', () => syncPolygonCreate(polygon));
            polygon.getPath().addListener('remove_at', () => syncPolygonCreate(polygon));
        });

        // Click derecho → centroide
        mapCreate.addListener('rightclick', function(e) {
            placeCentroidCreate(e.latLng.lat(), e.latLng.lng());
        });

        // Botón dibujar
        document.getElementById('btn-draw-create').addEventListener('click', function() {
            if (currentPolygonCreate) currentPolygonCreate.setMap(null);
            currentPolygonCreate = null;
            drawingManagerCreate.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
        });

        // Botón limpiar
        document.getElementById('btn-clear-create').addEventListener('click', function() {
            if (currentPolygonCreate) {
                currentPolygonCreate.setMap(null);
                currentPolygonCreate = null;
            }
            if (centroidMarkerCreate) {
                centroidMarkerCreate.setMap(null);
                centroidMarkerCreate = null;
            }
            document.getElementById('polygon-info-create').textContent = 'Sin polígono dibujado';

            const lw = getLivewireComponent();
            if (lw) {
                lw.call('updatePolygon', '[]');
                lw.set('lat', null);
                lw.set('lng', null);
            }
        });
    }

    function placeCentroidCreate(lat, lng) {
        if (centroidMarkerCreate) centroidMarkerCreate.setMap(null);

        centroidMarkerCreate = new google.maps.Marker({
            position: {
                lat,
                lng
            },
            map: mapCreate,
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

        centroidMarkerCreate.addListener('dragend', function(e) {
            const lw = getLivewireComponent();
            if (lw) {
                lw.call('updateCoordenadas', e.latLng.lat(), e.latLng.lng());
            }
        });

        const lw = getLivewireComponent();
        if (lw) {
            lw.call('updateCoordenadas', lat, lng);
        }
    }

    function syncPolygonCreate(polygon) {
        const path = polygon.getPath();
        const points = [];
        for (let i = 0; i < path.getLength(); i++) {
            const p = path.getAt(i);
            points.push({
                lat: p.lat(),
                lng: p.lng()
            });
        }
        document.getElementById('polygon-info-create').textContent = points.length + ' puntos definidos';

        const lw = getLivewireComponent();
        if (lw) {
            lw.call('updatePolygon', JSON.stringify(points));
        }
    }
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=drawing&callback=initMapCreate">
</script>