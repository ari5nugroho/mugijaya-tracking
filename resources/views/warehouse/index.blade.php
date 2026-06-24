@extends('layouts.main')

@section('title', 'Warehouse Management - CV Mugijaya Logistics ERP')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Warehouse Management</h4>
        <p class="text-muted text-sm m-0">Kelola informasi gudang utama, cabang, kapasitas, serta kepala gudang.</p>
    </div>
    <button class="btn btn-primary-gradient d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
        <i class="bi bi-house-add-fill"></i> Tambah Gudang Baru
    </button>
</div>

<!-- Warehouse Grid -->
<div class="row g-4" id="warehouse-grid">
    <!-- Dynamically populated -->
</div>

<!-- Modals Section -->
<!-- Add Warehouse Modal -->
<div class="modal fade" id="addWarehouseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-house-add-fill text-indigo me-2"></i>Tambah Gudang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-warehouse-form">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="add-code" class="form-label">Kode Gudang</label>
                            <input type="text" class="form-control" id="add-code" required placeholder="Contoh: WH-BDG-04">
                        </div>
                        <div class="col-md-6">
                            <label for="add-name" class="form-label">Nama Gudang</label>
                            <input type="text" class="form-control" id="add-name" required placeholder="Contoh: Gudang Bandung">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="add-manager" class="form-label">Kepala Gudang (Manager)</label>
                            <input type="text" class="form-control" id="add-manager" required placeholder="Contoh: Haryanto">
                        </div>
                        <div class="col-md-6">
                            <label for="add-capacity" class="form-label">Kapasitas (m³)</label>
                            <input type="number" class="form-control" id="add-capacity" required placeholder="Contoh: 20000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add-address" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="add-address" rows="3" required placeholder="Jl. Industri Raya No. 4..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">Simpan Gudang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Warehouse Modal -->
<div class="modal fade" id="editWarehouseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-indigo me-2"></i>Edit Informasi Gudang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-warehouse-form">
                <input type="hidden" id="edit-id">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit-code" class="form-label">Kode Gudang</label>
                            <input type="text" class="form-control" id="edit-code" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-name" class="form-label">Nama Gudang</label>
                            <input type="text" class="form-control" id="edit-name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit-manager" class="form-label">Kepala Gudang (Manager)</label>
                            <input type="text" class="form-control" id="edit-manager" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-capacity" class="form-label">Kapasitas (m³)</label>
                            <input type="number" class="form-control" id="edit-capacity" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-address" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="edit-address" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function renderWarehouses() {
        const warehouses = db.getData('warehouses');
        const stocks = db.getData('stocks');
        const grid = document.getElementById('warehouse-grid');
        grid.innerHTML = '';

        warehouses.forEach(wh => {
            // Count item species in this warehouse
            const whStocks = stocks.filter(s => s.warehouseId === wh.id);
            const distinctItems = whStocks.length;
            const totalQuantity = whStocks.reduce((sum, s) => sum + s.stockCurrent, 0);

            const percent = Math.round((wh.capacityUsed / wh.capacity) * 100);
            let progressColorClass = 'bg-success';
            let alertBadge = '';
            if (percent > 85) {
                progressColorClass = 'bg-danger';
                alertBadge = '<span class="badge bg-danger text-xs ms-2"><i class="bi bi-exclamation-triangle-fill"></i> Penuh</span>';
            } else if (percent > 65) {
                progressColorClass = 'bg-warning';
                alertBadge = '<span class="badge bg-warning text-dark text-xs ms-2">Padat</span>';
            }

            grid.innerHTML += `
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="erp-card h-100 d-flex flex-column justify-content-between p-4">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge bg-indigo text-xs fw-bold">${wh.code}</span>
                                <div class="dropdown">
                                    <button class="btn btn-link text-secondary p-0" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-light-dark bg-secondary">
                                        <li><a class="dropdown-item" href="#" onclick="openEditModal(${wh.id})"><i class="bi bi-pencil me-2"></i>Edit Gudang</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteWarehouse(${wh.id})"><i class="bi bi-trash me-2"></i>Hapus</a></li>
                                    </ul>
                                </div>
                            </div>
                            <h5 class="fw-bold mb-1 text-primary d-flex align-items-center">${wh.name} ${alertBadge}</h5>
                            <p class="text-xs text-muted mb-3"><i class="bi bi-geo-alt-fill me-1"></i>${wh.address}</p>
                            
                            <div class="row g-2 mb-4 bg-light-dark p-2.5 rounded border border-light-dark">
                                <div class="col-6 text-center border-end border-light-dark">
                                    <div class="text-xs text-muted">Model Produk</div>
                                    <div class="fw-bold text-lg text-indigo">${distinctItems}</div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="text-xs text-muted">Total Stok</div>
                                    <div class="fw-bold text-lg text-success">${totalQuantity.toLocaleString()} Pcs</div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between text-xs mb-1.5">
                                <span class="text-secondary fw-semibold"><i class="bi bi-person-fill text-muted me-1"></i>Manager: ${wh.manager}</span>
                                <span class="text-muted fw-bold">${wh.capacityUsed.toLocaleString()} / ${wh.capacity.toLocaleString()} m³ (${percent}%)</span>
                            </div>
                            <div class="progress capacity-progress mb-1">
                                <div class="progress-bar ${progressColorClass}" role="progressbar" style="width: ${percent}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    // Add Modal
    document.getElementById('add-warehouse-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const newWh = {
            code: document.getElementById('add-code').value.trim().toUpperCase(),
            name: document.getElementById('add-name').value.trim(),
            manager: document.getElementById('add-manager').value.trim(),
            capacity: parseInt(document.getElementById('add-capacity').value),
            capacityUsed: 0,
            address: document.getElementById('add-address').value.trim()
        };

        db.insertItem('warehouses', newWh);

        // Audit log
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        db.logAction(activeUser.name, "Warehouse Management", "Create Warehouse", `Membuat gudang baru: ${newWh.name} (${newWh.code})`, "Success");

        bootstrap.Modal.getInstance(document.getElementById('addWarehouseModal')).hide();
        document.getElementById('add-warehouse-form').reset();
        renderWarehouses();
    });

    // Open Edit Modal
    function openEditModal(id) {
        const warehouses = db.getData('warehouses');
        const wh = warehouses.find(w => w.id === id);
        if (wh) {
            document.getElementById('edit-id').value = wh.id;
            document.getElementById('edit-code').value = wh.code;
            document.getElementById('edit-name').value = wh.name;
            document.getElementById('edit-manager').value = wh.manager;
            document.getElementById('edit-capacity').value = wh.capacity;
            document.getElementById('edit-address').value = wh.address;

            const modal = new bootstrap.Modal(document.getElementById('editWarehouseModal'));
            modal.show();
        }
    }

    // Edit Modal Submit
    document.getElementById('edit-warehouse-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('edit-id').value;
        const updatedWh = {
            code: document.getElementById('edit-code').value.trim().toUpperCase(),
            name: document.getElementById('edit-name').value.trim(),
            manager: document.getElementById('edit-manager').value.trim(),
            capacity: parseInt(document.getElementById('edit-capacity').value),
            address: document.getElementById('edit-address').value.trim()
        };

        db.updateItem('warehouses', id, updatedWh);

        // Audit log
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        db.logAction(activeUser.name, "Warehouse Management", "Update Warehouse", `Mengubah data gudang ID #${id}: ${updatedWh.name}`, "Success");

        bootstrap.Modal.getInstance(document.getElementById('editWarehouseModal')).hide();
        renderWarehouses();
    });

    // Delete Warehouse
    function deleteWarehouse(id) {
        if (confirm('Apakah Anda yakin ingin menghapus gudang ini?')) {
            const warehouses = db.getData('warehouses');
            const wh = warehouses.find(w => w.id === id);
            
            db.deleteItem('warehouses', id);

            // Audit log
            const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
            db.logAction(activeUser.name, "Warehouse Management", "Delete Warehouse", `Menghapus gudang: ${wh ? wh.name : 'Unknown'}`, "Success");

            renderWarehouses();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderWarehouses();
    });
</script>
@endsection
