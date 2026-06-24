@extends('layouts.main')

@section('title', 'Detail Delivery Order - CV Mugijaya Logistics ERP')

@section('content')
<!-- Header with Action Buttons -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('delivery.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <div>
            <h4 class="fw-bold mb-1 d-inline-block align-middle" id="head-do-number">DO-2026-XXXX</h4>
            <span class="badge ms-2" id="badge-do-status">Draft</span>
            <span class="badge ms-1" id="badge-qc-status">Pending QC</span>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2" id="action-buttons-group">
        <!-- Populated dynamically based on DO status -->
    </div>
</div>

<!-- Info Grid -->
<div class="row g-4 mb-4">
    <!-- Column 1: Warehouse Origin -->
    <div class="col-12 col-md-4">
        <div class="erp-card p-4 h-100 card-accent-border">
            <h6 class="fw-bold mb-3 text-indigo">
                <i class="bi bi-house-door me-2"></i>Asal Gudang
            </h6>
            <div class="mb-2">
                <span class="text-muted text-xs d-block">Nama Gudang:</span>
                <span class="fw-semibold text-primary text-sm" id="wh-name">-</span>
            </div>
            <div class="mb-2">
                <span class="text-muted text-xs d-block">Kode Gudang:</span>
                <span class="badge bg-light-dark text-secondary" id="wh-code">-</span>
            </div>
            <div class="mb-2">
                <span class="text-muted text-xs d-block">Kepala Gudang:</span>
                <span class="text-secondary text-sm" id="wh-manager">-</span>
            </div>
            <div>
                <span class="text-muted text-xs d-block">Alamat Asal:</span>
                <span class="text-muted text-xs" id="wh-address">-</span>
            </div>
        </div>
    </div>

    <!-- Column 2: Customer Destination -->
    <div class="col-12 col-md-4">
        <div class="erp-card p-4 h-100">
            <h6 class="fw-bold mb-3 text-success">
                <i class="bi bi-geo-alt me-2"></i>Tujuan Pengiriman
            </h6>
            <div class="mb-2">
                <span class="text-muted text-xs d-block">Penerima / Instansi:</span>
                <span class="fw-semibold text-primary text-sm" id="cust-name">-</span>
            </div>
            <div class="mb-2">
                <span class="text-muted text-xs d-block">Tanggal Pengiriman:</span>
                <span class="text-secondary text-sm" id="do-date">-</span>
            </div>
            <div>
                <span class="text-muted text-xs d-block">Alamat Lengkap:</span>
                <span class="text-secondary text-xs" id="cust-address">-</span>
            </div>
        </div>
    </div>

    <!-- Column 3: Driver & Vehicle -->
    <div class="col-12 col-md-4">
        <div class="erp-card p-4 h-100">
            <h6 class="fw-bold mb-3 text-info">
                <i class="bi bi-truck me-2"></i>Driver & Armada
            </h6>
            <div id="driver-assigned-section" class="d-none">
                <div class="mb-2">
                    <span class="text-muted text-xs d-block">Nama Driver:</span>
                    <span class="fw-semibold text-primary text-sm" id="drv-name">-</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted text-xs d-block">No. Plat Kendaraan:</span>
                    <span class="badge bg-light-dark text-secondary" id="drv-plate">-</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted text-xs d-block">Kelas Armada:</span>
                    <span class="text-secondary text-sm" id="drv-vehicle">-</span>
                </div>
                <div>
                    <span class="text-muted text-xs d-block">No. Telp:</span>
                    <span class="text-secondary text-xs" id="drv-phone">-</span>
                </div>
            </div>
            <div id="driver-unassigned-section" class="text-center py-4">
                <i class="bi bi-person-x text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted text-xs mb-3">Belum ada driver yang ditugaskan untuk DO ini.</p>
                <button class="btn btn-sm btn-primary-gradient" id="btn-assign-driver-quick">
                    <i class="bi bi-person-plus-fill"></i> Tugaskan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="erp-card p-4 mb-4">
    <h6 class="fw-bold mb-3">
        <i class="bi bi-box-seam me-2 text-indigo"></i>Daftar Barang Bawaan
    </h6>
    <div class="table-responsive">
        <table class="table align-middle text-sm mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>SKU</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Bobot Per Unit</th>
                    <th class="text-center">Kuantitas</th>
                    <th class="text-end">Total Bobot</th>
                </tr>
            </thead>
            <tbody id="items-tbody">
                <!-- Populated dynamically -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end fw-bold">Total Muatan:</td>
                    <td class="text-center fw-bold text-primary" id="total-qty">0 Unit</td>
                    <td class="text-end fw-bold text-primary" id="total-weight">0.0 Kg</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Incident logs if exist -->
<div class="erp-card p-4 mb-4 d-none" id="incidents-card">
    <h6 class="fw-bold mb-3 text-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>Laporan Insiden Terkait
    </h6>
    <div class="table-responsive">
        <table class="table text-sm mb-0 align-middle">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pelapor</th>
                    <th>Tipe Insiden</th>
                    <th>Tingkat Keparahan</th>
                    <th>Detail Kejadian</th>
                    <th>Status Penyelesaian</th>
                </tr>
            </thead>
            <tbody id="incidents-tbody">
                <!-- Populated dynamically -->
            </tbody>
        </table>
    </div>
</div>

<!-- QC Validation Notes -->
<div class="erp-card p-4 mb-4 d-none" id="qc-notes-card">
    <h6 class="fw-bold mb-2">
        <i class="bi bi-patch-check-fill text-success me-2"></i>Catatan Validasi QC
    </h6>
    <div class="bg-light-dark p-3 rounded border border-light-dark">
        <p class="m-0 text-sm text-secondary" id="qc-notes-content">-</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const doId = parseInt(urlParams.get('id'));

        // Get DOs from database
        const dos = db.getData('deliveryOrders');
        let order = dos.find(d => d.id === doId);

        // Default to first DO if not found/no parameter
        if (!order) {
            if (dos.length > 0) {
                order = dos[0];
            } else {
                alert('Tidak ada data Delivery Order!');
                window.location.href = "{{ route('delivery.index') }}";
                return;
            }
        }

        // Fill header info
        document.getElementById('head-do-number').innerText = order.doNumber;

        // Set DO Status badge
        const statusEl = document.getElementById('badge-do-status');
        statusEl.innerText = order.status;
        statusEl.className = 'badge text-xs ms-2 ';
        if (order.status === 'Delivered') statusEl.classList.add('bg-success');
        else if (order.status === 'In Transit') statusEl.classList.add('bg-info');
        else if (order.status === 'Pending Validation') statusEl.classList.add('bg-warning', 'text-dark');
        else if (order.status === 'Prepared') statusEl.classList.add('bg-primary');
        else statusEl.classList.add('bg-secondary');

        // Set QC badge
        const qcEl = document.getElementById('badge-qc-status');
        qcEl.innerText = order.validationStatus;
        qcEl.className = 'badge text-xs ms-1 ';
        if (order.validationStatus === 'Validated') {
            qcEl.classList.add('bg-success');
            // Show QC Notes
            document.getElementById('qc-notes-card').classList.remove('d-none');
            document.getElementById('qc-notes-content').innerText = order.validationNotes || 'Sesuai dengan kriteria pemeriksaan QC Gudang.';
        } else {
            qcEl.classList.add('bg-warning', 'text-dark');
        }

        // Fetch Warehouse
        const warehouses = db.getData('warehouses');
        const wh = warehouses.find(w => w.id === order.originId);
        if (wh) {
            document.getElementById('wh-name').innerText = wh.name;
            document.getElementById('wh-code').innerText = wh.code;
            document.getElementById('wh-manager').innerText = wh.manager;
            document.getElementById('wh-address').innerText = wh.address;
        }

        // Customer Details (Mock customer name based on DO format)
        document.getElementById('cust-name').innerText = order.destination.split(',')[0] || 'PT. Mitra Sejahtera';
        document.getElementById('cust-address').innerText = order.destination;
        document.getElementById('do-date').innerText = order.date;

        // Driver Details
        const drivers = db.getData('drivers');
        const driver = drivers.find(d => d.id === order.driverId);
        if (driver) {
            document.getElementById('driver-assigned-section').classList.remove('d-none');
            document.getElementById('driver-unassigned-section').classList.add('d-none');

            document.getElementById('drv-name').innerText = driver.name;
            document.getElementById('drv-plate').innerText = driver.licensePlate;
            document.getElementById('drv-vehicle').innerText = driver.vehicleClass;
            document.getElementById('drv-phone').innerText = driver.phone;
        } else {
            document.getElementById('driver-assigned-section').classList.add('d-none');
            document.getElementById('driver-unassigned-section').classList.remove('d-none');

            document.getElementById('btn-assign-driver-quick').addEventListener('click', () => {
                window.location.href = `{{ route('delivery.assign-driver') }}?id=${order.id}`;
            });
        }

        // Items table population
        const products = db.getData('products');
        const tbody = document.getElementById('items-tbody');
        tbody.innerHTML = '';

        let totalQty = 0;
        let totalW = 0.0;

        order.items.forEach((item, index) => {
            const prod = products.find(p => p.sku === item.sku);
            const prodName = prod ? prod.name : 'Unknown Product';
            const category = prod ? prod.category : 'General';
            const weight = prod ? prod.weight : 0.0;
            const totalRowWeight = weight * item.quantity;

            totalQty += item.quantity;
            totalW += totalRowWeight;

            tbody.innerHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td class="fw-bold text-indigo">${item.sku}</td>
                    <td class="fw-semibold text-primary">${prodName}</td>
                    <td><span class="badge bg-light-dark text-secondary">${category}</span></td>
                    <td>${weight} Kg</td>
                    <td class="text-center fw-bold">${item.quantity} Unit</td>
                    <td class="text-end text-primary">${totalRowWeight.toFixed(2)} Kg</td>
                </tr>
            `;
        });

        document.getElementById('total-qty').innerText = `${totalQty} Unit`;
        document.getElementById('total-weight').innerText = `${totalW.toFixed(2)} Kg`;

        // Incident reports mapping
        const incidents = db.getData('incidents') || [];
        const relatedIncidents = incidents.filter(i => i.doNumber === order.doNumber);
        if (relatedIncidents.length > 0) {
            document.getElementById('incidents-card').classList.remove('d-none');
            const incTbody = document.getElementById('incidents-tbody');
            incTbody.innerHTML = '';

            relatedIncidents.forEach(inc => {
                let severityClass = 'bg-info';
                if (inc.severity === 'High') severityClass = 'bg-danger';
                else if (inc.severity === 'Medium') severityClass = 'bg-warning text-dark';

                incTbody.innerHTML += `
                    <tr>
                        <td>${inc.date}</td>
                        <td class="fw-semibold text-primary">${inc.reporter}</td>
                        <td><span class="badge bg-light-dark text-danger">${inc.type}</span></td>
                        <td><span class="badge ${severityClass} text-xs">${inc.severity}</span></td>
                        <td class="text-xs text-muted" style="max-width:250px;">
                            <strong>Deskripsi:</strong> ${inc.description}<br>
                            <strong class="text-success">Resolusi:</strong> ${inc.resolution || '-'}
                        </td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-30 text-xs">${inc.status}</span>
                        </td>
                    </tr>
                `;
            });
        }

        // Action Buttons Generator based on Status
        const btnGroup = document.getElementById('action-buttons-group');

        // Standard action buttons
        let buttonsHtml = '';

        // 1. QC Validation button
        if (order.validationStatus !== 'Validated') {
            buttonsHtml += `
                <a href="{{ route('warehouse.validation') }}?id=${order.id}" class="btn btn-sm btn-warning text-dark fw-semibold">
                    <i class="bi bi-patch-check-fill me-1"></i> Jalankan QC Check
                </a>
            `;
        }

        // 2. Assign Driver button (if not assigned)
        if (!order.driverId) {
            buttonsHtml += `
                <a href="{{ route('delivery.assign-driver') }}?id=${order.id}" class="btn btn-sm btn-primary-gradient">
                    <i class="bi bi-person-plus-fill me-1"></i> Assign Driver
                </a>
            `;
        }

        // 3. Loading Barang (if validated, assigned driver, and not loaded yet / status is Prepared)
        if (order.validationStatus === 'Validated' && order.driverId && order.status === 'Prepared') {
            buttonsHtml += `
                <a href="{{ route('warehouse.loading') }}?id=${order.id}" class="btn btn-sm btn-info text-dark fw-semibold">
                    <i class="bi bi-box-arrow-up me-1"></i> Proses Loading Barang
                </a>
            `;
        }

        // 4. Print Surat Jalan (if validated & driver assigned)
        if (order.validationStatus === 'Validated' && order.driverId) {
            buttonsHtml += `
                <a href="{{ route('delivery.surat-jalan') }}?id=${order.id}" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-printer-fill me-1"></i> Print Surat Jalan
                </a>
            `;
        }

        // 5. Tracking GPS (if In Transit)
        if (order.status === 'In Transit') {
            buttonsHtml += `
                <a href="{{ route('tracking.index') }}" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-geo-alt-fill me-1"></i> Lacak GPS
                </a>
                <button class="btn btn-sm btn-success fw-semibold" id="btn-mark-delivered">
                    <i class="bi bi-check-circle-fill me-1"></i> Selesaikan Pengiriman (POD)
                </button>
                <a href="{{ route('delivery.incident') }}?doNumber=${order.doNumber}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Laporkan Insiden
                </a>
            `;
        }

        btnGroup.innerHTML = buttonsHtml;

        // Bind Mark as Delivered
        const devBtn = document.getElementById('btn-mark-delivered');
        if (devBtn) {
            devBtn.addEventListener('click', () => {
                // Redirect to proof of delivery so they can do signature + photo
                window.location.href = `{{ route('pod.index') }}?id=${order.id}`;
            });
        }
    });
</script>
@endsection
