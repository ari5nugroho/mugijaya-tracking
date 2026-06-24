@extends('layouts.main')

@section('title', 'Driver & Armada - CV Mugijaya Logistics ERP')

@section('styles')
<!-- DataTables BS5 CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Driver & Fleet Management</h4>
        <p class="text-muted text-sm m-0">Pengaturan data sopir (driver), nomor polisi kendaraan, kelas armada, serta status operasional.</p>
    </div>
    <button class="btn btn-primary-gradient d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addDriverModal">
        <i class="bi bi-person-fill-add"></i> Tambah Driver Baru
    </button>
</div>

<div class="erp-card p-4">
    <div class="table-responsive">
        <table id="drivers-table" class="table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Driver</th>
                    <th>No. Telepon</th>
                    <th>Plat Nomor (Nopol)</th>
                    <th>Kelas Armada</th>
                    <th>Rating</th>
                    <th>Status Operasional</th>
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
<!-- Add Driver Modal -->
<div class="modal fade" id="addDriverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-fill-add text-indigo me-2"></i>Tambah Driver Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-driver-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add-drv-name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="add-drv-name" required placeholder="Contoh: Roni Hermawan">
                    </div>
                    <div class="mb-3">
                        <label for="add-drv-phone" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="add-drv-phone" required placeholder="Contoh: 0812xxxxxxxx">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="add-drv-plate" class="form-label">Plat Nomor (Nopol)</label>
                            <input type="text" class="form-control" id="add-drv-plate" required placeholder="Contoh: H 8821 YY">
                        </div>
                        <div class="col-md-6">
                            <label for="add-drv-class" class="form-label">Kelas Armada</label>
                            <select class="form-select" id="add-drv-class" required>
                                <option value="Pickup Bak (Light)">Pickup Bak (Light)</option>
                                <option value="CDE Box (Medium)">CDE Box (Medium)</option>
                                <option value="CDD Box (Heavy)">CDD Box (Heavy)</option>
                                <option value="Wingbox Truck">Wingbox Truck</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add-drv-status" class="form-label">Status Awal</label>
                        <select class="form-select" id="add-drv-status" required>
                            <option value="Available">Tersedia (Available)</option>
                            <option value="On Delivery">Sedang Mengirim (On Delivery)</option>
                            <option value="Break">Istirahat (Break)</option>
                            <option value="Off Duty">Libur (Off Duty)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">Simpan Driver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Driver Modal -->
<div class="modal fade" id="editDriverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-indigo me-2"></i>Edit Informasi Driver</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-driver-form">
                <input type="hidden" id="edit-drv-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-drv-name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit-drv-name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-drv-phone" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="edit-drv-phone" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit-drv-plate" class="form-label">Plat Nomor (Nopol)</label>
                            <input type="text" class="form-control" id="edit-drv-plate" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-drv-class" class="form-label">Kelas Armada</label>
                            <select class="form-select" id="edit-drv-class" required>
                                <option value="Pickup Bak (Light)">Pickup Bak (Light)</option>
                                <option value="CDE Box (Medium)">CDE Box (Medium)</option>
                                <option value="CDD Box (Heavy)">CDD Box (Heavy)</option>
                                <option value="Wingbox Truck">Wingbox Truck</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-drv-status" class="form-label">Status Operasional</label>
                        <select class="form-select" id="edit-drv-status" required>
                            <option value="Available">Tersedia (Available)</option>
                            <option value="On Delivery">Sedang Mengirim (On Delivery)</option>
                            <option value="Break">Istirahat (Break)</option>
                            <option value="Off Duty">Libur (Off Duty)</option>
                        </select>
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
<!-- jQuery (needed for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    let table;

    function renderDriversTable() {
        const drivers = db.getData('drivers');

        if ($.fn.DataTable.isDataTable('#drivers-table')) {
            table.destroy();
        }

        const tbody = document.querySelector('#drivers-table tbody');
        tbody.innerHTML = '';

        drivers.forEach(d => {
            let badgeClass = 'bg-secondary';
            if (d.status === 'Available') badgeClass = 'bg-success text-success bg-opacity-10 border border-success border-opacity-30';
            else if (d.status === 'On Delivery') badgeClass = 'bg-info text-info bg-opacity-10 border border-info border-opacity-30';
            else if (d.status === 'Break') badgeClass = 'bg-warning text-warning bg-opacity-10 border border-warning border-opacity-30';
            else if (d.status === 'Off Duty') badgeClass = 'bg-danger text-danger bg-opacity-10 border border-danger border-opacity-30';

            // Render star ratings
            const stars = '<i class="bi bi-star-fill text-warning me-1"></i>' + d.rating.toFixed(1);

            tbody.innerHTML += `
                <tr>
                    <td>#${d.id}</td>
                    <td class="fw-semibold text-primary"><i class="bi bi-person-fill me-2 text-indigo"></i>${d.name}</td>
                    <td>${d.phone}</td>
                    <td><span class="badge bg-light-dark text-secondary font-monospace">${d.licensePlate}</span></td>
                    <td class="text-xs">${d.vehicleClass}</td>
                    <td class="fw-bold">${stars}</td>
                    <td><span class="badge ${badgeClass} text-xs">${d.status}</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-info me-1" onclick="openEditModal(${d.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteDriver(${d.id})" title="Hapus"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        });

        table = $('#drivers-table').DataTable({
            responsive: true,
            language: {
                search: "Cari Sopir:",
                lengthMenu: "Tampil _MENU_",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ driver",
                paginate: {
                    next: "<i class='bi bi-chevron-right'></i>",
                    previous: "<i class='bi bi-chevron-left'></i>"
                }
            }
        });
    }

    // Open Edit modal
    function openEditModal(id) {
        const drivers = db.getData('drivers');
        const d = drivers.find(drv => drv.id === id);
        if (d) {
            document.getElementById('edit-drv-id').value = d.id;
            document.getElementById('edit-drv-name').value = d.name;
            document.getElementById('edit-drv-phone').value = d.phone;
            document.getElementById('edit-drv-plate').value = d.licensePlate;
            document.getElementById('edit-drv-class').value = d.vehicleClass;
            document.getElementById('edit-drv-status').value = d.status;

            const modal = new bootstrap.Modal(document.getElementById('editDriverModal'));
            modal.show();
        }
    }

    // Add form handler
    document.getElementById('add-driver-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const newDriver = {
            name: document.getElementById('add-drv-name').value.trim(),
            phone: document.getElementById('add-drv-phone').value.trim(),
            licensePlate: document.getElementById('add-drv-plate').value.trim().toUpperCase(),
            vehicleClass: document.getElementById('add-drv-class').value,
            status: document.getElementById('add-drv-status').value,
            rating: 5.0 // Initial starting rating
        };

        db.insertItem('drivers', newDriver);

        // Audit log
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        db.logAction(activeUser.name, "Driver Management", "Create Driver", `Menambahkan driver baru: ${newDriver.name} (${newDriver.licensePlate})`, "Success");

        bootstrap.Modal.getInstance(document.getElementById('addDriverModal')).hide();
        document.getElementById('add-driver-form').reset();
        renderDriversTable();
    });

    // Edit form handler
    document.getElementById('edit-driver-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('edit-drv-id').value;
        const updatedDriver = {
            name: document.getElementById('edit-drv-name').value.trim(),
            phone: document.getElementById('edit-drv-phone').value.trim(),
            licensePlate: document.getElementById('edit-drv-plate').value.trim().toUpperCase(),
            vehicleClass: document.getElementById('edit-drv-class').value,
            status: document.getElementById('edit-drv-status').value
        };

        db.updateItem('drivers', id, updatedDriver);

        // Audit log
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        db.logAction(activeUser.name, "Driver Management", "Update Driver", `Mengubah data driver ID #${id}: ${updatedDriver.name}`, "Success");

        bootstrap.Modal.getInstance(document.getElementById('editDriverModal')).hide();
        renderDriversTable();
    });

    // Delete Driver
    function deleteDriver(id) {
        if (confirm('Apakah Anda yakin ingin menghapus driver ini dari armada?')) {
            const drivers = db.getData('drivers');
            const d = drivers.find(drv => drv.id === id);

            db.deleteItem('drivers', id);

            // Audit log
            const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
            db.logAction(activeUser.name, "Driver Management", "Delete Driver", `Menghapus driver: ${d ? d.name : 'Unknown'}`, "Success");

            renderDriversTable();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderDriversTable();
    });
</script>
@endsection
