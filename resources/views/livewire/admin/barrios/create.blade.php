@section('page-title', 'Nuevo Barrio')
@section('page-description', 'Complete el formulario y dibuje el área del barrio en el mapa')

<div id="componente-create-barrio" x-data="mapaBarrio()">
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

                {{-- Centroide calculado automáticamente --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-crosshairs text-red-500 mr-1"></i> Latitud centroide
                        </label>
                        <input wire:model="lat" type="text" readonly placeholder="Se calcula del polígono"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 cursor-not-allowed text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-crosshairs text-red-500 mr-1"></i> Longitud centroide
                        </label>
                        <input wire:model="lng" type="text" readonly placeholder="Se calcula del polígono"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 cursor-not-allowed text-sm" />
                    </div>
                </div>

                {{-- Info polígono --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-draw-polygon text-green-500 mr-1"></i> Polígono
                    </label>
                    <div id="polygon-info-create" wire:ignore
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
                <p class="text-sm text-gray-500 mb-1">
                    <i class="fas fa-draw-polygon text-green-500"></i> Haz clic para agregar puntos &nbsp;|&nbsp;
                    <i class="fas fa-keyboard text-gray-400"></i> Enter o doble clic para cerrar &nbsp;|&nbsp; Esc para limpiar
                </p>
                <div x-show="dibujando"
                    class="mt-2 px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full inline-flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Modo dibujo activo —
                    <span x-text="polygonPoints.length"></span> puntos
                </div>
            </div>

            <div class="flex gap-2 mb-3">
                <button type="button" @click="activarDibujo()"
                    :class="dibujando ? 'bg-green-700 ring-2 ring-green-400' : 'bg-green-600 hover:bg-green-700'"
                    class="px-3 py-2 text-white text-sm rounded-lg transition flex items-center gap-1">
                    <i class="fas fa-draw-polygon"></i>
                    <span x-text="dibujando ? 'Dibujando...' : 'Dibujar Polígono'"></span>
                </button>
                <button type="button" @click="limpiarMapa()"
                    class="px-3 py-2 bg-gray-500 text-white text-sm rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-trash mr-1"></i>Limpiar
                </button>
            </div>

            <div wire:ignore>
                <div id="map-create" class="w-full rounded-lg border border-gray-200" style="height: 480px;"></div>
            </div>
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

{{-- Función global — Alpine la encuentra en window automáticamente --}}
<script>
    window.mapaBarrio = function() {
        return {
            mapCreate: null,
            currentPolygonCreate: null,
            centroidMarkerCreate: null,
            pointMarkers: [],
            polygonPoints: [],
            tempPolyline: null,
            dibujando: false,
            defaultLat: -0.1807,
            defaultLng: -78.4678,

            init() {
                this.initMapCreate();
            },

            initMapCreate() {
                if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                    setTimeout(() => this.initMapCreate(), 250);
                    return;
                }

                this.mapCreate = new google.maps.Map(document.getElementById('map-create'), {
                    center: {
                        lat: parseFloat(this.$wire.lat) || this.defaultLat,
                        lng: parseFloat(this.$wire.lng) || this.defaultLng
                    },
                    zoom: 15,
                    mapTypeId: 'roadmap',
                    streetViewControl: false,
                    fullscreenControl: true,
                });

                // Click izquierdo → agregar punto SOLO si está dibujando
                this.mapCreate.addListener('click', (e) => {
                    if (!this.dibujando) return;
                    this.agregarPunto(e.latLng.lat(), e.latLng.lng());
                });

                // Doble click → cerrar polígono
                this.mapCreate.addListener('dblclick', (e) => {
                    if (!this.dibujando) return;
                    e.stop();
                    this.cerrarPoligono();
                });

                // Teclado: D=dibujar, Enter=cerrar, Esc=limpiar
                window.addEventListener('keydown', (e) => {
                    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;
                    const key = e.key.toLowerCase();
                    if (key === 'd') this.activarDibujo();
                    if (key === 'escape') this.limpiarMapa();
                    if (key === 'enter') { e.preventDefault(); this.cerrarPoligono(); }
                });
            },

            activarDibujo() {
                this.limpiarPoligono();
                this.dibujando = true;
                this.polygonPoints = [];
                this.mapCreate.setOptions({ draggableCursor: 'crosshair' });
                document.getElementById('polygon-info-create').textContent =
                    'Haz clic en el mapa para agregar puntos. Doble clic o Enter para cerrar.';
            },

            agregarPunto(lat, lng) {
                this.polygonPoints.push({ lat, lng });

                const marker = new google.maps.Marker({
                    position: { lat, lng },
                    map: this.mapCreate,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 5,
                        fillColor: '#3B82F6',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 1,
                    }
                });
                this.pointMarkers.push(marker);

                if (this.tempPolyline) this.tempPolyline.setMap(null);
                this.tempPolyline = new google.maps.Polyline({
                    path: this.polygonPoints,
                    strokeColor: '#2563EB',
                    strokeWeight: 2,
                    map: this.mapCreate,
                });

                document.getElementById('polygon-info-create').textContent =
                    `${this.polygonPoints.length} puntos. Doble clic o Enter para cerrar.`;
            },

            cerrarPoligono() {
                if (this.polygonPoints.length < 3) {
                    document.getElementById('polygon-info-create').textContent =
                        'Necesitas al menos 3 puntos para formar un polígono.';
                    return;
                }

                this.dibujando = false;
                this.mapCreate.setOptions({ draggableCursor: null });

                if (this.tempPolyline) { this.tempPolyline.setMap(null); this.tempPolyline = null; }
                this.pointMarkers.forEach(m => m.setMap(null));
                this.pointMarkers = [];

                if (this.currentPolygonCreate) this.currentPolygonCreate.setMap(null);
                this.currentPolygonCreate = new google.maps.Polygon({
                    paths: this.polygonPoints,
                    fillColor: '#3B82F6',
                    fillOpacity: 0.25,
                    strokeColor: '#2563EB',
                    strokeWeight: 2,
                    editable: true,
                    map: this.mapCreate,
                });

                this.currentPolygonCreate.getPath().addListener('set_at',    () => this.syncPolygonCreate());
                this.currentPolygonCreate.getPath().addListener('insert_at', () => this.syncPolygonCreate());
                this.currentPolygonCreate.getPath().addListener('remove_at', () => this.syncPolygonCreate());

                this.syncPolygonCreate();
            },

            calcularCentroide(points) {
                const n = points.length;
                const sumLat = points.reduce((acc, p) => acc + p.lat, 0);
                const sumLng = points.reduce((acc, p) => acc + p.lng, 0);
                return { lat: sumLat / n, lng: sumLng / n };
            },

            actualizarMarcadorCentroide(lat, lng) {
                if (this.centroidMarkerCreate) {
                    this.centroidMarkerCreate.setMap(null);
                    this.centroidMarkerCreate = null;
                }
                this.centroidMarkerCreate = new google.maps.Marker({
                    position: { lat, lng },
                    map: this.mapCreate,
                    title: 'Centroide',
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 10,
                        fillColor: '#EF4444',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 2,
                    },
                    zIndex: 10,
                });
            },

            syncPolygonCreate() {
                const path = this.currentPolygonCreate.getPath();
                const points = [];
                for (let i = 0; i < path.getLength(); i++) {
                    const p = path.getAt(i);
                    points.push({ lat: p.lat(), lng: p.lng() });
                }

                const centroide = this.calcularCentroide(points);
                this.actualizarMarcadorCentroide(centroide.lat, centroide.lng);

                this.$wire.lat = centroide.lat.toFixed(6);
                this.$wire.lng = centroide.lng.toFixed(6);
                if (typeof this.$wire.updateCoordenadas === 'function') {
                    this.$wire.updateCoordenadas(centroide.lat, centroide.lng);
                }

                document.getElementById('polygon-info-create').textContent =
                    `✓ Polígono con ${points.length} puntos | Centroide: ${centroide.lat.toFixed(5)}, ${centroide.lng.toFixed(5)}`;

                if (typeof this.$wire.updatePolygon === 'function') {
                    this.$wire.updatePolygon(JSON.stringify(points));
                } else {
                    this.$wire.poligono = points;
                }
            },

            limpiarPoligono() {
                this.pointMarkers.forEach(m => m.setMap(null));
                this.pointMarkers = [];
                if (this.currentPolygonCreate) { this.currentPolygonCreate.setMap(null); this.currentPolygonCreate = null; }
                if (this.tempPolyline) { this.tempPolyline.setMap(null); this.tempPolyline = null; }
                this.polygonPoints = [];
            },

            limpiarMapa() {
                this.dibujando = false;
                this.mapCreate.setOptions({ draggableCursor: null });
                this.limpiarPoligono();
                if (this.centroidMarkerCreate) { this.centroidMarkerCreate.setMap(null); this.centroidMarkerCreate = null; }
                document.getElementById('polygon-info-create').textContent = 'Sin polígono dibujado';
                this.$wire.lat = null;
                this.$wire.lng = null;
                if (typeof this.$wire.updatePolygon === 'function') {
                    this.$wire.updatePolygon('[]');
                } else {
                    this.$wire.poligono = [];
                }
            },
        };
    };
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}">
</script>
