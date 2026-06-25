<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="text-decoration-none">
            <div class="brand-logo">
                <i class="bi bi-box-seam-fill"></i>
                <span>Mugijaya ERP</span>
            </div>
        </a>
    </div>
    <div class="sidebar-menu">

        {{-- ============================================
             MENU UTAMA (semua role)
        ============================================ --}}
        @can('dashboard.view')
        <span class="menu-label">Menu Utama</span>
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>
        @endcan

        {{-- ============================================
             MANAJEMEN GUDANG (Owner, Admin, Staff Gudang)
        ============================================ --}}
        @canany(['warehouse.view', 'category.view', 'product.view', 'inventory.view'])
        <span class="menu-label">Manajemen Gudang</span>
        @endcanany

        @can('warehouse.view')
        <a href="{{ route('warehouse.index') }}" class="sidebar-link {{ Request::routeIs('warehouse.*') ? 'active' : '' }}">
            <i class="bi bi-house-gear-fill"></i>
            <span>Gudang</span>
        </a>
        @endcan

        @can('category.view')
        <a href="{{ route('category.index') }}" class="sidebar-link {{ Request::routeIs('category.*') ? 'active' : '' }}">
            <i class="bi bi-tags-fill"></i>
            <span>Kategori Produk</span>
        </a>
        @endcan

        @can('product.view')
        <a href="{{ route('product.index') }}" class="sidebar-link {{ Request::routeIs('product.*') ? 'active' : '' }}">
            <i class="bi bi-boxes"></i>
            <span>Produk / Barang</span>
        </a>
        @endcan

        @can('inventory.view')
        <a href="{{ route('inventory.index') }}" class="sidebar-link {{ Request::routeIs('inventory.index') ? 'active' : '' }}">
            <i class="bi bi-stack"></i>
            <span>Stok & Mutasi</span>
        </a>
        @endcan

        {{-- QC & Loading: Staff Gudang + Owner + Admin --}}
        @hasanyrole(['Owner', 'Admin', 'Staff Gudang'])
        <a href="{{ route('warehouse.validation') }}" class="sidebar-link {{ Request::routeIs('warehouse.validation') ? 'active' : '' }}">
            <i class="bi bi-patch-check-fill"></i>
            <span>Validasi QC</span>
        </a>
        <a href="{{ route('warehouse.loading') }}" class="sidebar-link {{ Request::routeIs('warehouse.loading') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-up"></i>
            <span>Loading Barang</span>
        </a>
        @endhasanyrole

        {{-- ============================================
             DISTRIBUSI & LOGISTIK (Owner, Admin, Driver)
        ============================================ --}}
        @canany(['delivery.view', 'driver.view', 'gps.view'])
        <span class="menu-label">Distribusi & Logistik</span>
        @endcanany

        @can('delivery.view')
        <a href="{{ route('delivery.index') }}" class="sidebar-link {{ Request::routeIs('delivery.index') ? 'active' : '' }}">
            <i class="bi bi-truck-flatbed"></i>
            <span>Delivery Order</span>
        </a>
        @endcan

        @canany(['delivery.approve', 'delivery.create'])
        <a href="{{ route('delivery.assign-driver') }}" class="sidebar-link {{ Request::routeIs('delivery.assign-driver') ? 'active' : '' }}">
            <i class="bi bi-person-plus-fill"></i>
            <span>Assign Driver</span>
        </a>
        <a href="{{ route('delivery.monitoring') }}" class="sidebar-link {{ Request::routeIs('delivery.monitoring') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Monitoring Kiriman</span>
        </a>
        @endcanany

        @can('driver.view')
        <a href="{{ route('driver.index') }}" class="sidebar-link {{ Request::routeIs('driver.index') ? 'active' : '' }}">
            <i class="bi bi-person-badge-fill"></i>
            <span>Driver & Armada</span>
        </a>
        @endcan

        @hasrole('Driver')
        <a href="{{ route('pod.index') }}" class="sidebar-link {{ Request::routeIs('pod.index') ? 'active' : '' }}">
            <i class="bi bi-card-checklist"></i>
            <span>Proof of Delivery</span>
        </a>
        @endhasrole

        @can('gps.view')
        <a href="{{ route('tracking.index') }}" class="sidebar-link {{ Request::routeIs('tracking.index') ? 'active' : '' }}">
            <i class="bi bi-geo-alt-fill"></i>
            <span>Pelacakan GPS</span>
        </a>
        @endcan

        @hasanyrole(['Owner', 'Admin'])
        <a href="{{ route('delivery.incident') }}" class="sidebar-link {{ Request::routeIs('delivery.incident') ? 'active' : '' }}">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>Laporan Insiden</span>
        </a>
        @endhasanyrole

        {{-- ============================================
             SISTEM (Owner only)
        ============================================ --}}
        @can('user.view')
        <span class="menu-label">Sistem</span>
        <a href="{{ route('users.index') }}" class="sidebar-link {{ Request::routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>Manajemen User</span>
        </a>
        <a href="{{ route('roles.index') }}" class="sidebar-link {{ Request::routeIs('roles.*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock-fill"></i>
            <span>Manajemen Role</span>
        </a>
        @endcan

        @can('audit.view')
        @cannot('user.view')
        <span class="menu-label">Sistem</span>
        @endcannot
        <a href="{{ route('audit.index') }}" class="sidebar-link {{ Request::routeIs('audit.index') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Audit Log</span>
        </a>
        @endcan

    </div>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <img id="sidebar-user-avatar"
                 src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4B6EF5&color=fff&size=80&rounded=true"
                 alt="{{ Auth::user()->name }}">
            <div class="sidebar-user-info">
                <div class="sidebar-user-name" id="sidebar-user-name">{{ Auth::user()->name }}</div>
                <div class="sidebar-user-role" id="sidebar-user-role" style="font-size: 0.7rem; opacity: 0.7;">
                    {{ Auth::user()->roles->first()?->name ?? Auth::user()->email }}
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
                @csrf
            </form>
            <div class="sidebar-logout"
                 id="btn-logout"
                 title="Keluar dari Sistem"
                 onclick="document.getElementById('sidebar-logout-form').submit()"
                 style="cursor: pointer;">
                <i class="bi bi-box-arrow-right"></i>
            </div>
        </div>
    </div>
</aside>
