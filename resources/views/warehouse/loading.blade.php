@extends('layouts.main')

@section('title', 'Loading Barang - CV Mugijaya Logistics ERP')

@section('styles')
<style>
    .progress-bar-glow {
        box-shadow: 0 0 8px rgba(99, 102, 241, 0.4);
    }
    .laser-line {
        height: 2px;
        background-color: var(--danger);
        width: 100%;
        position: absolute;
        top: 50%;
        left: 0;
        animation: laserScan 2s infinite ease-in-out;
        box-shadow: 0 0 8px var(--danger);
    }
    @keyframes laserScan {
        0% { top: 10%; }
        50% { top: 90%; }
        100% { top: 10%; }
    }
</style>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Loading Barang Keluar (Loading & Dispatch)</h4>
        <p class="text-muted text-sm m-0">Muat barang ke dalam armada pengiriman dan lepaskan kurir setelah verifikasi muatan selesai.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Loading Queue -->
    <div class="col-12 col-lg-6">
        <div class="erp-card p-4 h-100">
            <h6 class="fw-bold mb-3">
                <i class="bi bi-hourglass-split text-info me-2"></i>Antrean Loading Barang Gudang
            </h6>
            <div class="table-responsive">
                <table class="table align-middle text-sm mb-0">
                    <thead>
                        <tr>
                            <th>Nomor DO</th>
                            <th>Tujuan</th>
                            <th>Driver</th>
                            <th>Armada</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="loading-queue-tbody">
                        <!-- Loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Scan Terminal & Progress Checklist -->
    <div class="col-12 col-lg-6">
        <!-- Default state -->
        <div id="empty-state" class="erp-card p-5 text-center h-100 d-flex flex-column align-items-center justify-content-center border-dashed border-light-dark bg-light-dark">
            <i class="bi bi-truck text-muted mb-3" style="font-size: 3rem;"></i>
            <h6 class="fw-bold">Terminal Loading Belum Aktif</h6>
            <p class="text-muted text-xs" style="max-width: 300px;">Pilih salah satu nomor DO dari antrean loading di sebelah kiri untuk membuka konsol pemindaian.</p>
        </div>

        <!-- Active Scanner Console -->
        <div id="loading-console" class="d-none">
            <!-- Scanner simulation card -->
            <div class="erp-card p-4 mb-4 text-center border-dashed border-light-dark bg-light-dark position-relative overflow-hidden" style="height: 180px;">
                <div class="laser-line"></div>
                <div class="d-inline-flex align-items-center justify-content-center bg-secondary p-3 rounded mb-2 border border-light-dark" style="position: relative; z-index: 2;">
                    <i class="bi bi-barcode text-indigo" style="font-size: 2rem;"></i>
                </div>
                <h6 class="fw-bold mb-1" style="position: relative; z-index: 2;">Scanner Loading Simulator</h6>
                <p class="text-muted text-xs mb-0" style="position: relative; z-index: 2;">Masukkan SKU barang secara manual atau gunakan jalan pintas klik tombol di tabel.</p>
                
                <div class="d-flex gap-2 justify-content-center mt-3" style="position: relative; z-index: 2; max-width: 320px; margin: 0 auto;">
                    <input type="text" class="form-control form-control-sm text-center font-monospace" id="scan-sku-input" placeholder="Masukkan SKU (Contoh: PRD-ELC-001)">
                    <button class="btn btn-sm btn-primary-gradient px-3" onclick="handleSkuScan()"><i class="bi bi-upc-scan"></i> Scan SKU</button>
                </div>
            </div>

            <!-- Cargo checklist card -->
            <div class="erp-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold m-0" id="console-do-number">DO-2026-XXXX</h6>
                        <span class="text-muted text-xs" id="console-driver-info">Driver: Rian Hidayat (H 1234 AB)</span>
                    </div>
                    <span class="badge bg-indigo" id="loading-progress-percentage">0% Terisi</span>
                </div>

                <div class="mb-4">
                    <div class="progress capacity-progress bg-light-dark" style="height: 10px;">
                        <div class="progress-bar progress-bar-glow bg-indigo" role="progressbar" id="console-progress-bar" style="width: 0%"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-xs text-muted text-uppercase fw-semibold mb-2">Checklist Muatan Barang:</h6>
                    <div class="d-flex flex-column gap-2" id="cargo-items-list">
                        <!-- Loaded dynamically -->
                    </div>
                </div>

                <button class="btn btn-success w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2" id="btn-dispatch-delivery" disabled>
                    <i class="bi bi-send-fill"></i> Berangkatkan & Kirim Barang (Dispatch)
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentDoId = null;
    let loadedItems = {}; // Map of sku -> loadedQty

    function renderLoadingQueue() {
        const dos = db.getData('deliveryOrders');
        const drivers = db.getData('drivers');
        const tbody = document.getElementById('loading-queue-tbody');
        tbody.innerHTML = '';

        // Filter DOs:
        // - QC validation status is 'Validated'
        // - Driver is assigned
        // - Delivery status is 'Prepared' (ready for loading)
        const loadingQueue = dos.filter(d => d.validationStatus === 'Validated' && d.driverId && d.status === 'Prepared');

        loadingQueue.forEach(d => {
            const drv = drivers.find(driverObj => driverObj.id === d.driverId);
            const driverName = drv ? drv.name : 'Unknown';
            const vehiclePlate = drv ? drv.licensePlate : '-';

            tbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-primary">${d.doNumber}</td>
                    <td class="text-truncate" style="max-width: 140px;" title="${d.destination}">${d.destination.split(',')[0]}</td>
                    <td>${driverName}</td>
                    <td><span class="badge bg-light-dark text-secondary text-xs">${vehiclePlate}</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-primary-gradient py-1 text-xs" onclick="activateConsole(${d.id})">
                            <i class="bi bi-box-arrow-in-down"></i> Muat Barang
                        </button>
                    </td>
                </tr>
            `;
        });

        if (loadingQueue.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i>
                        Tidak ada antrean DO yang siap loading saat ini.
                        <br><span class="text-xs">Pastikan DO sudah divalidasi QC dan driver telah ditugaskan.</span>
                    </td>
                </tr>
            `;
        }
    }

    function activateConsole(id) {
        const dos = db.getData('deliveryOrders');
        const drivers = db.getData('drivers');
        const products = db.getData('products');
        const order = dos.find(d => d.id === id);

        if (order) {
            currentDoId = id;
            loadedItems = {};
            
            // Initialize loaded amounts to 0
            order.items.forEach(item => {
                loadedItems[item.sku] = 0;
            });

            // Display info
            const drv = drivers.find(driverObj => driverObj.id === order.driverId);
            document.getElementById('console-do-number').innerText = order.doNumber;
            document.getElementById('console-driver-info').innerText = `Driver: ${drv ? drv.name : 'Unknown'} (${drv ? drv.licensePlate : '-'} - ${drv ? drv.vehicleClass : '-'})`;

            // Update UI visibility
            document.getElementById('empty-state').classList.add('d-none');
            document.getElementById('loading-console').classList.remove('d-none');

            document.getElementById('scan-sku-input').value = '';
            updateCargoList();
        }
    }

    function updateCargoList() {
        const dos = db.getData('deliveryOrders');
        const products = db.getData('products');
        const order = dos.find(d => d.id === currentDoId);

        if (!order) return;

        const cargoContainer = document.getElementById('cargo-items-list');
        cargoContainer.innerHTML = '';

        let totalReq = 0;
        let totalLoad = 0;

        order.items.forEach(item => {
            const prod = products.find(p => p.sku === item.sku);
            const prodName = prod ? prod.name : 'Unknown Product';
            const loaded = loadedItems[item.sku] || 0;
            const req = item.quantity;
            
            totalReq += req;
            totalLoad += loaded;

            let rowStatusHtml = '';
            let bgClass = 'bg-light-dark';
            
            if (loaded === 0) {
                rowStatusHtml = `<span class="badge bg-secondary text-xs">Waiting</span>`;
            } else if (loaded < req) {
                rowStatusHtml = `<span class="badge bg-warning text-dark text-xs">Loading (${loaded}/${req})</span>`;
                bgClass = 'bg-warning bg-opacity-10 border-warning border-opacity-20';
            } else {
                rowStatusHtml = `<span class="badge bg-success text-xs"><i class="bi bi-check-lg"></i> Loaded</span>`;
                bgClass = 'bg-success bg-opacity-10 border-success border-opacity-20';
            }

            cargoContainer.innerHTML += `
                <div class="d-flex align-items-center justify-content-between p-2.5 rounded border border-light-dark ${bgClass}">
                    <div class="text-xs" style="max-width: 70%;">
                        <div class="fw-semibold text-primary">${prodName}</div>
                        <div class="text-muted font-monospace">SKU: ${item.sku}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        ${rowStatusHtml}
                        <button class="btn btn-xs btn-outline-info py-0.5 px-1.5 text-xs fw-semibold" onclick="simulateBarcodeScan('${item.sku}')">
                            <i class="bi bi-upc"></i> Scan
                        </button>
                    </div>
                </div>
            `;
        });

        // Progress bar calc
        const pct = totalReq > 0 ? Math.round((totalLoad / totalReq) * 100) : 0;
        document.getElementById('loading-progress-percentage').innerText = `${pct}% Dimuat`;
        
        const pb = document.getElementById('console-progress-bar');
        pb.style.width = `${pct}%`;
        
        if (pct >= 100) {
            pb.className = 'progress-bar progress-bar-glow bg-success';
            document.getElementById('btn-dispatch-delivery').disabled = false;
        } else {
            pb.className = 'progress-bar progress-bar-glow bg-indigo';
            document.getElementById('btn-dispatch-delivery').disabled = true;
        }
    }

    // Simulate scanning button click on item row
    function simulateBarcodeScan(sku) {
        document.getElementById('scan-sku-input').value = sku;
        handleSkuScan();
    }

    // General barcode text input trigger
    function handleSkuScan() {
        const skuInput = document.getElementById('scan-sku-input');
        const skuVal = skuInput.value.trim().toUpperCase();

        if (!skuVal) return;

        const dos = db.getData('deliveryOrders');
        const order = dos.find(d => d.id === currentDoId);

        if (!order) return;

        // Search for SKU inside order items
        const matchedItem = order.items.find(item => item.sku.toUpperCase() === skuVal);

        if (matchedItem) {
            const currentLoaded = loadedItems[matchedItem.sku];
            if (currentLoaded < matchedItem.quantity) {
                loadedItems[matchedItem.sku]++;
                // Trigger sound/flash visual effect
                flashScannerCard();
                updateCargoList();
            } else {
                alert(`Barang dengan SKU ${matchedItem.sku} sudah dimuat lengkap (${matchedItem.quantity}/${matchedItem.quantity})!`);
            }
        } else {
            alert(`SKU ${skuVal} tidak ada dalam daftar barang bawaan Delivery Order ini!`);
        }

        skuInput.value = '';
        skuInput.focus();
    }

    function flashScannerCard() {
        const container = document.querySelector('.laser-line').parentNode;
        const originalBg = container.style.backgroundColor;
        container.style.backgroundColor = 'rgba(16, 185, 129, 0.15)'; // light success glow
        setTimeout(() => {
            container.style.backgroundColor = originalBg;
        }, 300);
    }

    // Dispatch click handling
    document.getElementById('btn-dispatch-delivery').addEventListener('click', () => {
        if (!currentDoId) return;

        const dos = db.getData('deliveryOrders');
        const d = dos.find(orderObj => orderObj.id === currentDoId);

        if (d) {
            // Update DO status to "In Transit" (transit delivery)
            db.updateItem('deliveryOrders', currentDoId, {
                status: 'In Transit'
            });

            // Update Driver status to "On Delivery" (active dispatch)
            if (d.driverId) {
                db.updateItem('drivers', d.driverId, {
                    status: 'On Delivery'
                });
            }

            // Write Audit Log
            const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
            db.logAction(
                activeUser.name, 
                "Logistics Loading", 
                "Loading Finished", 
                `Barang DO ${d.doNumber} dimuat lengkap ke armada. Driver diberangkatkan ke ${d.destination.split(',')[0]}`,
                "Success"
            );

            alert(`Loading DO ${d.doNumber} SELESAI. Driver telah didelegasikan dan status DO diubah menjadi: In Transit.`);

            // Close Console and Reload
            document.getElementById('loading-console').classList.add('d-none');
            document.getElementById('empty-state').classList.remove('d-none');
            currentDoId = null;
            loadedItems = {};
            
            renderLoadingQueue();
        }
    });

    // Initialize queue
    document.addEventListener('DOMContentLoaded', () => {
        renderLoadingQueue();

        // Support link mapping from Detail Page directly to console loading
        const urlParams = new URLSearchParams(window.location.search);
        const shortcutId = parseInt(urlParams.get('id'));
        if (shortcutId) {
            activateConsole(shortcutId);
        }
    });
</script>
@endsection
