let map;
let polygon = null;
let tempPolyline = null;
let markers = [];
let polygonPoints = [];

// Inputs del formulario
const inputLat = document.getElementById('input-lat');
const inputLng = document.getElementById('input-lng');
const inputPoligono = document.getElementById('input-poligono');
const polygonInfo = document.getElementById('polygon-info');

console.log("JS cargado correctamente");

// Inicializar mapa
function initMap() {
    const defaultCenter = { lat: -0.1807, lng: -78.4678 };

    document.getElementById('closePolygonBtn').addEventListener('click', closePolygon);
    document.getElementById('resetDrawingBtn').addEventListener('click', resetDrawing);

    map = new google.maps.Map(document.getElementById('map'), {
        center: defaultCenter,
        zoom: 15,
        zoomControl: true,
        mapTypeId: 'roadmap',
        streetViewControl: false,
        fullscreenControl: true,
        clickableIcons: false,
    });

    map.addListener('click', (e) => addPoint(e.latLng.lat(), e.latLng.lng()));

}


// Agregar punto al polígono
function addPoint(lat, lng) {
    polygonPoints.push({ lat, lng });

    const marker = new google.maps.Marker({
        position: { lat, lng },
        map: map,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 5,
            fillColor: '#2563EB',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 1,
        },
    });

    markers.push(marker);

    // Dibujar línea temporal
    if (tempPolyline) tempPolyline.setMap(null);

    tempPolyline = new google.maps.Polyline({
        path: polygonPoints,
        strokeColor: '#2563EB',
        strokeWeight: 2,
        map: map,
    });

    polygonInfo.textContent = `${polygonPoints.length} puntos agregados...`;
}

// Cerrar polígono
function closePolygon() {
    if (polygonPoints.length < 3) {
        polygonInfo.textContent = 'Necesitas al menos 3 puntos para formar un polígono.';
        return;
    }

    // Limpiar polyline temporal
    if (tempPolyline) {
        tempPolyline.setMap(null);
        tempPolyline = null;
    }

    // Limpiar marcadores
    markers.forEach(m => m.setMap(null));
    markers = [];

    // Dibujar polígono final
    if (polygon) polygon.setMap(null);

    polygon = new google.maps.Polygon({
        paths: polygonPoints,
        fillColor: '#3B82F6',
        fillOpacity: 0.25,
        strokeColor: '#2563EB',
        strokeWeight: 2,
        editable: true,
        map: map,
    });

    // Eventos de edición
    polygon.getPath().addListener('set_at', syncPolygon);
    polygon.getPath().addListener('insert_at', syncPolygon);
    polygon.getPath().addListener('remove_at', syncPolygon);

    syncPolygon();
}

// Sincronizar polígono con inputs
function syncPolygon() {
    const path = polygon.getPath();
    const points = [];

    for (let i = 0; i < path.getLength(); i++) {
        const p = path.getAt(i);
        points.push({ lat: p.lat(), lng: p.lng() });
    }

    polygonPoints = points;

    // Calcular centroide
    const centroide = calcularCentroide(points);

    inputLat.value = centroide.lat.toFixed(6);
    inputLng.value = centroide.lng.toFixed(6);
    inputPoligono.value = JSON.stringify(points);

    polygonInfo.textContent =
        `✓ Polígono con ${points.length} puntos | Centroide: ${centroide.lat.toFixed(5)}, ${centroide.lng.toFixed(5)}`;
}

// Calcular centroide simple
function calcularCentroide(points) {
    const n = points.length;
    const sumLat = points.reduce((acc, p) => acc + p.lat, 0);
    const sumLng = points.reduce((acc, p) => acc + p.lng, 0);

    return {
        lat: sumLat / n,
        lng: sumLng / n,
    };
}
function resetDrawing() {
    console.log("Reiniciando dibujo…");

    // 1. Eliminar marcadores
    markers.forEach(m => m.setMap(null));
    markers = [];

    // 2. Eliminar polyline temporal
    if (tempPolyline) {
        tempPolyline.setMap(null);
        tempPolyline = null;
    }

    // 3. Eliminar polígono final
    if (polygon) {
        polygon.setMap(null);
        polygon = null;
    }

    // 4. Limpiar puntos
    polygonPoints = [];

    // 5. Limpiar inputs
    inputLat.value = "";
    inputLng.value = "";
    inputPoligono.value = "";
    polygonInfo.textContent = "Dibujo reiniciado. Puedes comenzar de nuevo.";

    // 6. Resetear vista del mapa
    map.setCenter({ lat: -0.1807, lng: -78.4678 });
    map.setZoom(15);

    console.log("Dibujo reiniciado correctamente");
}

// Inicializar mapa cuando cargue la página
window.addEventListener('load', initMap);

