@extends('layouts.main')

@section('title', 'Manajemen User - CV Mugijaya Logistics ERP')

@section('styles')
<!-- DataTables BS5 CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Manajemen User</h4>
        <p class="text-muted text-sm m-0">Pengaturan hak akses, peran karyawan, dan status aktif akun.</p>
    </div>
    <button class="btn btn-primary-gradient d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus-fill"></i> Tambah User Baru
    </button>
</div>

<div class="erp-card p-4">
    <div class="table-responsive">
        <table id="users-table" class="table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Lengkap</th>
                    <th>Alamat Email</th>
                    <th>Peran / Role</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill text-indigo me-2"></i>Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-user-form">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add-name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="add-name" name="name" required placeholder="Contoh: Andi Wijaya">
                    </div>
                    <div class="mb-3">
                        <label for="add-email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control" id="add-email" name="email" required placeholder="Contoh: andi@mugijaya.com">
                    </div>
                    <div class="mb-3">
                        <label for="add-role" class="form-label">Peran / Role</label>
                        <select class="form-select" id="add-role" name="role" required>
                            <option value="Super Admin">Super Admin</option>
                            <option value="Warehouse Admin">Warehouse Admin</option>
                            <option value="Courier Admin">Courier Admin</option>
                            <option value="Driver">Driver</option>
                            <option value="Validator">Validator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="add-status" class="form-label">Status Akun</label>
                        <select class="form-select" id="add-status" name="status" required>
                            <option value="Active">Aktif</option>
                            <option value="Inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-indigo me-2"></i>Edit Informasi User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-user-form">
                @csrf
                <input type="hidden" id="edit-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control" id="edit-email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-role" class="form-label">Peran / Role</label>
                        <select class="form-select" id="edit-role" name="role" required>
                            <option value="Super Admin">Super Admin</option>
                            <option value="Warehouse Admin">Warehouse Admin</option>
                            <option value="Courier Admin">Courier Admin</option>
                            <option value="Driver">Driver</option>
                            <option value="Validator">Validator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Status Akun</label>
                        <select class="form-select" id="edit-status" name="status" required>
                            <option value="Active">Aktif</option>
                            <option value="Inactive">Nonaktif</option>
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    let table;

    function renderUsersTable() {
        const users = db.getData('users');
        if ($.fn.DataTable.isDataTable('#users-table')) { table.destroy(); }
        const tbody = document.querySelector('#users-table tbody');
        tbody.innerHTML = '';

        users.forEach(user => {
            const badgeClass = user.status === 'Active' ? 'bg-success text-success bg-opacity-10 border border-success border-opacity-30' : 'bg-danger text-danger bg-opacity-10 border border-danger border-opacity-30';
            let roleIcon = 'bi-person';
            if (user.role === 'Super Admin') roleIcon = 'bi-shield-fill text-danger';
            else if (user.role === 'Warehouse Admin') roleIcon = 'bi-house-fill text-success';
            else if (user.role === 'Courier Admin') roleIcon = 'bi-truck text-indigo';
            else if (user.role === 'Validator') roleIcon = 'bi-patch-check-fill text-warning';

            tbody.innerHTML += `
                <tr>
                    <td>#${user.id}</td>
                    <td class="fw-semibold text-primary"><i class="bi ${roleIcon} me-2"></i>${user.name}</td>
                    <td>${user.email}</td>
                    <td><span class="badge bg-light-dark border border-light-dark text-secondary fw-semibold">${user.role}</span></td>
                    <td><span class="badge ${badgeClass} text-xs">${user.status === 'Active' ? 'Aktif' : 'Nonaktif'}</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-info me-1" onclick="openEditModal(${user.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id})" title="Hapus"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        });

        table = $('#users-table').DataTable({
            responsive: true,
            language: {
                search: "Cari:", lengthMenu: "Tampilkan _MENU_ user",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ user",
                paginate: { next: "<i class='bi bi-chevron-right'></i>", previous: "<i class='bi bi-chevron-left'></i>" }
            }
        });
    }

    function openEditModal(id) {
        const users = db.getData('users');
        const user = users.find(u => u.id === id);
        if (user) {
            document.getElementById('edit-id').value = user.id;
            document.getElementById('edit-name').value = user.name;
            document.getElementById('edit-email').value = user.email;
            document.getElementById('edit-role').value = user.role;
            document.getElementById('edit-status').value = user.status;
            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();
        }
    }

    document.getElementById('add-user-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const newUser = {
            name: document.getElementById('add-name').value.trim(),
            email: document.getElementById('add-email').value.trim(),
            role: document.getElementById('add-role').value,
            status: document.getElementById('add-status').value
        };
        db.insertItem('users', newUser);
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        db.logAction(activeUser.name, "User Management", "Create User", `Menambahkan user baru: ${newUser.name} (${newUser.role})`, "Success");
        bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
        document.getElementById('add-user-form').reset();
        renderUsersTable();
    });

    document.getElementById('edit-user-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('edit-id').value;
        const updatedUser = {
            name: document.getElementById('edit-name').value.trim(),
            email: document.getElementById('edit-email').value.trim(),
            role: document.getElementById('edit-role').value,
            status: document.getElementById('edit-status').value
        };
        db.updateItem('users', id, updatedUser);
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        db.logAction(activeUser.name, "User Management", "Update User", `Mengubah data user ID #${id}: ${updatedUser.name}`, "Success");
        bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
        renderUsersTable();
    });

    function deleteUser(id) {
        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            const users = db.getData('users');
            const user = users.find(u => u.id === id);
            db.deleteItem('users', id);
            const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
            db.logAction(activeUser.name, "User Management", "Delete User", `Menghapus user: ${user ? user.name : 'Unknown'}`, "Success");
            renderUsersTable();
        }
    }

    document.addEventListener('DOMContentLoaded', () => { renderUsersTable(); });
</script>
@endsection
