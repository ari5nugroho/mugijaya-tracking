@extends('layouts.main')

@section('title', 'Manajemen User - CV Mugijaya Logistics ERP')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-people-fill text-indigo me-2"></i>Manajemen User</h4>
        <p class="text-muted text-sm m-0">Kelola daftar pengguna dan assignment role mereka.</p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-shield-lock-fill me-1"></i> Kelola Role
    </a>
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

{{-- Search --}}
<div class="erp-card p-3 mb-4">
    <form method="GET" action="{{ route('users.index') }}" class="d-flex gap-2">
        <div class="input-group" style="max-width: 380px;">
            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0 ps-0"
                   placeholder="Cari nama atau email..." value="{{ $search ?? '' }}">
        </div>
        <button type="submit" class="btn btn-primary-gradient btn-sm px-3">Cari</button>
        @if($search)
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
        @endif
    </form>
</div>

{{-- Users Table --}}
<div class="erp-card">
    <div class="table-responsive">
        <table class="table align-middle text-sm mb-0">
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>Role Saat Ini</th>
                    <th>Status</th>
                    <th>Bergabung</th>
                    <th width="120" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="text-muted">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4B6EF5&color=fff&size=64&rounded=true"
                                 width="36" height="36" class="rounded-circle" alt="{{ $user->name }}">
                            <div>
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                <span class="badge bg-primary bg-opacity-15 text-primary" style="font-size:0.65rem;">Anda</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-muted">{{ $user->email }}</td>
                    <td>
                        @php $roleName = $user->roles->first()?->name ?? null; @endphp
                        @if($roleName)
                            @php
                                $badgeClass = match($roleName) {
                                    'Owner'       => 'bg-warning text-warning',
                                    'Admin'       => 'bg-primary text-primary',
                                    'Staff Gudang'=> 'bg-success text-success',
                                    'Driver'      => 'bg-info text-info',
                                    default       => 'bg-secondary text-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} bg-opacity-15 border border-opacity-25 fw-semibold px-2">
                                {{ $roleName }}
                            </span>
                        @else
                            <span class="text-muted fst-italic text-xs">Belum ada role</span>
                        @endif
                    </td>
                    <td>
                        @if($user->is_active ?? true)
                            <span class="badge bg-success text-success bg-opacity-15 border border-success border-opacity-25 fw-semibold px-2">
                                <i class="bi bi-check-circle me-1"></i>Aktif
                            </span>
                        @else
                            <span class="badge bg-danger text-danger bg-opacity-15 border border-danger border-opacity-25 fw-semibold px-2">
                                <i class="bi bi-x-circle me-1"></i>Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="text-muted text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#assignRoleModal"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}"
                                    data-user-role="{{ $roleName ?? '' }}"
                                    title="Assign / Ubah Role">
                                <i class="bi bi-person-gear"></i>
                            </button>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.toggleStatus', $user) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status aktif user ini?')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm {{ ($user->is_active ?? true) ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                        title="{{ ($user->is_active ?? true) ? 'Nonaktifkan User' : 'Aktifkan User' }}">
                                    <i class="bi {{ ($user->is_active ?? true) ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                </button>
                            </form>
                            @else
                            <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak dapat menonaktifkan akun sendiri">
                                <i class="bi bi-person-x"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-people display-6 d-block mb-2 opacity-25"></i>
                        Tidak ada user ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="p-3 border-top border-light-dark">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <small class="text-muted">
                Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna
            </small>
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>

{{-- Assign Role Modal --}}
<div class="modal fade" id="assignRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="modal-header border-bottom border-light-dark">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-person-gear me-2 text-indigo"></i>Assign / Ubah Role
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="assign-role-form">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p class="text-muted text-sm mb-3">
                        Pilih role untuk <strong id="modal-user-name" style="color: var(--text-primary);"></strong>:
                    </p>

                    <div class="d-flex flex-column gap-2">
                        @foreach($roles as $role)
                        @php
                            $iconMap = [
                                'Owner'       => 'bi-star-fill text-warning',
                                'Admin'       => 'bi-shield-fill-check text-primary',
                                'Staff Gudang'=> 'bi-house-gear-fill text-success',
                                'Driver'      => 'bi-truck-front-fill text-info',
                            ];
                            $icon = $iconMap[$role->name] ?? 'bi-person-fill text-secondary';
                        @endphp
                        <label class="d-flex align-items-center gap-3 p-3 rounded cursor-pointer role-option"
                               style="border: 1px solid var(--border-color); cursor: pointer; transition: all 0.15s;"
                               for="role_{{ $role->id }}">
                            <input type="radio" name="role" id="role_{{ $role->id }}"
                                   value="{{ $role->name }}" class="form-check-input mt-0 flex-shrink-0">
                            <div>
                                <div class="fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi {{ $icon }}"></i>
                                    {{ $role->name }}
                                </div>
                                <div class="text-muted text-xs">{{ $role->permissions->count() }} permissions</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top border-light-dark">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary-gradient">
                        <i class="bi bi-check2 me-1"></i>Simpan Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const assignRoleModal = document.getElementById('assignRoleModal');
    assignRoleModal.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        const userId   = btn.getAttribute('data-user-id');
        const userName = btn.getAttribute('data-user-name');
        const userRole = btn.getAttribute('data-user-role');

        document.getElementById('modal-user-name').textContent = userName;
        document.getElementById('assign-role-form').action = `/dashboard/users/${userId}/role`;

        // Pre-select current role
        if (userRole) {
            const radios = document.querySelectorAll('input[name="role"]');
            radios.forEach(r => {
                r.checked = (r.value === userRole);
            });
        }
    });
</script>
@endsection
