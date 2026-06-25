@extends('layouts.main')

@section('title', 'Audit Log Sistem - CV Mugijaya Logistics ERP')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-clock-history text-indigo me-2"></i>Audit Log Sistem</h4>
        <p class="text-muted text-sm m-0">Catatan riwayat aktivitas pengguna, perubahan data stok, status DO, dan akses login.</p>
    </div>
</div>

{{-- Filters & Search --}}
<div class="erp-card p-3 mb-4">
    <form method="GET" action="{{ route('audit.index') }}" class="row g-2 align-items-end">
        {{-- Search Input --}}
        <div class="col-md-3">
            <label class="form-label text-xs text-muted mb-1">Kata Kunci Pencarian</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-transparent border-end-0 border-light-dark"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 border-light-dark ps-0 bg-transparent text-white text-xs"
                       placeholder="Cari deskripsi, SKU, data..." value="{{ $search ?? '' }}">
            </div>
        </div>

        {{-- User Filter --}}
        <div class="col-md-2">
            <label class="form-label text-xs text-muted mb-1">Pengguna</label>
            <select name="user_id" class="form-select form-select-sm bg-transparent text-white border-light-dark text-xs">
                <option value="all" class="bg-dark text-white">Semua Pengguna</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ ($causer_id ?? 'all') == $u->id ? 'selected' : '' }} class="bg-dark text-white">
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Module Filter --}}
        <div class="col-md-2">
            <label class="form-label text-xs text-muted mb-1">Modul / Fitur</label>
            <select name="module" class="form-select form-select-sm bg-transparent text-white border-light-dark text-xs">
                <option value="all" class="bg-dark text-white">Semua Modul</option>
                @foreach($modules as $key => $label)
                    <option value="{{ $key }}" {{ ($module ?? 'all') == $key ? 'selected' : '' }} class="bg-dark text-white">
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Date Start --}}
        <div class="col-md-2 col-6">
            <label class="form-label text-xs text-muted mb-1">Tanggal Mulai</label>
            <input type="date" name="date_start" class="form-control form-control-sm bg-transparent text-white border-light-dark text-xs"
                   value="{{ $date_start ?? '' }}">
        </div>

        {{-- Date End --}}
        <div class="col-md-2 col-6">
            <label class="form-label text-xs text-muted mb-1">Tanggal Selesai</label>
            <input type="date" name="date_end" class="form-control form-control-sm bg-transparent text-white border-light-dark text-xs"
                   value="{{ $date_end ?? '' }}">
        </div>

        {{-- Filter Buttons --}}
        <div class="col-md-1 d-flex gap-2">
            <button type="submit" class="btn btn-primary-gradient btn-sm w-100 py-1.5" title="Terapkan Filter">
                <i class="bi bi-filter"></i> Filter
            </button>
            @if($search || ($causer_id && $causer_id !== 'all') || ($module && $module !== 'all') || $date_start || $date_end)
                <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary btn-sm py-1.5" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Audit Logs Table --}}
<div class="erp-card">
    <div class="table-responsive">
        <table class="table align-middle text-sm mb-0">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th width="150">Waktu</th>
                    <th width="180">Pengguna</th>
                    <th width="140">Role</th>
                    <th width="120">Aksi (Event)</th>
                    <th width="140">Modul</th>
                    <th>Deskripsi</th>
                    <th width="90" class="text-center">Perubahan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                @php
                    // Determine event badge class
                    $eventBadgeClass = match($activity->event) {
                        'login', 'created', 'stock_in', 'activate' => 'bg-success text-success bg-opacity-10 border border-success border-opacity-20',
                        'logout' => 'bg-secondary text-secondary bg-opacity-10 border border-secondary border-opacity-20',
                        'password_reset', 'password_change', 'adjustment' => 'bg-warning text-warning bg-opacity-10 border border-warning border-opacity-20',
                        'updated', 'change_role' => 'bg-primary text-primary bg-opacity-10 border border-primary border-opacity-20',
                        'deleted', 'stock_out', 'deactivate' => 'bg-danger text-danger bg-opacity-10 border border-danger border-opacity-20',
                        'transfer' => 'bg-info text-info bg-opacity-10 border border-info border-opacity-20',
                        default => 'bg-secondary text-secondary bg-opacity-10 border border-secondary border-opacity-20',
                    };

                    // Determine module badge class
                    $moduleBadgeClass = match($activity->log_name) {
                        'auth' => 'bg-primary text-primary bg-opacity-10 border border-primary border-opacity-20',
                        'warehouse' => 'bg-warning text-warning bg-opacity-10 border border-warning border-opacity-20',
                        'category' => 'bg-info text-info bg-opacity-10 border border-info border-opacity-20',
                        'product' => 'bg-success text-success bg-opacity-10 border border-success border-opacity-20',
                        'inventory' => 'style-badge-inventory',
                        'user_management' => 'bg-danger text-danger bg-opacity-10 border border-danger border-opacity-20',
                        default => 'bg-secondary text-secondary bg-opacity-10 border border-secondary border-opacity-20',
                    };

                    // Eagerly resolve causer details
                    $causer = $activity->causer;
                    $userName = $causer ? $causer->name : ($activity->causer_id ? "Deleted User (ID: {$activity->causer_id})" : "System");
                    $userRole = $causer ? ($causer->roles->first()?->name ?? 'No Role') : ($activity->causer_id ? '-' : 'System');

                    // Determine user role badge color
                    $roleBadgeClass = match($userRole) {
                        'Owner'       => 'bg-warning text-warning',
                        'Admin'       => 'bg-primary text-primary',
                        'Staff Gudang'=> 'bg-success text-success',
                        'Driver'      => 'bg-info text-info',
                        default       => 'bg-secondary text-secondary',
                    };
                @endphp
                <tr>
                    <td class="text-muted">{{ $loop->iteration + ($activities->currentPage() - 1) * $activities->perPage() }}</td>
                    <td class="font-monospace text-xs text-muted">{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="fw-semibold text-primary-gradient">
                        <i class="bi bi-person text-muted me-1"></i>{{ $userName }}
                    </td>
                    <td>
                        @if($userRole === 'System')
                            <span class="badge bg-secondary bg-opacity-15 border border-secondary border-opacity-25 fw-semibold px-2">System</span>
                        @elseif($userRole === '-')
                            <span class="text-muted">-</span>
                        @else
                            <span class="badge {{ $roleBadgeClass }} bg-opacity-15 border border-opacity-25 fw-semibold px-2">
                                {{ $userRole }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $eventBadgeClass }} text-xs px-2 py-1 uppercase-first">
                            {{ str_replace('_', ' ', $activity->event ?? 'action') }}
                        </span>
                    </td>
                    <td>
                        @if($activity->log_name === 'inventory')
                            <span class="badge bg-opacity-10 px-2 py-1 text-xs" style="background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.25);">
                                {{ $modules[$activity->log_name] ?? $activity->log_name }}
                            </span>
                        @else
                            <span class="badge {{ $moduleBadgeClass }} text-xs px-2 py-1">
                                {{ $modules[$activity->log_name] ?? $activity->log_name }}
                            </span>
                        @endif
                    </td>
                    <td class="text-wrap text-muted" style="max-width: 350px;">{{ $activity->description }}</td>
                    <td class="text-center">
                        @if(!empty($activity->properties->toArray()))
                        <button class="btn btn-sm btn-outline-primary py-0.5 px-2 text-xs"
                                data-bs-toggle="modal"
                                data-bs-target="#auditDetailModal"
                                data-old="{{ json_encode($activity->properties['old'] ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}"
                                data-new="{{ json_encode($activity->properties['attributes'] ?? $activity->properties['new'] ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}"
                                data-full="{{ json_encode($activity->properties ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}">
                            <i class="bi bi-eye me-1"></i>Detail
                        </button>
                        @else
                        <span class="text-muted text-xs italic">Tidak ada</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-clock display-6 d-block mb-2 opacity-25"></i>
                        Tidak ada log aktivitas sistem ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($activities->hasPages())
    <div class="p-3 border-top border-light-dark">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <small class="text-muted">
                Menampilkan {{ $activities->firstItem() }}–{{ $activities->lastItem() }} dari {{ $activities->total() }} log
            </small>
            {{ $activities->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="auditDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="modal-header border-bottom border-light-dark">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-info-circle me-2 text-indigo"></i>Detail Perubahan Data
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-xs text-muted mb-1">Nilai Lama (Sebelum)</label>
                        <pre class="p-3 rounded text-xs overflow-auto border border-light-dark" id="detail-old" style="max-height: 350px; color: #f87171; background: #0f172a; border-color: #334155 !important;"></pre>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-xs text-muted mb-1">Nilai Baru (Sesudah)</label>
                        <pre class="p-3 rounded text-xs overflow-auto border border-light-dark" id="detail-new" style="max-height: 350px; color: #4ade80; background: #0f172a; border-color: #334155 !important;"></pre>
                    </div>
                </div>
                <div class="mt-2">
                    <label class="form-label text-xs text-muted mb-1">Seluruh Metadata / Parameter</label>
                    <pre class="p-3 rounded text-xs overflow-auto border border-light-dark" id="detail-full" style="max-height: 250px; color: #e2e8f0; background: #0f172a; border-color: #334155 !important;"></pre>
                </div>
            </div>
            <div class="modal-footer border-top border-light-dark">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const auditDetailModal = document.getElementById('auditDetailModal');
        if (auditDetailModal) {
            auditDetailModal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                const oldVal = btn.getAttribute('data-old');
                const newVal = btn.getAttribute('data-new');
                const fullVal = btn.getAttribute('data-full');

                const formatJSON = (val) => {
                    try {
                        if (!val || val === 'null' || val === '[]') return 'Tidak ada data.';
                        const parsed = JSON.parse(val);
                        if (Object.keys(parsed).length === 0) return 'Tidak ada data.';
                        return JSON.stringify(parsed, null, 4);
                    } catch(e) {
                        return val || 'Tidak ada data.';
                    }
                };

                document.getElementById('detail-old').textContent = formatJSON(oldVal);
                document.getElementById('detail-new').textContent = formatJSON(newVal);
                document.getElementById('detail-full').textContent = formatJSON(fullVal);
            });
        }
    });
</script>
@endsection
