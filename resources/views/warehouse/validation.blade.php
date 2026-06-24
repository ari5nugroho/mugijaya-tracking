@extends('layouts.main')

@section('title', 'Validasi QC - CV Mugijaya Logistics ERP')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Validasi Barang (QC Validation)</h4>
        <p class="text-muted text-sm m-0">Lakukan pengecekan fisik barang, verifikasi segel, dan setujui kesesuaian Delivery Order.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Left: List of Pending QC -->
    <div class="col-12 col-lg-7">
        <div class="erp-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-hourglass-split text-warning me-2"></i>Antrean Validasi QC Gudang</h6>
            <div class="table-responsive">
                <table class="table align-middle text-sm mb-0">
                    <thead>
                        <tr>
                            <th>Nomor DO</th>
                            <th>Asal Gudang</th>
                            <th>Tujuan</th>
                            <th>Items</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="qc-pending-tbody">
                        <!-- Loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Barcode Scanner Mock & QC Checklist Console -->
    <div class="col-12 col-lg-5">
        <!-- Barcode Scanner Mock -->
        <div class="erp-card p-4 mb-4 text-center border-dashed border-light-dark bg-light-dark">
            <div class="d-inline-flex align-items-center justify-content-center bg-secondary p-3 rounded mb-3 border border-light-dark">
                <i class="bi bi-qr-code-scan text-indigo" style="font-size: 2.5rem;"></i>
            </div>
            <h6 class="fw-bold mb-1">Mock Scanner Barcode / QR</h6>
            <p class="text-muted text-xs mb-3">Simulasikan pemindaian label barcode pada kotak palet barang.</p>
            
            <div class="d-flex gap-2 justify-content-center">
                <input type="text" class="form-control form-control-sm text-center" id="scan-input" placeholder="Masukkan / scan nomor DO..." style="max-width: 220px;">
                <button class="btn btn-sm btn-primary-gradient" onclick="simulateScan()"><i class="bi bi-upc-scan"></i> Scan</button>
            </div>
        </div>

        <!-- QC Checklist Console (Hidden initially until DO selected) -->
        <div class="erp-card p-4 d-none" id="qc-console-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-card-checklist text-indigo me-2"></i>Konsol Verifikasi QC</h6>
                <span class="badge bg-warning text-dark text-xs" id="console-do-num">DO-2026-0003</span>
            </div>

            <div class="mb-3 bg-light-dark p-2.5 rounded border border-light-dark">
                <div class="text-xs text-muted mb-1">Daftar Barang untuk Dicek:</div>
                <ul class="list-unstyled m-0 text-xs text-secondary" id="console-items-list">
                    <!-- Items listed here -->
                </ul>
            </div>

            <form id="qc-checklist-form">
                <input type="hidden" id="console-do-id">
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="check-seal" required>
                    <label class="form-check-label text-xs fw-semibold" for="check-seal">
                        Segel kemasan luar utuh & bersih
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="check-qty" required>
                    <label class="form-check-label text-xs fw-semibold" for="check-qty">
                        Jumlah barang fisik sesuai data DO
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="check-physical" required>
                    <label class="form-check-label text-xs fw-semibold" for="check-physical">
                        Barang bebas cacat/kerusakan fisik
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="check-doc" required>
                    <label class="form-check-label text-xs fw-semibold" for="check-doc">
                        Dokumen surat jalan / manifes sesuai
                    </label>
                </div>

                <div class="mb-3">
                    <label for="qc-notes" class="form-label text-xs">Catatan Pemeriksaan (Optional)</label>
                    <input type="text" class="form-control form-control-sm" id="qc-notes" placeholder="Contoh: Segel aman, kuantitas pas.">
                </div>

                <button type="submit" class="btn btn-sm btn-primary-gradient w-100 py-2">
                    <i class="bi bi-check-circle-fill me-1"></i> Validasi & Setujui Pengiriman
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function renderQcQueue() {
        const dos = db.getData('deliveryOrders');
        const warehouses = db.getData('warehouses');
        const products = db.getData('products');
        const tbody = document.getElementById('qc-pending-tbody');
        
        tbody.innerHTML = '';
        
        // Filter DOs with validationStatus !== 'Validated' or status === 'Pending Validation'
        const pendingDos = dos.filter(d => d.validationStatus !== 'Validated');

        pendingDos.forEach(d => {
            const wh = warehouses.find(w => w.id === d.originId);
            
            // Construct a small item summary string
            const itemsSummary = d.items.map(it => {
                const p = products.find(prod => prod.sku === it.sku);
                return `${it.quantity} Pcs ${p ? p.name : it.sku}`;
            }).join(', ');

            tbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-primary">${d.doNumber}</td>
                    <td><span class="badge bg-light-dark text-secondary">${wh ? wh.code : 'WH'}</span></td>
                    <td class="text-truncate" style="max-width: 140px;" title="${d.destination}">${d.destination}</td>
                    <td class="text-xs text-muted text-truncate" style="max-width: 180px;">${itemsSummary}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-primary-gradient py-1 text-xs" onclick="selectDoForQc(${d.id})"><i class="bi bi-clipboard-check"></i> Proses</button>
                    </td>
                </tr>
            `;
        });

        if (pendingDos.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-check-circle-fill text-success d-block mb-2 fs-3"></i>Semua antrean DO telah tervalidasi.</td></tr>`;
        }
    }

    // Open QC Console for DO
    function selectDoForQc(id) {
        const dos = db.getData('deliveryOrders');
        const products = db.getData('products');
        const d = dos.find(doObj => doObj.id === id);

        if (d) {
            document.getElementById('console-do-id').value = d.id;
            document.getElementById('console-do-num').innerText = d.doNumber;
            
            // Load item details
            const list = document.getElementById('console-items-list');
            list.innerHTML = '';
            d.items.forEach(it => {
                const p = products.find(prod => prod.sku === it.sku);
                list.innerHTML += `<li><i class="bi bi-box me-1.5 text-indigo"></i>${it.quantity}x ${p ? p.name : it.sku} (SKU: ${it.sku})</li>`;
            });

            // Reset checks
            document.getElementById('check-seal').checked = false;
            document.getElementById('check-qty').checked = false;
            document.getElementById('check-physical').checked = false;
            document.getElementById('check-doc').checked = false;
            document.getElementById('qc-notes').value = '';

            // Show console card
            document.getElementById('qc-console-card').classList.remove('d-none');
        }
    }

    // Barcode input simulator
    function simulateScan() {
        const inputVal = document.getElementById('scan-input').value.trim();
        if (!inputVal) {
            alert('Silakan masukkan nomor DO yang valid (Contoh: DO-2026-0003)');
            return;
        }

        const dos = db.getData('deliveryOrders');
        const foundDo = dos.find(d => d.doNumber.toLowerCase() === inputVal.toLowerCase());

        if (foundDo) {
            if (foundDo.validationStatus === 'Validated') {
                alert(`DO ${foundDo.doNumber} sudah tervalidasi QC sebelumnya.`);
            } else {
                selectDoForQc(foundDo.id);
            }
        } else {
            alert(`DO dengan nomor ${inputVal} tidak ditemukan dalam database.`);
        }
    }

    // Handle Checklist Submission
    document.getElementById('qc-checklist-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const doId = parseInt(document.getElementById('console-do-id').value);
        const notes = document.getElementById('qc-notes').value.trim() || 'Segel utuh, lolos QC.';

        const dos = db.getData('deliveryOrders');
        const d = dos.find(doObj => doObj.id === doId);

        if (d) {
            // Determine next status
            let nextStatus = d.status;
            if (d.driverId) {
                nextStatus = "In Transit"; // Auto dispatched for delivery simulation
            } else {
                nextStatus = "Prepared"; // Prepared, waiting driver assignment
            }

            db.updateItem('deliveryOrders', doId, {
                validationStatus: "Validated",
                status: nextStatus,
                validationNotes: notes
            });

            // Write audit logs
            const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
            db.logAction(activeUser.name, "QC Validation", "Validation Approval", `QC berhasil memvalidasi ${d.doNumber} status diubah ke ${nextStatus}`, "Success");

            // If in transit, update driver status
            if (d.driverId) {
                db.updateItem('drivers', d.driverId, { status: "On Delivery" });
            }

            alert(`Validasi QC untuk DO ${d.doNumber} BERHASIL. Pengiriman di-update ke status: ${nextStatus}.`);

            // Hide Console and Refresh Queue
            document.getElementById('qc-console-card').classList.add('d-none');
            document.getElementById('scan-input').value = '';
            renderQcQueue();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        renderQcQueue();
    });
</script>
@endsection
