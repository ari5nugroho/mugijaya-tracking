@extends('layouts.main')

@section('title', 'Delivery Order - CV Mugijaya Logistics ERP')

@section('styles')
<!-- DataTables BS5 CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Delivery Order (DO)</h4>
        <p class="text-muted text-sm m-0">Buat, pantau, dan tugaskan pengiriman barang ke berbagai lokasi tujuan.</p>
    </div>
    <button class="btn btn-primary-gradient d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addDoModal">
        <i class="bi bi-file-earmark-plus-fill"></i> Buat DO Baru
    </button>
</div>

<div class="erp-card p-4">
    <div class="table-responsive">
        <table id="do-table" class="table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>Nomor DO</th>
                    <th>Asal Gudang</th>
                    <th>Alamat Tujuan</th>
                    <th>Driver / Armada</th>
                    <th>Tanggal Buat</th>
                    <th>Status Validasi</th>
                    <th>Status Kirim</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via script -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modals Section -->
<!-- Add DO Modal -->
<div class="modal fade" id="addDoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-plus-fill text-indigo me-2"></i>Buat Delivery Order Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-do-form">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="add-do-warehouse" class="form-label">Asal Gudang Pengirim</label>
                            <select class="form-select" id="add-do-warehouse" required>
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="add-do-driver" class="form-label">Tugaskan Driver</label>
                            <select class="form-select" id="add-do-driver">
                                <option value="">-- Tunda Penugasan (Unassigned) --</option>
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add-do-dest" class="form-label">Alamat & Nama Penerima (Tujuan)</label>
                        <input type="text" class="form-control" id="add-do-dest" required placeholder="Contoh: Toko Berkah, Jl. Gajahmada No. 88, Semarang">
                    </div>

                    <hr class="border-light-dark my-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-box-seam me-2 text-indigo"></i>Daftar Barang & Kuantitas</h6>
                    
                    <div id="do-items-container" class="mb-3">
                        <!-- Rows will be added dynamically -->
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-outline-info" id="btn-add-item-row">
                        <i class="bi bi-plus-circle-fill"></i> Tambah Baris Barang
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">Terbitkan DO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View DO Details Modal -->
<div class="modal fade" id="viewDoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="view-do-number">DO-2026-XXXX</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-sm">
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-muted text-xs">Asal Gudang:</div>
                        <div class="fw-bold" id="view-do-origin">Gudang Utama Semarang</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted text-xs">Tanggal Buat:</div>
                        <div class="fw-bold" id="view-do-date">2026-06-15</div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-muted text-xs">Alamat Tujuan:</div>
                    <div class="fw-bold" id="view-do-dest">Toko Elektronik Makmur, Jl. Pemuda Semarang</div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-muted text-xs">Driver:</div>
                        <div class="fw-bold" id="view-do-driver">Rian Hidayat (H 1234 AB)</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted text-xs">Status Kirim:</div>
                        <span class="badge bg-indigo text-xs mt-1" id="view-do-status">In Transit</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-muted text-xs">Validasi QC:</div>
                        <span class="badge bg-success text-xs mt-1" id="view-do-qc">Validated</span>
                    </div>
                    <div class="col-6">
                        <div class="text-muted text-xs">Catatan Validasi:</div>
                        <div class="text-secondary text-xs mt-1" id="view-do-notes">-</div>
                    </div>
                </div>

                <hr class="border-light-dark">
                <h6 class="fw-bold mb-2">Item Barang</h6>
                <div class="bg-light-dark rounded p-2.5 border border-light-dark">
                    <ul class="list-group list-group-flush bg-transparent m-0" id="view-do-items-list">
                        <!-- Items populated here -->
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery (needed for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    let table;

    function renderDoTable() {
        const dos = db.getData('deliveryOrders');
        const warehouses = db.getData('warehouses');
        const drivers = db.getData('drivers');

        if ($.fn.DataTable.isDataTable('#do-table')) {
            table.destroy();
        }

        const tbody = document.querySelector('#do-table tbody');
        tbody.innerHTML = '';

        dos.forEach(d => {
            const wh = warehouses.find(w => w.id === d.originId);
            const driverObj = drivers.find(drv => drv.id === d.driverId);
            const driverName = driverObj ? `${driverObj.name} (${driverObj.licensePlate})` : '<span class="text-muted">Belum Ditunjuk</span>';

            let validateBadge = 'bg-secondary';
            if (d.validationStatus === 'Validated') validateBadge = 'bg-success text-success bg-opacity-10 border border-success border-opacity-30';
            else if (d.validationStatus === 'Pending QC') validateBadge = 'bg-warning text-warning bg-opacity-10 border border-warning border-opacity-30';

            let statusBadge = 'bg-secondary';
            if (d.status === 'Delivered') statusBadge = 'bg-success text-success bg-opacity-10 border border-success border-opacity-30';
            else if (d.status === 'In Transit') statusBadge = 'bg-info text-info bg-opacity-10 border border-info border-opacity-30';
            else if (d.status === 'Pending Validation') statusBadge = 'bg-warning text-warning bg-opacity-10 border border-warning border-opacity-30';
            else if (d.status === 'Prepared') statusBadge = 'bg-primary text-primary bg-opacity-10 border border-primary border-opacity-30';

            tbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-primary">${d.doNumber}</td>
                    <td>${wh ? wh.name : 'Unknown Warehouse'}</td>
                    <td class="text-truncate" style="max-width: 200px;" title="${d.destination}">${d.destination}</td>
                    <td class="text-xs fw-semibold">${driverName}</td>
                    <td>${d.date}</td>
                    <td><span class="badge ${validateBadge} text-xs">${d.validationStatus}</span></td>
                    <td><span class="badge ${statusBadge} text-xs">${d.status}</span></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info me-1" href="{{ route('delivery.detail') }}?id=${d.id}" title="Lihat Detail"><i class="bi bi-eye"></i></a>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteDo(${d.id})" title="Batalkan/Hapus"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        });

        table = $('#do-table').DataTable({
            responsive: true,
            order: [[4, 'desc']], // Order by date desc
            language: {
                search: "Cari DO:",
                lengthMenu: "Tampil _MENU_",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ DO",
                paginate: {
                    next: "<i class='bi bi-chevron-right'></i>",
                    previous: "<i class='bi bi-chevron-left'></i>"
                }
            }
        });
    }

    // Add a line item row in create DO modal
    function addItemRow() {
        const container = document.getElementById('do-items-container');
        const products = db.getData('products');
        
        const rowIndex = container.children.length;
        const div = document.createElement('div');
        div.className = 'row g-2 mb-2 item-row';
        
        let optionsHtml = '<option value="" disabled selected>-- Pilih Produk --</option>';
        products.forEach(p => {
            optionsHtml += `<option value="${p.sku}">${p.sku} - ${p.name}</option>`;
        });

        div.innerHTML = `
            <div class="col-8 col-sm-9">
                <select class="form-select select-item-sku" required>
                    ${optionsHtml}
                </select>
            </div>
            <div class="col-3 col-sm-2">
                <input type="number" class="form-control input-item-qty" placeholder="Qty" required min="1">
            </div>
            <div class="col-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.row').remove()"><i class="bi bi-x-lg"></i></button>
            </div>
        `;
        container.appendChild(div);
    }

    // Setup drop-down choices in new DO modal
    function populateSelectors() {
        const warehouses = db.getData('warehouses');
        const drivers = db.getData('drivers');

        const wSelect = document.getElementById('add-do-warehouse');
        wSelect.innerHTML = '';
        warehouses.forEach(w => {
            wSelect.innerHTML += `<option value="${w.id}">${w.code} - ${w.name}</option>`;
        });

        const dSelect = document.getElementById('add-do-driver');
        // Reset to default option
        dSelect.innerHTML = '<option value="">-- Tunda Penugasan (Unassigned) --</option>';
        // Add available drivers
        drivers.forEach(d => {
            if (d.status === 'Available') {
                dSelect.innerHTML += `<option value="${d.id}">${d.name} (${d.vehicleClass})</option>`;
            }
        });
    }

    // Form Submit
    document.getElementById('add-do-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const rows = document.querySelectorAll('.item-row');
        if (rows.length === 0) {
            alert('Paling sedikit harus menambahkan 1 baris produk!');
            return;
        }

        const itemsArray = [];
        rows.forEach(r => {
            const sku = r.querySelector('.select-item-sku').value;
            const qty = parseInt(r.querySelector('.input-item-qty').value);
            if (sku && qty) {
                itemsArray.push({ sku, quantity: qty });
            }
        });

        const rawDriver = document.getElementById('add-do-driver').value;
        const driverId = rawDriver ? parseInt(rawDriver) : null;
        const originId = parseInt(document.getElementById('add-do-warehouse').value);

        // Generate DO Number sequence
        const dos = db.getData('deliveryOrders');
        const seq = dos.length + 1;
        const formattedSeq = String(seq).padStart(4, '0');
        const doNum = `DO-2026-${formattedSeq}`;

        // A DO starts as: "Pending Validation" or "Prepared"
        const newDo = {
            doNumber: doNum,
            originId: originId,
            destination: document.getElementById('add-do-dest').value.trim(),
            driverId: driverId,
            status: "Pending Validation", // Default ERP workflow
            date: new Date().toISOString().split('T')[0],
            items: itemsArray,
            validationStatus: "Pending QC",
            validationNotes: "",
            pod: null
        };

        db.insertItem('deliveryOrders', newDo);

        // If driver assigned, change driver status
        if (driverId) {
            db.updateItem('drivers', driverId, { status: "On Delivery" }); // Driver is now busy
        }

        // Audit log
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        db.logAction(activeUser.name, "Delivery Order", "Create DO", `Menerbitkan DO baru ${newDo.doNumber} menuju ${newDo.destination}`, "Success");

        bootstrap.Modal.getInstance(document.getElementById('addDoModal')).hide();
        document.getElementById('add-do-form').reset();
        document.getElementById('do-items-container').innerHTML = ''; // reset items
        
        renderDoTable();
    });

    // Delete DO
    function deleteDo(id) {
        if (confirm('Apakah Anda yakin ingin membatalkan DO ini?')) {
            const dos = db.getData('deliveryOrders');
            const d = dos.find(doObj => doObj.id === id);

            // If DO driver was busy, reset them to available
            if (d && d.driverId) {
                db.updateItem('drivers', d.driverId, { status: "Available" });
            }

            db.deleteItem('deliveryOrders', id);

            // Audit log
            const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
            db.logAction(activeUser.name, "Delivery Order", "Cancel DO", `Membatalkan DO: ${d ? d.doNumber : 'Unknown'}`, "Success");

            renderDoTable();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('btn-add-item-row').addEventListener('click', addItemRow);
        
        populateSelectors();
        renderDoTable();

        // Default add 1 item row in modal
        addItemRow();

        // Check URL parameters for shortcut action
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'new') {
            const modal = new bootstrap.Modal(document.getElementById('addDoModal'));
            modal.show();
        }
    });
</script>
@endsection
