@extends('layouts.main')

@section('title', 'Manajemen Role - CV Mugijaya Logistics ERP')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-shield-lock-fill text-indigo me-2"></i>Manajemen Role</h4>
        <p class="text-muted text-sm m-0">Kelola role dan permission sistem ERP.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-people-fill me-1"></i> Kelola User
        </a>
        <button class="btn btn-sm btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Role
        </button>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 mb-4" role="alert"
     style="background: rgba(22,163,74,0.12); border-left: 3px solid #16A34A !important; border-radius: 8px;">
    <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 mb-4" role="alert"
     style="background: rgba(220,38,38,0.12); border-left: 3px solid #DC2626 !important; border-radius: 8px;">
    <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Roles Grid --}}
<div class="row g-3 mb-4">
    @foreach($roles as $role)
    @php
        $colorMap = [
            'Owner'        => ['bg' => 'rgba(234,179,8,0.12)',  'border' => '#EAB308', 'text' => '#EAB308',  'icon' => 'bi-star-fill'],
            'Admin'        => ['bg' => 'rgba(75,110,245,0.12)', 'border' => '#4B6EF5', 'text' => '#4B6EF5',  'icon' => 'bi-shield-fill-check'],
            'Staff Gudang' => ['bg' => 'rgba(22,163,74,0.12)',  'border' => '#16A34A', 'text' => '#16A34A',  'icon' => 'bi-house-gear-fill'],
            'Driver'       => ['bg' => 'rgba(6,182,212,0.12)',  'border' => '#06B6D4', 'text' => '#06B6D4',  'icon' => 'bi-truck-front-fill'],
        ];
        $style = $colorMap[$role->name] ?? ['bg' => 'rgba(100,116,139,0.12)', 'border' => '#64748B', 'text' => '#64748B', 'icon' => 'bi-person-fill'];
        $isProtected = in_array($role->name, ['Owner', 'Admin', 'Staff Gudang', 'Driver']);
    @endphp
    <div class="col-12 col-md-6 col-xl-3">
        <div class="erp-card p-4 h-100" style="border-top: 3px solid {{ $style['border'] }};">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded p-2" style="background: {{ $style['bg'] }}; color: {{ $style['text'] }}; font-size: 1.1rem;">
                        <i class="bi {{ $style['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="color: var(--text-primary);">{{ $role->name }}</div>
                        @if($isProtected)
                        <span class="badge text-xs" style="background: {{ $style['bg'] }}; color: {{ $style['text'] }};">Sistem</span>
                        @endif
                    </div>
                </div>
                @if(!$isProtected)
                <div class="dropdown">
                    <button class="btn btn-icon btn-sm" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-light-dark bg-secondary">
                        <li>
                            <button class="dropdown-item text-sm" data-bs-toggle="modal"
                                    data-bs-target="#editRoleModal"
                                    data-role-id="{{ $role->id }}"
                                    data-role-name="{{ $role->name }}">
                                <i class="bi bi-pencil me-2 text-primary"></i>Edit Nama
                            </button>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('roles.destroy', $role) }}"
                                  onsubmit="return confirm('Hapus role {{ $role->name }}?')">
                                @csrf @method('DELETE')
                                <button class="dropdown-item text-sm text-danger">
                                    <i class="bi bi-trash me-2"></i>Hapus Role
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endif
            </div>
            <div class="d-flex gap-4">
                <div>
                    <div class="fw-bold fs-5" style="color: {{ $style['text'] }};">{{ $role->permissions->count() }}</div>
                    <div class="text-muted text-xs">Permissions</div>
                </div>
                <div>
                    <div class="fw-bold fs-5" style="color: var(--text-primary);">{{ $role->users->count() }}</div>
                    <div class="text-muted text-xs">Users</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Permissions Table --}}
<div class="erp-card">
    <div class="p-4 border-bottom border-light-dark">
        <h6 class="fw-bold m-0"><i class="bi bi-key-fill text-warning me-2"></i>Daftar Permissions</h6>
    </div>
    <div class="table-responsive">
        <table class="table align-middle text-sm mb-0">
            <thead>
                <tr>
                    <th>Permission</th>
                    <th>Owner</th>
                    <th>Admin</th>
                    <th>Staff Gudang</th>
                    <th>Driver</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $perm)
                @php
                    $ownerRole    = $roles->firstWhere('name', 'Owner');
                    $adminRole    = $roles->firstWhere('name', 'Admin');
                    $staffRole    = $roles->firstWhere('name', 'Staff Gudang');
                    $driverRole   = $roles->firstWhere('name', 'Driver');

                    $has = fn($role, $p) => $role && $role->permissions->contains('name', $p);
                @endphp
                <tr>
                    <td>
                        <code class="text-xs" style="color: var(--accent-indigo, #4B6EF5);">{{ $perm->name }}</code>
                    </td>
                    <td>{!! $has($ownerRole, $perm->name)  ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash text-muted opacity-25"></i>' !!}</td>
                    <td>{!! $has($adminRole, $perm->name)  ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash text-muted opacity-25"></i>' !!}</td>
                    <td>{!! $has($staffRole, $perm->name)  ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash text-muted opacity-25"></i>' !!}</td>
                    <td>{!! $has($driverRole, $perm->name) ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash text-muted opacity-25"></i>' !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Create Role Modal --}}
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="modal-header border-bottom border-light-dark">
                <h6 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2 text-indigo"></i>Tambah Role Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="modal-body">
                    <label class="form-label text-sm fw-semibold">Nama Role</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="cth: Supervisor, QC Inspector..." value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted">Role baru tidak memiliki permissions secara default.</div>
                </div>
                <div class="modal-footer border-top border-light-dark">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary-gradient">
                        <i class="bi bi-plus me-1"></i>Buat Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Role Modal --}}
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="modal-header border-bottom border-light-dark">
                <h6 class="modal-title fw-bold"><i class="bi bi-pencil me-2 text-primary"></i>Edit Role</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="edit-role-form">
                @csrf @method('PUT')
                <div class="modal-body">
                    <label class="form-label text-sm fw-semibold">Nama Role</label>
                    <input type="text" name="name" id="edit-role-name" class="form-control" required>
                </div>
                <div class="modal-footer border-top border-light-dark">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary-gradient">
                        <i class="bi bi-check2 me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const editRoleModal = document.getElementById('editRoleModal');
    editRoleModal.addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        const roleId   = btn.getAttribute('data-role-id');
        const roleName = btn.getAttribute('data-role-name');
        document.getElementById('edit-role-name').value = roleName;
        document.getElementById('edit-role-form').action = `/roles/${roleId}`;
    });

    @if($errors->has('name'))
    var createModal = new bootstrap.Modal(document.getElementById('createRoleModal'));
    createModal.show();
    @endif
</script>
@endsection
