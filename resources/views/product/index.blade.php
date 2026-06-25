@extends('layouts.main')

@section('title', 'Product Management - CV Mugijaya Logistics ERP')

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
        <h4 class="fw-bold mb-1">Product Management</h4>
        <p class="text-muted text-sm m-0">Katalog utama barang/produk, SKU, dimensi, berat, dan harga dasar satuan.</p>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Search form -->
        <form action="{{ route('product.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group" style="max-width: 250px;">
                <span class="input-group-text bg-light-dark border-light-dark text-muted py-1.5"><i class="bi bi-search text-xs"></i></span>
                <input type="text" name="search" class="form-control bg-light-dark border-light-dark text-white text-xs" placeholder="Cari SKU atau nama..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('product.index') }}" class="btn btn-outline-secondary border-light-dark bg-light-dark d-flex align-items-center"><i class="bi bi-x-lg text-xs"></i></a>
                @endif
            </div>
            <button type="submit" class="btn btn-primary-gradient px-3 text-sm">Cari</button>
        </form>

        <button class="btn btn-primary-gradient d-flex align-items-center gap-1 py-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-square-fill"></i> Tambah Produk Baru
        </button>
    </div>
</div>

<div class="erp-card p-4">
    <div class="table-responsive">
        <table id="products-table" class="table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Berat Satuan</th>
                    <th>Dimensi (P x L x T)</th>
                    <th>Harga Dasar</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                    <tr>
                        <td class="fw-bold text-indigo">{{ $p->sku }}</td>
                        <td class="fw-semibold text-primary">
                            {{ $p->name }}
                            @if($p->description)
                                <div class="text-muted font-normal text-xs mt-0.5" style="max-width: 320px; white-space: normal;">{{ $p->description }}</div>
                            @endif
                        </td>
                        <td><span class="badge bg-light-dark border border-light-dark text-secondary">{{ $p->category->name }}</span></td>
                        <td>{{ $p->weight }} {{ $p->unit }}</td>
                        <td>
                            @if($p->length || $p->width || $p->height)
                                {{ $p->length ?? 0 }} x {{ $p->width ?? 0 }} x {{ $p->height ?? 0 }} cm
                            @else
                                <span class="text-muted text-xs">-</span>
                            @endif
                        </td>
                        <td class="fw-semibold">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-info me-1" onclick="openEditModal({{ json_encode($p) }})" title="Edit"><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('product.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-box2-fill text-muted mb-2 d-block fs-3"></i>
                            Tidak ada produk ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Links -->
<div class="d-flex justify-content-center mt-4">
    {{ $products->links() }}
</div>

<!-- Modals Section -->
<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-square-fill text-indigo me-2"></i>Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-product-form" action="{{ route('product.store') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="add-sku" class="form-label">SKU (Stock Keeping Unit)</label>
                            <input type="text" class="form-control" id="add-sku" name="sku" required value="{{ old('action') === 'create' ? old('sku') : '' }}" placeholder="Contoh: PRD-ELC-005">
                        </div>
                        <div class="col-md-6">
                            <label for="add-name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="add-name" name="name" required value="{{ old('action') === 'create' ? old('name') : '' }}" placeholder="Contoh: Rice Cooker Digital 2L">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="add-category" class="form-label">Kategori</label>
                            <select class="form-select" id="add-category" name="category_id" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (old('action') === 'create' && old('category_id') == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="add-price" class="form-label">Harga Dasar (IDR)</label>
                            <input type="number" class="form-control" id="add-price" name="price" required value="{{ old('action') === 'create' ? old('price') : '' }}" placeholder="Contoh: 450000">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="add-weight" class="form-label">Berat Satuan (Kg)</label>
                            <input type="number" step="0.01" class="form-control" id="add-weight" name="weight" required value="{{ old('action') === 'create' ? old('weight') : '' }}" placeholder="Contoh: 2.5">
                        </div>
                        <div class="col-md-6">
                            <label for="add-unit" class="form-label">Satuan Unit</label>
                            <input type="text" class="form-control" id="add-unit" name="unit" required placeholder="Contoh: Pcs, Pack, Box" value="{{ old('action') === 'create' ? old('unit') : 'Pcs' }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="add-length" class="form-label">Panjang (cm)</label>
                            <input type="number" step="0.01" class="form-control" id="add-length" name="length" placeholder="Panjang" value="{{ old('action') === 'create' ? old('length') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label for="add-width" class="form-label">Lebar (cm)</label>
                            <input type="number" step="0.01" class="form-control" id="add-width" name="width" placeholder="Lebar" value="{{ old('action') === 'create' ? old('width') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label for="add-height" class="form-label">Tinggi (cm)</label>
                            <input type="number" step="0.01" class="form-control" id="add-height" name="height" placeholder="Tinggi" value="{{ old('action') === 'create' ? old('height') : '' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add-description" class="form-label">Deskripsi Produk</label>
                        <textarea class="form-control" id="add-description" name="description" rows="2" placeholder="Deskripsi detail produk...">{{ old('action') === 'create' ? old('description') : '' }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-indigo me-2"></i>Edit Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-product-form" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit-id" name="id" value="{{ old('action') === 'edit' ? old('id') : '' }}">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="edit-sku" class="form-label">SKU (Stock Keeping Unit)</label>
                            <input type="text" class="form-control" id="edit-sku" name="sku" required readonly value="{{ old('action') === 'edit' ? old('sku') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="edit-name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="edit-name" name="name" required value="{{ old('action') === 'edit' ? old('name') : '' }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="edit-category" class="form-label">Kategori</label>
                            <select class="form-select" id="edit-category" name="category_id" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (old('action') === 'edit' && old('category_id') == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-price" class="form-label">Harga Dasar (IDR)</label>
                            <input type="number" class="form-control" id="edit-price" name="price" required value="{{ old('action') === 'edit' ? old('price') : '' }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="edit-weight" class="form-label">Berat Satuan (Kg)</label>
                            <input type="number" step="0.01" class="form-control" id="edit-weight" name="weight" required value="{{ old('action') === 'edit' ? old('weight') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="edit-unit" class="form-label">Satuan Unit</label>
                            <input type="text" class="form-control" id="edit-unit" name="unit" required value="{{ old('action') === 'edit' ? old('unit') : '' }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="edit-length" class="form-label">Panjang (cm)</label>
                            <input type="number" step="0.01" class="form-control" id="edit-length" name="length" value="{{ old('action') === 'edit' ? old('length') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label for="edit-width" class="form-label">Lebar (cm)</label>
                            <input type="number" step="0.01" class="form-control" id="edit-width" name="width" value="{{ old('action') === 'edit' ? old('width') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label for="edit-height" class="form-label">Tinggi (cm)</label>
                            <input type="number" step="0.01" class="form-control" id="edit-height" name="height" value="{{ old('action') === 'edit' ? old('height') : '' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-description" class="form-label">Deskripsi Produk</label>
                        <textarea class="form-control" id="edit-description" name="description" rows="2">{{ old('action') === 'edit' ? old('description') : '' }}</textarea>
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
    // Open edit modal
    function openEditModal(p) {
        if (p) {
            document.getElementById('edit-id').value = p.id;
            document.getElementById('edit-sku').value = p.sku;
            document.getElementById('edit-name').value = p.name;
            document.getElementById('edit-category').value = p.category_id;
            document.getElementById('edit-price').value = Math.round(p.price);
            document.getElementById('edit-weight').value = p.weight;
            document.getElementById('edit-unit').value = p.unit;
            document.getElementById('edit-length').value = p.length || '';
            document.getElementById('edit-width').value = p.width || '';
            document.getElementById('edit-height').value = p.height || '';
            document.getElementById('edit-description').value = p.description || '';

            // Set action URL dynamically
            const form = document.getElementById('edit-product-form');
            form.action = `/product/${p.id}`;

            const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
            modal.show();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Sync local storage audit logs
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

        // Re-open modals on validation error
        @if($errors->any())
            @if(old('action') === 'edit')
                const p = {
                    id: "{{ old('id') }}",
                    sku: "{{ old('sku') }}",
                    name: "{{ old('name') }}",
                    category_id: "{{ old('category_id') }}",
                    price: "{{ old('price') }}",
                    weight: "{{ old('weight') }}",
                    unit: "{{ old('unit') }}",
                    length: "{{ old('length') }}",
                    width: "{{ old('width') }}",
                    height: "{{ old('height') }}",
                    description: {!! json_encode(old('description')) !!}
                };
                openEditModal(p);
            @else
                const addModal = new bootstrap.Modal(document.getElementById('addProductModal'));
                addModal.show();
            @endif
        @endif

        // Support direct shortcut display for add modal
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'new') {
            const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
            modal.show();
        }
    });
</script>
@endsection
