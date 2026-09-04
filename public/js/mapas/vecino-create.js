let map;
let marker = null;

// Inputs del formulario de Vecinos (deben existir en el Blade, ver nota adjunta)
const inputLat = document.getElementById('input-lat');
const inputLng = document.getElementById('input-lng');
const inputCallePrincipal = document.getElementById('input-calle-principal');
const inputCalleSecundaria = document.getElementById('input-calle-secundaria');
const polygonInfo = document.getElementById('polygon-info');

console.log("JS de Registro de Vecinos cargado correctamente");

// Inicializar mapa enfocado en la zona por defecto (Quito, DMQ)
function initMap() {
    // Coordenadas temporales de prueba en Manchester (barrio de prueba actual)
    // TODO: cambiar a un centro de Quito/DMQ antes de producción
    const defaultCenter = { lat: 53.484251, lng: -2.228189 };

    const mapContainer = document.getElementById('map-registration');
    if (!mapContainer) {
        console.error("No se encontró el contenedor #map-registration en el DOM");
        return;
    }

    const resetBtn = document.getElementById('resetDrawingBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => resetPin(defaultCenter));
    }

    map = new google.maps.Map(mapContainer, {
        center: defaultCenter,
        zoom: 16, // Zoom más cercano para que el vecino vea los techos de las casas
        zoomControl: true,
        mapTypeId: 'roadmap',
        streetViewControl: false,
        fullscreenControl: true,
        clickableIcons: false,
    });

    // 1. Crear el marcador único y arrastrable
    marker = new google.maps.Marker({
        position: defaultCenter,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: "Arrástrame hasta tu casa"
    });

    // 2. Evento: cuando el usuario termina de arrastrar el pin
    marker.addListener('dragend', () => {
        const position = marker.getPosition();
        procesarUbicacionNueva(position.lat(), position.lng());
    });

    // 3. Evento: cuando el usuario hace clic directo en cualquier parte del mapa
    map.addListener('click', (e) => {
        marker.setPosition(e.latLng);
        procesarUbicacionNueva(e.latLng.lat(), e.latLng.lng());
    });

    // Procesar la ubicación por defecto al cargar por primera vez
    procesarUbicacionNueva(defaultCenter.lat, defaultCenter.lng);
}

/**
 * Coordina la actualización de coordenadas y ejecuta el Reverse Geocoding
 */
function procesarUbicacionNueva(lat, lng) {
    // Actualizar los spans visuales (si existen)
    const latDisplay = document.getElementById('lat-display');
    const lngDisplay = document.getElementById('lng-display');
    if (latDisplay) latDisplay.textContent = lat.toFixed(6);
    if (lngDisplay) lngDisplay.textContent = lng.toFixed(6);

    // Sincronizar inputs numéricos ocultos (necesarios para Livewire)
    if (inputLat) {
        inputLat.value = lat.toFixed(6);
        inputLat.dispatchEvent(new Event('input'));
    }
    if (inputLng) {
        inputLng.value = lng.toFixed(6);
        inputLng.dispatchEvent(new Event('input'));
    }

    if (polygonInfo) {
        polygonInfo.textContent = `📍 Ubicación marcada: ${lat.toFixed(5)}, ${lng.toFixed(5)}. Buscando dirección oficial...`;
    }

    ejecutarReverseGeocoding(lat, lng);
}

/**
 * Llama a la API de Google para extraer las calles reales basados en el Pin
 */
function ejecutarReverseGeocoding(lat, lng) {
    const geocoder = new google.maps.Geocoder();
    const latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };

    geocoder.geocode({ location: latlng }, (results, status) => {
        if (status === "OK") {
            if (results[0]) {
                console.log("Dirección encontrada: ", results[0].formatted_address);

                let callePrincipal = "";
                let calleSecundaria = "";

                const addressComponents = results[0].address_components;

                const routeComponent = addressComponents.find(c => c.types.includes("route"));
                if (routeComponent) {
                    callePrincipal = routeComponent.long_name;
                }

                const intersectionComponent = addressComponents.find(c => c.types.includes("intersection"));
                if (intersectionComponent) {
                    calleSecundaria = intersectionComponent.long_name;
                } else {
                    const neighborhood = addressComponents.find(c => c.types.includes("neighborhood") || c.types.includes("sublocality"));
                    calleSecundaria = neighborhood ? neighborhood.long_name : "Sin intersección registrada";
                }

                if (inputCallePrincipal) {
                    inputCallePrincipal.value = callePrincipal;
                    inputCallePrincipal.dispatchEvent(new Event('input'));
                }
                if (inputCalleSecundaria) {
                    inputCalleSecundaria.value = calleSecundaria;
                    inputCalleSecundaria.dispatchEvent(new Event('input'));
                }

                if (polygonInfo) {
                    polygonInfo.textContent = `✓ Dirección validada: ${callePrincipal} y ${calleSecundaria}`;
                }

            } else {
                if (polygonInfo) polygonInfo.textContent = "⚠ No se encontraron direcciones detalladas en este punto.";
            }
        } else {
            console.error("Geocoder falló debido a: " + status);
            if (polygonInfo) polygonInfo.textContent = "⚠ Error al conectar con el servicio de direcciones.";
        }
    });
}

/**
 * Resetea el pin a la posición original por defecto
 */
function resetPin(coords) {
    if (marker) {
        marker.setPosition(coords);
        map.setCenter(coords);
        map.setZoom(16);
        procesarUbicacionNueva(coords.lat, coords.lng);
    }
}

// Inicializar cuando la ventana esté completamente lista
window.addEventListener('load', initMap);