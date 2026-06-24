@extends('layouts.main')

@section('title', 'Pelacakan GPS - CV Mugijaya Logistics ERP')

@section('styles')
<!-- Leaflet.js Map CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .pulse-dot {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse 1.2s infinite;
    }
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }
</style>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Pelacakan GPS Armada (GPS Tracking)</h4>
        <p class="text-muted text-sm m-0">Pantau posisi kendaraan pengiriman secara real-time yang sedang melakukan transit.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Left Panel: Map Container -->
    <div class="col-12 col-xl-9">
        <div class="erp-card position-relative" style="height: 570px;">
            <!-- Status Overlay -->
            <div class="position-absolute bg-secondary bg-opacity-95 p-3 rounded shadow-lg border border-light-dark" style="top: 15px; left: 60px; z-index: 1000; max-width: 250px;">
                <div class="text-xs text-muted mb-1 font-monospace">STATUS KENDARAAN:</div>
                <div class="fw-bold text-sm text-primary" id="map-status-name">Pilih DO dari panel kanan</div>
                <div class="text-xs text-secondary mt-1" id="map-status-desc">Simulasi GPS tidak berjalan.</div>
                <div class="d-flex align-items-center justify-content-between mt-2.5 pt-2 border-top border-light-dark d-none" id="speed-control">
                    <span class="text-xs text-muted">Kecepatan:</span>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-xs btn-outline-info active" onclick="setSpeed(1)">1x</button>
                        <button class="btn btn-xs btn-outline-info" onclick="setSpeed(2)">2x</button>
                        <button class="btn btn-xs btn-outline-info" onclick="setSpeed(4)">4x</button>
                    </div>
                </div>
            </div>

            <div id="map-container" style="height: 100%; width: 100%;"></div>
        </div>
    </div>

    <!-- Right Panel: Active Shipments list -->
    <div class="col-12 col-xl-3">
        <div class="erp-card p-4 h-100 d-flex flex-column justify-content-between" style="min-height: 570px;">
            <div>
                <h6 class="fw-bold mb-3"><i class="bi bi-truck text-indigo me-2"></i>Armada Dalam Transit</h6>
                <div class="d-flex flex-column gap-2" id="transit-do-list">
                    <!-- Loaded dynamically -->
                </div>
            </div>
            
            <div class="mt-4 bg-light-dark p-3 rounded border border-light-dark text-xs">
                <div class="fw-bold mb-1"><i class="bi bi-info-circle-fill text-indigo"></i> Info Simulasi</div>
                <p class="text-muted m-0">Klik tombol <strong>"Lacak Map"</strong> untuk menyalakan modul simulasi GPS dan melihat armada sopir bergerak sepanjang rute.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet.js Map script -->
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let map;
    let vehicleMarker = null;
    let routePolyline = null;
    let activeRoutePoints = [];
    let currentPointIndex = 0;
    let simulationTimer = null;
    let simulationSpeed = 1000; // time in ms per step

    // Predefined route coordinates starting from Gudang Utama Semarang
    const ROUTES = {
        // Gudang Semarang to Solo
        "DO-2026-0001": [
            [-6.9745, 110.4493], // WH Semarang
            [-7.0425, 110.4357], // Ungaran
            [-7.1583, 110.4072], // Bawen
            [-7.2341, 110.4190], // Salatiga
            [-7.3820, 110.5100], // Boyolali
            [-7.5310, 110.7410], // Kartasura
            [-7.5561, 110.8218]  // Solo City Center (Dest)
        ],
        // Gudang Semarang to Kendal / Pekalongan
        "DO-2026-0004": [
            [-6.9745, 110.4493], // WH Semarang
            [-6.9800, 110.3500], // Mangkang
            [-6.9180, 110.2050], // Kendal
            [-6.9310, 110.0500], // Weleri
            [-6.9020, 109.8000], // Batang
            [-6.8901, 109.6800]  // Pekalongan (Dest)
        ],
        // Generic fallback route
        "default": [
            [-6.9745, 110.4493],
            [-6.9600, 110.4800],
            [-6.9500, 110.5200],
            [-6.8000, 110.8400]
        ]
    };

    function initMap() {
        // Center map around Semarang, Indonesia
        map = L.map('map-container').setView([-6.9745, 110.4493], 10);
        
        // Add tile layer
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // Add starting point warehouse marker
        const warehouseIcon = L.divIcon({
            html: '<div style="background-color: #6366f1; color: white; padding: 6px; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; box-shadow: 0 0 10px #6366f1;"><i class="bi bi-house-gear-fill"></i></div>',
            className: '',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        L.marker([-6.9745, 110.4493], { icon: warehouseIcon })
            .addTo(map)
            .bindPopup('<strong style="color:#6366f1;">Gudang Utama Semarang (WH-SMG-01)</strong><br>Origin Depo Utama.')
            .openPopup();
    }

    function renderTransitList() {
        const dos = db.getData('deliveryOrders');
        const drivers = db.getData('drivers');
        const listContainer = document.getElementById('transit-do-list');
        listContainer.innerHTML = '';

        const transitDos = dos.filter(d => d.status === 'In Transit');

        transitDos.forEach(d => {
            const driverObj = drivers.find(drv => drv.id === d.driverId);
            const driverName = driverObj ? driverObj.name : 'Unknown Driver';
            const plate = driverObj ? driverObj.licensePlate : 'H XXXX XX';

            listContainer.innerHTML += `
                <div class="erp-card p-3 bg-light-dark border border-light-dark mb-2 transit-item-card" id="do-card-${d.id}">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="fw-bold text-primary text-xs">${d.doNumber}</span>
                        <span class="badge bg-indigo text-xs"><i class="bi bi-truck"></i> In Transit</span>
                    </div>
                    <div class="text-xs text-secondary mb-2 text-truncate" title="${d.destination}"><i class="bi bi-geo-alt-fill text-danger me-1"></i>${d.destination.split(',')[0]}</div>
                    <div class="text-xs text-muted mb-2"><i class="bi bi-person-fill me-1"></i>${driverName} (${plate})</div>
                    <button class="btn btn-xs btn-primary-gradient w-100 py-1" onclick="startGpsTracking('${d.doNumber}', ${d.id})"><i class="bi bi-geo-fill"></i> Lacak Map</button>
                </div>
            `;
        });

        if (transitDos.length === 0) {
            listContainer.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-geo-alt d-block mb-2 fs-3"></i>Tidak ada armada sedang di perjalanan (Transit).</div>';
        }
    }

    // Trigger moving marker GPS simulation
    function startGpsTracking(doNumber, doId) {
        // Clear previous simulation
        if (simulationTimer) clearInterval(simulationTimer);
        if (routePolyline) map.removeLayer(routePolyline);
        if (vehicleMarker) map.removeLayer(vehicleMarker);

        // Highlight active card
        document.querySelectorAll('.transit-item-card').forEach(card => card.classList.remove('border-indigo'));
        const activeCard = document.getElementById(`do-card-${doId}`);
        if (activeCard) activeCard.classList.add('border-indigo');

        // Find driver name for status overlay
        const dos = db.getData('deliveryOrders');
        const drivers = db.getData('drivers');
        const d = dos.find(doObj => doObj.id === doId);
        const driverObj = drivers.find(drv => drv.id === d.driverId);
        const driverName = driverObj ? driverObj.name : 'Rian Hidayat';
        const vehicleNopol = driverObj ? driverObj.licensePlate : 'H 1234 AB';

        document.getElementById('map-status-name').innerText = `${doNumber} - ${driverName}`;
        document.getElementById('map-status-desc').innerHTML = `<span class="pulse-dot d-inline-block me-1"></span> Bergerak... (Nopol: ${vehicleNopol})`;
        document.getElementById('speed-control').classList.remove('d-none');

        // Select route points
        activeRoutePoints = ROUTES[doNumber] || ROUTES["default"];
        currentPointIndex = 0;

        // Draw route line
        routePolyline = L.polyline(activeRoutePoints, {
            color: '#6366f1',
            weight: 4,
            opacity: 0.8,
            dashArray: '5, 10'
        }).addTo(map);

        // Fit map bounds to show full route
        map.fitBounds(routePolyline.getBounds(), { padding: [50, 50] });

        // Create vehicle marker (glowing truck circle)
        const vehicleIcon = L.divIcon({
            html: '<div style="background-color: #10b981; color: white; padding: 6px; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; box-shadow: 0 0 15px #10b981;"><i class="bi bi-truck"></i></div>',
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        vehicleMarker = L.marker(activeRoutePoints[0], { icon: vehicleIcon }).addTo(map);
        vehicleMarker.bindPopup(`<strong>${driverName}</strong><br>Nopol: ${vehicleNopol}<br>Status: On Duty.`).openPopup();

        // Run coordinates iteration
        runSimulationStep();
    }

    function runSimulationStep() {
        simulationTimer = setInterval(() => {
            if (currentPointIndex < activeRoutePoints.length - 1) {
                currentPointIndex++;
                const nextLatLng = activeRoutePoints[currentPointIndex];
                
                // Move marker smoothly
                vehicleMarker.setLatLng(nextLatLng);
                map.panTo(nextLatLng);

                // Add progress update description
                const progress = Math.round((currentPointIndex / (activeRoutePoints.length - 1)) * 100);
                document.getElementById('map-status-desc').innerHTML = `<span class="pulse-dot d-inline-block me-1"></span> Bergerak... (${progress}% Perjalanan)`;
            } else {
                // Route complete
                clearInterval(simulationTimer);
                document.getElementById('map-status-desc').innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i> Tiba di Lokasi Tujuan!`;
                document.getElementById('speed-control').classList.add('d-none');
                vehicleMarker.bindPopup(`<strong>Armada Tiba!</strong><br>Siap melakukan Proof of Delivery (POD).`).openPopup();
            }
        }, simulationSpeed);
    }

    function setSpeed(multiplier) {
        // Update active styling
        document.querySelectorAll('#speed-control button').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        // Reset timer with new speed
        clearInterval(simulationTimer);
        simulationSpeed = 1000 / multiplier;
        runSimulationStep();
    }

    document.addEventListener('DOMContentLoaded', () => {
        initMap();
        renderTransitList();
    });
</script>
@endsection
