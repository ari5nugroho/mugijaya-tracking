@extends('layouts.main')

@section('title', 'Warehouse Management - CV Mugijaya Logistics ERP')

@section('content')
<!-- Success Alert -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background-color: rgba(22, 163, 74, 0.2); color: #4ade80;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(1);"></button>
    </div>
@endif

<!-- Validation Errors -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background-color: rgba(220, 38, 38, 0.2); color: #f87171;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong class="d-block mb-1">Terjadi Kesalahan Validasi:</strong>
                <ul class="mb-0 text-sm ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(1);"></button>
    </div>
@endif

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Warehouse Management</h4>
        <p class="text-muted text-sm m-0">Kelola informasi gudang utama, cabang, kapasitas, serta kepala gudang.</p>
    </div>
    
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Search form -->
        <form action="{{ route('warehouse.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group" style="max-width: 250px;">
                <span class="input-group-text bg-light-dark border-light-dark text-muted py-1.5"><i class="bi bi-search text-xs"></i></span>
                <input type="text" name="search" class="form-control bg-light-dark border-light-dark text-white text-xs" placeholder="Cari gudang..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('warehouse.index') }}" class="btn btn-outline-secondary border-light-dark bg-light-dark d-flex align-items-center"><i class="bi bi-x-lg text-xs"></i></a>
                @endif
            </div>
            <button type="submit" class="btn btn-primary-gradient px-3 text-sm">Cari</button>
        </form>

        <button class="btn btn-primary-gradient d-flex align-items-center gap-1 py-2" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
            <i class="bi bi-house-add-fill"></i> Tambah Gudang Baru
        </button>
    </div>
</div>

<!-- Warehouse Grid -->
<div class="row g-4 mb-4" id="warehouse-grid">
    @forelse($warehouses as $wh)
        @php
            $percent = $wh->capacity > 0 ? round(($wh->capacity_used / $wh->capacity) * 100) : 0;
            $progressColorClass = 'bg-success';
            $alertBadge = '';
            if ($percent > 85) {
                $progressColorClass = 'bg-danger';
                $alertBadge = '<span class="badge bg-danger text-xs ms-2"><i class="bi bi-exclamation-triangle-fill"></i> Penuh</span>';
            } else if ($percent > 65) {
                $progressColorClass = 'bg-warning';
                $alertBadge = '<span class="badge bg-warning text-dark text-xs ms-2">Padat</span>';
            }
        @endphp
        <div class="col-12 col-md-6 col-lg-4">
            <div class="erp-card h-100 d-flex flex-column justify-content-between p-4">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-indigo text-xs fw-bold">{{ $wh->code }}</span>
                        <div class="dropdown">
                            <button class="btn btn-link text-secondary p-0" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-light-dark bg-secondary shadow-lg">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="openEditModal({{ json_encode($wh) }}); return false;">
                                        <i class="bi bi-pencil me-2 text-indigo"></i>Edit Gudang
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('warehouse.destroy', $wh->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gudang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start">
                                            <i class="bi bi-trash me-2 text-danger"></i>Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 text-primary d-flex align-items-center">
                        {{ $wh->name }} 
                        {!! $alertBadge !!}
                    </h5>
                    <p class="text-xs text-muted mb-3"><i class="bi bi-geo-alt-fill me-1"></i>{{ $wh->address }}</p>
                    
                    <div class="row g-2 mb-4 bg-light-dark p-2.5 rounded border border-light-dark">
                        <div class="col-6 text-center border-end border-light-dark">
                            <div class="text-xs text-muted">Model Produk</div>
                            <div class="fw-bold text-lg text-indigo">0</div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="text-xs text-muted">Total Stok</div>
                            <div class="fw-bold text-lg text-success">0 Pcs</div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between text-xs mb-1.5">
                        <span class="text-secondary fw-semibold"><i class="bi bi-person-fill text-muted me-1"></i>Manager: {{ $wh->manager }}</span>
                        <span class="text-muted fw-bold">{{ number_format($wh->capacity_used) }} / {{ number_format($wh->capacity) }} m³ ({{ $percent }}%)</span>
                    </div>
                    <div class="progress capacity-progress mb-1">
                        <div class="progress-bar {{ $progressColorClass }}" role="progressbar" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="erp-card p-5 text-center d-flex flex-column align-items-center justify-content-center border-dashed border-light-dark bg-light-dark">
                <i class="bi bi-house-x-fill text-muted mb-3" style="font-size: 3rem;"></i>
                <h6 class="fw-bold">Tidak Ada Gudang Ditemukan</h6>
                <p class="text-muted text-xs" style="max-width: 300px;">Data gudang kosong atau tidak cocok dengan filter pencarian Anda.</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination Links -->
<div class="d-flex justify-content-center mt-4">
    {{ $warehouses->links() }}
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
            <form id="add-warehouse-form" action="{{ route('warehouse.store') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="add-code" class="form-label">Kode Gudang</label>
                            <input type="text" class="form-control" id="add-code" name="code" required value="{{ old('action') === 'create' ? old('code') : '' }}" placeholder="Contoh: WH-BDG-04">
                        </div>
                        <div class="col-md-6">
                            <label for="add-name" class="form-label">Nama Gudang</label>
                            <input type="text" class="form-control" id="add-name" name="name" required value="{{ old('action') === 'create' ? old('name') : '' }}" placeholder="Contoh: Gudang Bandung">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="add-manager" class="form-label">Kepala Gudang (Manager)</label>
                            <input type="text" class="form-control" id="add-manager" name="manager" required value="{{ old('action') === 'create' ? old('manager') : '' }}" placeholder="Contoh: Haryanto">
                        </div>
                        <div class="col-md-6">
                            <label for="add-capacity" class="form-label">Kapasitas (m³)</label>
                            <input type="number" class="form-control" id="add-capacity" name="capacity" required value="{{ old('action') === 'create' ? old('capacity') : '' }}" placeholder="Contoh: 20000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add-address" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="add-address" name="address" rows="3" required placeholder="Jl. Industri Raya No. 4...">{{ old('action') === 'create' ? old('address') : '' }}</textarea>
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
            <form id="edit-warehouse-form" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit-id" name="id" value="{{ old('action') === 'edit' ? old('id') : '' }}">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit-code" class="form-label">Kode Gudang</label>
                            <input type="text" class="form-control" id="edit-code" name="code" required value="{{ old('action') === 'edit' ? old('code') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="edit-name" class="form-label">Nama Gudang</label>
                            <input type="text" class="form-control" id="edit-name" name="name" required value="{{ old('action') === 'edit' ? old('name') : '' }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit-manager" class="form-label">Kepala Gudang (Manager)</label>
                            <input type="text" class="form-control" id="edit-manager" name="manager" required value="{{ old('action') === 'edit' ? old('manager') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="edit-capacity" class="form-label">Kapasitas (m³)</label>
                            <input type="number" class="form-control" id="edit-capacity" name="capacity" required value="{{ old('action') === 'edit' ? old('capacity') : '' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-address" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="edit-address" name="address" rows="3" required>{{ old('action') === 'edit' ? old('address') : '' }}</textarea>
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
    // Open Edit Modal
    function openEditModal(wh) {
        if (wh) {
            document.getElementById('edit-id').value = wh.id;
            document.getElementById('edit-code').value = wh.code;
            document.getElementById('edit-name').value = wh.name;
            document.getElementById('edit-manager').value = wh.manager;
            document.getElementById('edit-capacity').value = wh.capacity;
            document.getElementById('edit-address').value = wh.address;

            // Dynamically set action URL for update
            const form = document.getElementById('edit-warehouse-form');
            form.action = `/warehouse/${wh.id}`;

            const modal = new bootstrap.Modal(document.getElementById('editWarehouseModal'));
            modal.show();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Sync local storage audit log mock system with the backend redirect action
        @if(session('log_action'))
            const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
            if (activeUser && window.db) {
                db.logAction(
                    activeUser.name,
                    "{{ session('log_action.category') }}",
                    "{{ session('log_action.action') }}",
                    "{{ session('log_action.details') }}",
                    "Success"
                );
            }
        @endif

        // Re-open modal if validation fails
        @if($errors->any())
            @if(old('action') === 'edit')
                const wh = {
                    id: "{{ old('id') }}",
                    code: "{{ old('code') }}",
                    name: "{{ old('name') }}",
                    manager: "{{ old('manager') }}",
                    capacity: "{{ old('capacity') }}",
                    address: {!! json_encode(old('address')) !!}
                };
                openEditModal(wh);
            @else
                const addModal = new bootstrap.Modal(document.getElementById('addWarehouseModal'));
                addModal.show();
            @endif
        @endif
    });
</script>
@endsection
