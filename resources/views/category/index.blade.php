@extends('layouts.main')

@section('title', 'Category Management - CV Mugijaya Logistics ERP')

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
        <h4 class="fw-bold mb-1">Category Management</h4>
        <p class="text-muted text-sm m-0">Kelola kategori produk untuk klasifikasi barang dan pengaturan inventaris.</p>
    </div>
    
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Search form -->
        <form action="{{ route('category.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group" style="max-width: 250px;">
                <span class="input-group-text bg-light-dark border-light-dark text-muted py-1.5"><i class="bi bi-search text-xs"></i></span>
                <input type="text" name="search" class="form-control bg-light-dark border-light-dark text-white text-xs" placeholder="Cari kategori..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('category.index') }}" class="btn btn-outline-secondary border-light-dark bg-light-dark d-flex align-items-center"><i class="bi bi-x-lg text-xs"></i></a>
                @endif
            </div>
            <button type="submit" class="btn btn-primary-gradient px-3 text-sm">Cari</button>
        </form>

        <button class="btn btn-primary-gradient d-flex align-items-center gap-1 py-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-circle-fill"></i> Tambah Kategori Baru
        </button>
    </div>
</div>

<!-- Category Grid -->
<div class="row g-4 mb-4" id="category-grid">
    @forelse($categories as $cat)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="erp-card h-100 d-flex flex-column justify-content-between p-4">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-indigo text-xs fw-bold">{{ $cat->slug }}</span>
                        <div class="dropdown">
                            <button class="btn btn-link text-secondary p-0" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-light-dark bg-secondary shadow-lg">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="openEditModal({{ json_encode($cat) }}); return false;">
                                        <i class="bi bi-pencil me-2 text-indigo"></i>Edit Kategori
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('category.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Kategori produk akan dihapus.')">
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
                    <h5 class="fw-bold mb-2 text-primary">{{ $cat->name }}</h5>
                    <p class="text-xs text-secondary mb-3" style="min-height: 40px; white-space: normal; line-height: 1.5;">
                        {{ $cat->description ?: 'Tidak ada deskripsi.' }}
                    </p>
                </div>

                <div class="d-flex align-items-center justify-content-between border-top border-light-dark pt-3 mt-2">
                    <span class="text-xs text-muted">Status:</span>
                    @if($cat->status)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-30 text-xs">Aktif</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-30 text-xs">Non-Aktif</span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="erp-card p-5 text-center d-flex flex-column align-items-center justify-content-center border-dashed border-light-dark bg-light-dark">
                <i class="bi bi-tags-fill text-muted mb-3" style="font-size: 3rem;"></i>
                <h6 class="fw-bold">Tidak Ada Kategori Ditemukan</h6>
                <p class="text-muted text-xs" style="max-width: 300px;">Data kategori kosong atau tidak cocok dengan filter pencarian Anda.</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination Links -->
<div class="d-flex justify-content-center mt-4">
    {{ $categories->links() }}
</div>

<!-- Modals Section -->
<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill text-indigo me-2"></i>Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-category-form" action="{{ route('category.store') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add-name" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="add-name" name="name" required value="{{ old('action') === 'create' ? old('name') : '' }}" placeholder="Contoh: Electronics" onkeyup="generateSlug('add-name', 'add-slug')">
                    </div>
                    <div class="mb-3">
                        <label for="add-slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="add-slug" name="slug" required value="{{ old('action') === 'create' ? old('slug') : '' }}" placeholder="Contoh: electronics">
                    </div>
                    <div class="mb-3">
                        <label for="add-description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="add-description" name="description" rows="3" placeholder="Deskripsi kategori...">{{ old('action') === 'create' ? old('description') : '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="add-status" class="form-label">Status</label>
                        <select class="form-select" id="add-status" name="status">
                            <option value="1" {{ old('action') === 'create' && old('status') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('action') === 'create' && old('status') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-indigo me-2"></i>Edit Detail Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-category-form" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit-id" name="id" value="{{ old('action') === 'edit' ? old('id') : '' }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required value="{{ old('action') === 'edit' ? old('name') : '' }}" onkeyup="generateSlug('edit-name', 'edit-slug')">
                    </div>
                    <div class="mb-3">
                        <label for="edit-slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="edit-slug" name="slug" required value="{{ old('action') === 'edit' ? old('slug') : '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="edit-description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit-description" name="description" rows="3">{{ old('action') === 'edit' ? old('description') : '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Status</label>
                        <select class="form-select" id="edit-status" name="status">
                            <option value="1" {{ old('action') === 'edit' && old('status') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('action') === 'edit' && old('status') === '0' ? 'selected' : '' }}>Non-Aktif</option>
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
<script>
    // Auto generate slug helper
    function generateSlug(sourceId, targetId) {
        const nameVal = document.getElementById(sourceId).value;
        const slugVal = nameVal.toLowerCase()
                               .replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                               .replace(/\s+/g, '-')        // collapse whitespace and replace by -
                               .replace(/-+/g, '-');        // collapse dashes
        document.getElementById(targetId).value = slugVal;
    }

    // Open edit modal
    function openEditModal(cat) {
        if (cat) {
            document.getElementById('edit-id').value = cat.id;
            document.getElementById('edit-name').value = cat.name;
            document.getElementById('edit-slug').value = cat.slug;
            document.getElementById('edit-description').value = cat.description || '';
            document.getElementById('edit-status').value = cat.status ? "1" : "0";

            // Set action URL dynamically
            const form = document.getElementById('edit-category-form');
            form.action = `/category/${cat.id}`;

            const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
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
                const cat = {
                    id: "{{ old('id') }}",
                    name: "{{ old('name') }}",
                    slug: "{{ old('slug') }}",
                    description: {!! json_encode(old('description')) !!},
                    status: {{ old('status') == '1' ? 'true' : 'false' }}
                };
                openEditModal(cat);
            @else
                const addModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
                addModal.show();
            @endif
        @endif
    });
</script>
@endsection
