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
        <!-- Theme Toggle -->
        <div class="btn-icon-wrapper" id="theme-toggle" title="Toggle Theme" style="font-size: 1.1rem; line-height: 1;">
            <i class="bi bi-moon" id="theme-moon-icon"></i>
            <i class="bi bi-sun d-none" id="theme-sun-icon"></i>
        </div>

        <!-- Notifications Dropdown -->
        <div class="dropdown">
            <div class="btn-icon-wrapper" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                <span class="notif-badge"></span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end p-2 border-light-dark bg-secondary shadow-lg" style="width: 320px;">
                <div class="d-flex align-items-center justify-content-between p-2 mb-1 border-bottom border-light-dark">
                    <h6 class="m-0 text-primary fw-bold">Notifikasi Terkini</h6>
                    <span class="badge bg-indigo text-xs">Baru</span>
                </div>
                <div id="notif-items-container">
                    <!-- Loaded dynamically via layout.blade.php js -->
                </div>
                <div class="text-center pt-2">
                    <a href="{{ route('audit.index') }}" class="text-xs text-indigo text-decoration-none fw-semibold">Lihat Semua Log</a>
                </div>
            </ul>
        </div>

        <!-- Quick Actions Dropdown -->
        <div class="dropdown">
            <button class="btn btn-sm btn-primary-gradient d-flex align-items-center gap-1 dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-plus-circle"></i>
                <span class="d-none d-sm-inline">Aksi Cepat</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-light-dark bg-secondary">
                <li><a class="dropdown-item" href="{{ route('delivery.index') }}?action=new"><i class="bi bi-file-earmark-plus me-2"></i>Buat DO Baru</a></li>
                <li><a class="dropdown-item" href="{{ route('inventory.index') }}?action=adjust"><i class="bi bi-arrow-left-right me-2"></i>Mutasi Stok</a></li>
                <li><hr class="dropdown-divider border-light-dark"></li>
                <li><a class="dropdown-item" href="{{ route('product.index') }}?action=new"><i class="bi bi-plus-square me-2"></i>Tambah Produk</a></li>
            </ul>
        </div>
    </div>
</nav>
