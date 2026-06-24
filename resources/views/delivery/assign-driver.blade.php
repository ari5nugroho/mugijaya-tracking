@extends('layouts.main')

@section('title', 'Penugasan Driver & Armada - CV Mugijaya Logistics ERP')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('delivery.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <div>
            <h4 class="fw-bold mb-1">Penugasan Driver & Armada</h4>
            <p class="text-muted text-sm m-0">Tugaskan kurir pengantar dan plat nomor kendaraan ke pesanan Delivery Order.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Form Penugasan -->
    <div class="col-12 col-lg-7">
        <div class="erp-card p-4 h-100">
            <h6 class="fw-bold mb-4">
                <i class="bi bi-person-fill-gear text-indigo me-2"></i>Formulir Dispatcher Logistik
            </h6>
            
            <form id="assign-driver-form">
                <div class="mb-3">
                    <label for="select-do" class="form-label">Pilih Delivery Order (DO)</label>
                    <select class="form-select" id="select-do" required>
                        <option value="" disabled selected>-- Pilih DO yang Belum Ditugaskan --</option>
                        <!-- Loaded dynamically -->
                    </select>
                    <div class="form-text text-muted text-xs">Hanya DO berstatus unassigned yang muncul di sini.</div>
                </div>

                <div class="mb-3">
                    <label for="select-driver" class="form-label">Pilih Driver Tersedia</label>
                    <select class="form-select" id="select-driver" required>
                        <option value="" disabled selected>-- Pilih Kurir / Driver Aktif --</option>
                        <!-- Loaded dynamically -->
                    </select>
                    <div class="form-text text-muted text-xs">Hanya driver dengan status 'Available' yang muncul di sini.</div>
                </div>

                <div class="mb-4">
                    <label for="dispatch-notes" class="form-label">Catatan Instruksi Pengiriman (Optional)</label>
                    <textarea class="form-control text-sm" id="dispatch-notes" rows="3" placeholder="Contoh: Harap hubungi customer 30 menit sebelum sampai di lokasi tujuan..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary-gradient w-100 py-2.5 fw-semibold">
                    <i class="bi bi-check-circle-fill me-1"></i> Simpan & Konfirmasi Penugasan
                </button>
            </form>
        </div>
    </div>

    <!-- Right: Info Panel (DO Cargo details and Driver details) -->
    <div class="col-12 col-lg-5">
        <div class="d-flex flex-column gap-4">
            <!-- DO Details Panel -->
            <div class="erp-card p-4 d-none" id="info-do-card">
                <h6 class="fw-bold mb-3 text-success">
                    <i class="bi bi-box-seam me-2"></i>Rincian Muatan Cargo
                </h6>
                <div class="mb-3 bg-light-dark p-2.5 rounded border border-light-dark text-xs">
                    <div class="row mb-2">
                        <div class="col-6 text-muted">Asal Gudang:</div>
                        <div class="col-6 fw-bold text-end" id="info-do-origin">-</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-muted">Tujuan Kirim:</div>
                        <div class="col-6 fw-bold text-end text-truncate" id="info-do-dest">-</div>
                    </div>
                    <div class="row">
                        <div class="col-6 text-muted">Tanggal Buat:</div>
                        <div class="col-6 fw-bold text-end" id="info-do-date">-</div>
                    </div>
                </div>
                <div class="text-xs">
                    <span class="text-muted d-block mb-1">Daftar Barang:</span>
                    <ul class="list-group list-group-flush bg-transparent" id="info-do-items-list">
                        <!-- Loaded dynamically -->
                    </ul>
                </div>
            </div>

            <!-- Driver Details Panel -->
            <div class="erp-card p-4 d-none" id="info-driver-card">
                <h6 class="fw-bold mb-3 text-info">
                    <i class="bi bi-person-badge me-2"></i>Profil & Armada Driver
                </h6>
                <div class="text-xs">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-light-dark p-3 rounded border border-light-dark text-indigo">
                            <i class="bi bi-person-vcard" style="font-size: 1.8rem;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-sm text-primary" id="info-drv-name">-</div>
                            <div class="text-muted text-xs" id="info-drv-phone">-</div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-2 bg-light-dark p-2 rounded border border-light-dark text-center">
                        <div class="col-6 border-end border-secondary border-opacity-20">
                            <span class="text-muted text-xxs d-block">PLAT NOMOR</span>
                            <span class="badge bg-indigo mt-0.5 text-xs font-monospace" id="info-drv-plate">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted text-xxs d-block">TIPE KENDARAAN</span>
                            <strong class="text-secondary d-block mt-0.5" id="info-drv-class">-</strong>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between text-muted text-xxs">
                        <span>RATING KINERJA DRIVER:</span>
                        <span class="text-warning fw-bold text-xs"><i class="bi bi-star-fill me-0.5"></i><span id="info-drv-rating">4.8</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectDo = document.getElementById('select-do');
        const selectDriver = document.getElementById('select-driver');

        // Populate Dropdowns
        const dos = db.getData('deliveryOrders') || [];
        const drivers = db.getData('drivers') || [];
        const warehouses = db.getData('warehouses') || [];
        const products = db.getData('products') || [];

        // 1. Populate DOs dropdown
        // Show DOs that have NO driver assigned yet (driverId = null)
        const unassignedDos = dos.filter(d => !d.driverId);
        unassignedDos.forEach(d => {
            selectDo.innerHTML += `<option value="${d.id}">${d.doNumber} - ${d.destination.split(',')[0]}</option>`;
        });

        // 2. Populate Drivers dropdown
        // Show drivers that are Available
        const availableDrivers = drivers.filter(d => d.status === 'Available');
        availableDrivers.forEach(d => {
            selectDriver.innerHTML += `<option value="${d.id}">${d.name} (${d.vehicleClass})</option>`;
        });

        // Handle URL Parameters for shortcut assigning
        const urlParams = new URLSearchParams(window.location.search);
        const shortcutDoId = parseInt(urlParams.get('id'));
        
        if (shortcutDoId) {
            // Check if this DO is already assigned. If so, let user reassign
            const targetDo = dos.find(d => d.id === shortcutDoId);
            if (targetDo) {
                if (targetDo.driverId) {
                    const exists = Array.from(selectDo.options).some(opt => parseInt(opt.value) === shortcutDoId);
                    if (!exists) {
                        selectDo.innerHTML += `<option value="${targetDo.id}">${targetDo.doNumber} - ${targetDo.destination.split(',')[0]} (Ditugaskan: ${targetDo.driverId})</option>`;
                    }
                }
                selectDo.value = shortcutDoId;
                renderDoDetails(shortcutDoId);
            }
        }

        // Bind dropdown selection changes to render sidecards
        selectDo.addEventListener('change', (e) => {
            renderDoDetails(parseInt(e.target.value));
        });

        selectDriver.addEventListener('change', (e) => {
            renderDriverDetails(parseInt(e.target.value));
        });

        function renderDoDetails(id) {
            const targetDo = dos.find(d => d.id === id);
            if (!targetDo) return;

            const originWh = warehouses.find(w => w.id === targetDo.originId);

            document.getElementById('info-do-origin').innerText = originWh ? originWh.name : 'Unknown';
            document.getElementById('info-do-dest').innerText = targetDo.destination;
            document.getElementById('info-do-date').innerText = targetDo.date;

            const list = document.getElementById('info-do-items-list');
            list.innerHTML = '';

            targetDo.items.forEach(item => {
                const prod = products.find(p => p.sku === item.sku);
                const prodName = prod ? prod.name : 'Unknown Product';
                list.innerHTML += `
                    <li class="list-group-item bg-transparent text-secondary border-light-dark px-0 py-1.5 d-flex justify-content-between align-items-center">
                        <div>
                            <span>${prodName}</span><br>
                            <span class="text-xxs text-muted">SKU: ${item.sku}</span>
                        </div>
                        <span class="badge bg-indigo font-monospace">${item.quantity} Unit</span>
                    </li>
                `;
            });

            document.getElementById('info-do-card').classList.remove('d-none');
        }

        function renderDriverDetails(id) {
            const targetDrv = drivers.find(d => d.id === id);
            if (!targetDrv) return;

            document.getElementById('info-drv-name').innerText = targetDrv.name;
            document.getElementById('info-drv-phone').innerText = targetDrv.phone;
            document.getElementById('info-drv-plate').innerText = targetDrv.licensePlate;
            document.getElementById('info-drv-class').innerText = targetDrv.vehicleClass;
            document.getElementById('info-drv-rating').innerText = targetDrv.rating.toFixed(1);

            document.getElementById('info-driver-card').classList.remove('d-none');
        }

        // Handle submit penugasan
        document.getElementById('assign-driver-form').addEventListener('submit', (e) => {
            e.preventDefault();

            const doIdVal = parseInt(selectDo.value);
            const drvIdVal = parseInt(selectDriver.value);
            const notes = document.getElementById('dispatch-notes').value.trim() || 'Penugasan standar dispatcher logistik.';

            const targetDo = dos.find(d => d.id === doIdVal);
            const targetDrv = drivers.find(drv => drv.id === drvIdVal);

            if (targetDo && targetDrv) {
                db.updateItem('deliveryOrders', doIdVal, {
                    driverId: drvIdVal
                });

                db.updateItem('drivers', drvIdVal, {
                    status: 'On Delivery'
                });

                // Write Audit Log
                const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
                db.logAction(
                    activeUser.name, 
                    "Logistics Dispatcher", 
                    "Assign Driver", 
                    `Driver ${targetDrv.name} ditugaskan untuk mengantar DO ${targetDo.doNumber} (Tujuan: ${targetDo.destination.split(',')[0]})`,
                    "Success"
                );

                alert(`PENUGASAN BERHASIL. Driver ${targetDrv.name} ditugaskan untuk DO ${targetDo.doNumber}.`);
                
                // Redirect back to detail
                window.location.href = `{{ route('delivery.detail') }}?id=${targetDo.id}`;
            }
        });
    });
</script>
@endsection
