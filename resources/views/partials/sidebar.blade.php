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
        <span class="menu-label">Menu Utama</span>
        <a href="{{ route('dashboard') }}" class="sidebar-link nav-dash {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>
        
        <span class="menu-label">Manajemen Gudang</span>
        <a href="{{ route('warehouse.index') }}" class="sidebar-link nav-wh {{ Request::routeIs('warehouse.index') ? 'active' : '' }}">
            <i class="bi bi-house-gear-fill"></i>
            <span>Gudang</span>
        </a>
        <a href="{{ route('category.index') }}" class="sidebar-link nav-cat {{ Request::routeIs('category.index') ? 'active' : '' }}">
            <i class="bi bi-tags-fill"></i>
            <span>Kategori Produk</span>
        </a>
        <a href="{{ route('product.index') }}" class="sidebar-link nav-prd {{ Request::routeIs('product.index') ? 'active' : '' }}">
            <i class="bi bi-boxes"></i>
            <span>Produk / Barang</span>
        </a>
        <a href="{{ route('inventory.index') }}" class="sidebar-link nav-stk {{ Request::routeIs('inventory.index') ? 'active' : '' }}">
            <i class="bi bi-stack"></i>
            <span>Stok & Mutasi</span>
        </a>
        <a href="{{ route('warehouse.validation') }}" class="sidebar-link nav-val {{ Request::routeIs('warehouse.validation') ? 'active' : '' }}">
            <i class="bi bi-patch-check-fill"></i>
            <span>Validasi QC</span>
        </a>
        <a href="{{ route('warehouse.loading') }}" class="sidebar-link nav-load {{ Request::routeIs('warehouse.loading') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-up"></i>
            <span>Loading Barang</span>
        </a>
        
        <span class="menu-label">Distribusi & Logistik</span>
        <a href="{{ route('delivery.index') }}" class="sidebar-link nav-do {{ Request::routeIs('delivery.index') || Request::routeIs('delivery.detail') || Request::routeIs('delivery.surat-jalan') ? 'active' : '' }}">
            <i class="bi bi-truck-flatbed"></i>
            <span>Delivery Order</span>
        </a>
        <a href="{{ route('delivery.assign-driver') }}" class="sidebar-link nav-asg {{ Request::routeIs('delivery.assign-driver') ? 'active' : '' }}">
            <i class="bi bi-person-plus-fill"></i>
            <span>Assign Driver</span>
        </a>
        <a href="{{ route('delivery.monitoring') }}" class="sidebar-link nav-mon {{ Request::routeIs('delivery.monitoring') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Monitoring Kiriman</span>
        </a>
        <a href="{{ route('driver.index') }}" class="sidebar-link nav-drv {{ Request::routeIs('driver.index') || Request::routeIs('vehicle.index') ? 'active' : '' }}">
            <i class="bi bi-person-badge-fill"></i>
            <span>Driver & Armada</span>
        </a>
        <a href="{{ route('pod.index') }}" class="sidebar-link nav-pod {{ Request::routeIs('pod.index') ? 'active' : '' }}">
            <i class="bi bi-card-checklist"></i>
            <span>Proof of Delivery</span>
        </a>
        <a href="{{ route('tracking.index') }}" class="sidebar-link nav-gps {{ Request::routeIs('tracking.index') ? 'active' : '' }}">
            <i class="bi bi-geo-alt-fill"></i>
            <span>Pelacakan GPS</span>
        </a>
        <a href="{{ route('delivery.incident') }}" class="sidebar-link nav-inc {{ Request::routeIs('delivery.incident') ? 'active' : '' }}">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>Laporan Insiden</span>
        </a>

        <span class="menu-label">Sistem</span>
        <a href="{{ route('dashboard.users') }}" class="sidebar-link nav-usr {{ Request::routeIs('dashboard.users') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>Manajemen User</span>
        </a>
        <a href="{{ route('audit.index') }}" class="sidebar-link nav-aud {{ Request::routeIs('audit.index') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Audit Log</span>
        </a>
    </div>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <img id="sidebar-user-avatar"
                 src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4B6EF5&color=fff&size=80&rounded=true"
                 alt="{{ Auth::user()->name }}">
            <div class="sidebar-user-info">
                <div class="sidebar-user-name" id="sidebar-user-name">{{ Auth::user()->name }}</div>
                <div class="sidebar-user-role" id="sidebar-user-role" style="font-size: 0.7rem; opacity: 0.7;">{{ Auth::user()->email }}</div>
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

