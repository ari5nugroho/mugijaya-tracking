<nav class="top-navbar">
    <div class="navbar-left">
        <button class="sidebar-toggle" id="sidebar-toggle-btn">
            <i class="bi bi-list"></i>
        </button>
        <div class="d-none d-md-flex align-items-center gap-2">
            <span class="pulse-badge">
                <span class="pulse-dot"></span>
                Live System
            </span>
            <span class="text-muted text-sm border-start border-light-dark ps-2">CV Mugijaya Logistics</span>
        </div>
    </div>

    <div class="navbar-right">
        {{-- Theme Toggle --}}
        <div class="btn-icon-wrapper" id="theme-toggle" title="Toggle Theme" style="font-size: 1.1rem; line-height: 1;">
            <i class="bi bi-moon" id="theme-moon-icon"></i>
            <i class="bi bi-sun d-none" id="theme-sun-icon"></i>
        </div>

        {{-- Notifications Dropdown --}}
        <div class="dropdown">
            <div class="btn-icon-wrapper" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                <span class="notif-badge"></span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end p-2 border-light-dark bg-secondary shadow-lg"
                style="width: 320px;">
                <div class="d-flex align-items-center justify-content-between p-2 mb-1 border-bottom border-light-dark">
                    <h6 class="m-0 text-primary fw-bold">Notifikasi Terkini</h6>
                    <span class="badge bg-indigo text-xs">Baru</span>
                </div>
                <div id="notif-items-container">
                    <!-- Loaded dynamically via layout.blade.php js -->
                </div>
                <div class="text-center pt-2">
                    <a href="{{ route('audit.index') }}"
                        class="text-xs text-indigo text-decoration-none fw-semibold">Lihat Semua Log</a>
                </div>
            </ul>
        </div>

        {{-- Quick Actions Dropdown --}}
        @canany(['delivery.create', 'inventory.stockin', 'product.create'])
            <div class="dropdown">
                <button class="btn btn-sm btn-primary-gradient d-flex align-items-center gap-1 dropdown-toggle"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-plus-circle"></i>
                    <span class="d-none d-sm-inline">Aksi Cepat</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-light-dark bg-secondary">
                    @can('delivery.create')
                        <li><a class="dropdown-item" href="{{ route('delivery.index') }}?action=new"><i
                                    class="bi bi-file-earmark-plus me-2"></i>Buat DO Baru</a></li>
                    @endcan
                    @can('inventory.stockin')
                        <li><a class="dropdown-item" href="{{ route('inventory.index') }}?action=adjust"><i
                                    class="bi bi-arrow-left-right me-2"></i>Mutasi Stok</a></li>
                    @endcan
                    @can('product.create')
                        <li>
                            <hr class="dropdown-divider border-light-dark">
                        </li>
                        <li><a class="dropdown-item" href="{{ route('product.index') }}?action=new"><i
                                    class="bi bi-plus-square me-2"></i>Tambah Produk</a></li>
                    @endcan
                </ul>
            </div>
        @endcanany

        {{-- User Profile Dropdown --}}
        <div class="dropdown">
            <button class="d-flex align-items-center gap-2 btn btn-sm"
                style="background: transparent; border: 1px solid var(--border-color, #30363D); border-radius: 8px; padding: 0.35rem 0.65rem;"
                data-bs-toggle="dropdown">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4B6EF5&color=fff&size=64&rounded=true"
                    width="26" height="26" class="rounded-circle" alt="{{ Auth::user()->name }}">
                <div class="d-none d-lg-block text-start" style="line-height: 1.2;">
                    <div
                        style="font-size: 0.78rem; font-weight: 600; color: var(--text-primary, #E6EDF3); max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ Auth::user()->name }}
                    </div>
                    @php $roleName = Auth::user()->roles->first()?->name; @endphp
                    @if ($roleName)
                        @php
                            $badgeColor = match ($roleName) {
                                'Owner' => '#EAB308',
                                'Kepala Produksi' => '#4B6EF5',
                                'Mandor' => '#16A34A',
                                'Kepala Lapangan' => '#F97316',
                                'Driver' => '#06B6D4',
                                default => '#64748B',
                            };
                        @endphp
                        <span
                            style="font-size: 0.6rem; font-weight: 700; color: {{ $badgeColor }}; letter-spacing: 0.5px; text-transform: uppercase;">
                            {{ $roleName }}
                        </span>
                    @endif
                </div>
                <i class="bi bi-chevron-down text-muted d-none d-lg-block" style="font-size: 0.65rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-light-dark bg-secondary" style="min-width: 220px;">
                {{-- User Info Header --}}
                <li>
                    <div class="px-3 py-2 border-bottom border-light-dark">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4B6EF5&color=fff&size=64&rounded=true"
                                width="36" height="36" class="rounded-circle flex-shrink-0"
                                alt="{{ Auth::user()->name }}">
                            <div class="min-w-0">
                                <div class="fw-semibold text-sm"
                                    style="color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">
                                    {{ Auth::user()->name }}</div>
                                <div class="text-muted text-xs"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">
                                    {{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        @if ($roleName)
                            <span class="badge text-xs px-2 py-1 fw-bold"
                                style="background: {{ $badgeColor }}22; color: {{ $badgeColor }}; border: 1px solid {{ $badgeColor }}44;">
                                {{ strtoupper($roleName) }}
                            </span>
                        @endif
                    </div>
                </li>
                <li><a class="dropdown-item text-sm py-2" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person-circle me-2 text-indigo"></i>Edit Profil
                    </a></li>
                @can('user.view')
                    <li><a class="dropdown-item text-sm py-2" href="{{ route('users.index') }}">
                            <i class="bi bi-people-fill me-2 text-success"></i>Manajemen User
                        </a></li>
                @endcan
                <li>
                    <hr class="dropdown-divider border-light-dark">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-sm py-2 text-danger w-100 text-start">
                            <i class="bi bi-box-arrow-right me-2"></i>Keluar dari Sistem
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
