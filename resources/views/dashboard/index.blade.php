@extends('layouts.main')

@section('title', 'Dashboard - CV Mugijaya Logistics ERP')

@section('styles')
<!-- ApexCharts JS -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('content')
<!-- Welcome Banner -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" id="welcome-title">Selamat Datang, {{ Auth::user()->name }}!</h4>
        <p class="text-muted text-sm m-0">Ringkasan aktivitas operasional pergudangan dan distribusi CV Mugijaya.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()"><i class="bi bi-arrow-clockwise me-1"></i> Refresh</button>
        <span class="badge bg-indigo py-2 px-3 fw-semibold" id="current-date-badge">15 Juni 2026</span>
    </div>
</div>

<!-- Metrics Widgets Row -->
<div class="row g-3 mb-4">
    <!-- Widget 1: Total Delivery Orders -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="erp-card stat-card card-accent-border">
            <div class="stat-card-info">
                <span class="stat-card-label">Delivery Orders</span>
                <span class="stat-card-value" id="count-do">0</span>
                <span class="stat-card-trend up">
                    <i class="bi bi-arrow-up-short"></i> +12% vs kemarin
                </span>
            </div>
            <div class="stat-card-icon accent">
                <i class="bi bi-truck-flatbed"></i>
            </div>
        </div>
    </div>
    <!-- Widget 2: Active Warehouses -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="erp-card stat-card">
            <div class="stat-card-info">
                <span class="stat-card-label">Total Gudang</span>
                <span class="stat-card-value" id="count-wh">0</span>
                <span class="text-muted text-xs">Kapasitas total aman</span>
            </div>
            <div class="stat-card-icon success">
                <i class="bi bi-house-gear-fill"></i>
            </div>
        </div>
    </div>
    <!-- Widget 3: Active Drivers -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="erp-card stat-card">
            <div class="stat-card-info">
                <span class="stat-card-label">Driver Aktif</span>
                <span class="stat-card-value" id="count-drivers">0</span>
                <span class="stat-card-trend text-indigo fw-semibold">
                    <span class="pulse-dot d-inline-block me-1"></span> On Delivery
                </span>
            </div>
            <div class="stat-card-icon info">
                <i class="bi bi-person-badge-fill"></i>
            </div>
        </div>
    </div>
    <!-- Widget 4: Low Stock Warnings -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="erp-card stat-card">
            <div class="stat-card-info">
                <span class="stat-card-label">Peringatan Stok</span>
                <span class="stat-card-value text-danger" id="count-alerts">0</span>
                <span class="text-danger text-xs fw-semibold">
                    <i class="bi bi-exclamation-triangle-fill"></i> Butuh Restock Segera
                </span>
            </div>
            <div class="stat-card-icon warning">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Visual Analytics Section -->
<div class="row g-4 mb-4">
    <!-- Delivery Traffic Chart -->
    <div class="col-12 col-lg-8">
        <div class="erp-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap">
                <h6 class="fw-bold m-0"><i class="bi bi-bar-chart-fill text-indigo me-2"></i>Tren Pengiriman DO Terkini</h6>
                <span class="text-xs text-muted">Aktivitas 7 Hari Terakhir</span>
            </div>
            <div id="chart-delivery-trends" style="min-height: 300px;"></div>
        </div>
    </div>
    <!-- Warehouse Capacity gauge list -->
    <div class="col-12 col-lg-4">
        <div class="erp-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill text-success me-2"></i>Kapasitas Gudang (Volumetrik)</h6>
            <div id="chart-warehouse-pie" style="min-height: 220px;" class="mb-3"></div>
            <div class="d-flex flex-column gap-2" id="warehouse-progress-bars">
                <!-- Dynamically generated progress bars for warehouses -->
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities Table & Quick Log alerts -->
<div class="row g-4">
    <!-- Recent DO -->
    <div class="col-12 col-xl-7">
        <div class="erp-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-journal-check text-info me-2"></i>Delivery Order Terbaru</h6>
                <a href="{{ route('delivery.index') }}" class="text-xs text-indigo text-decoration-none fw-semibold">Kelola DO <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle text-sm mb-0">
                    <thead>
                        <tr>
                            <th>Nomor DO</th>
                            <th>Tujuan</th>
                            <th>Driver</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="recent-do-tbody">
                        <!-- Dynamically generated recent DOs -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Recent Logs -->
    <div class="col-12 col-xl-5">
        <div class="erp-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-clock-history text-warning me-2"></i>Aktivitas Sistem Terbaru</h6>
                <a href="{{ route('audit.index') }}" class="text-xs text-indigo text-decoration-none fw-semibold">Audit Log <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="d-flex flex-column gap-3" id="recent-logs-list">
                <!-- Dynamically loaded audit logs -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Set current date
        const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date-badge').innerText = new Date().toLocaleDateString('id-ID', dateOptions);

        // Fetch mock database statistics
        const dos = db.getData('deliveryOrders');
        const warehouses = db.getData('warehouses');
        const drivers = db.getData('drivers');
        const stocks = db.getData('stocks');
        const products = db.getData('products');
        const logs = db.getData('auditLogs');

        // Set metric values
        document.getElementById('count-do').innerText = dos.length;
        document.getElementById('count-wh').innerText = warehouses.length;
        document.getElementById('count-drivers').innerText = drivers.filter(d => d.status === 'On Delivery').length;

        // Low stock warning count
        let lowStockCount = 0;
        stocks.forEach(stk => {
            if (stk.stockCurrent <= stk.stockMin) lowStockCount++;
        });
        document.getElementById('count-alerts').innerText = lowStockCount;

        // Determine chart theme
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const chartForeColor = isDark ? '#94A3B8' : '#475569';
        const chartGridColor = isDark ? '#334155' : '#E2E8F0';
        const chartStrokeColor = isDark ? '#1E293B' : '#FFFFFF';
        const chartMode = isDark ? 'dark' : 'light';

        // 1. ApexCharts Area Chart: Delivery Trends
        const trendOptions = {
            chart: {
                type: 'area',
                height: 290,
                toolbar: { show: false },
                background: 'transparent',
                foreColor: chartForeColor
            },
            series: [{
                name: 'Selesai Dikirim',
                data: [15, 23, 18, 28, 21, 30, 25]
            }, {
                name: 'Sedang Transit',
                data: [5, 8, 12, 6, 9, 11, 8]
            }],
            xaxis: {
                categories: ['09 Jun', '10 Jun', '11 Jun', '12 Jun', '13 Jun', '14 Jun', '15 Jun'],
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (value) { return value + " DO"; }
                }
            },
            colors: ['#16A34A', '#2563EB'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            grid: {
                borderColor: chartGridColor,
                strokeDashArray: 4
            },
            theme: { mode: chartMode },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            legend: { position: 'top', horizontalAlign: 'right' }
        };
        window.trendChart = new ApexCharts(document.querySelector("#chart-delivery-trends"), trendOptions);
        window.trendChart.render();

        // 2. ApexCharts Donut Chart: Warehouse Capacity share
        const whCapacities = warehouses.map(w => w.capacityUsed);
        
        const donutOptions = {
            chart: {
                type: 'donut',
                height: 220,
                background: 'transparent',
                foreColor: chartForeColor
            },
            series: whCapacities,
            labels: warehouses.map(w => w.code),
            colors: ['#2563EB', '#16A34A', '#F59E0B'],
            legend: { position: 'bottom', horizontalAlign: 'center' },
            dataLabels: { enabled: false },
            theme: { mode: chartMode },
            stroke: { colors: [chartStrokeColor] }
        };
        window.donutChart = new ApexCharts(document.querySelector("#chart-warehouse-pie"), donutOptions);
        window.donutChart.render();

        // Render progress indicators for capacity
        const progressContainer = document.getElementById('warehouse-progress-bars');
        warehouses.forEach(wh => {
            const percent = Math.round((wh.capacityUsed / wh.capacity) * 100);
            let progressColorClass = 'bg-primary-gradient';
            if (percent > 85) progressColorClass = 'bg-danger';
            else if (percent > 65) progressColorClass = 'bg-warning';
            else progressColorClass = 'bg-success';

            progressContainer.innerHTML += `
                <div>
                    <div class="d-flex justify-content-between text-xs mb-1">
                        <span class="fw-semibold text-secondary">${wh.name}</span>
                        <span class="text-muted">${wh.capacityUsed.toLocaleString()} / ${wh.capacity.toLocaleString()} m³ (${percent}%)</span>
                    </div>
                    <div class="progress capacity-progress">
                        <div class="progress-bar ${progressColorClass}" role="progressbar" style="width: ${percent}%"></div>
                    </div>
                </div>
            `;
        });

        // 3. Render Recent Delivery Orders in Table
        const recentDoTbody = document.getElementById('recent-do-tbody');
        const recentDos = dos.slice(0, 4);
        recentDos.forEach(doItem => {
            const driverObj = drivers.find(d => d.id === doItem.driverId);
            const driverName = driverObj ? driverObj.name : '<span class="text-muted">Unassigned</span>';
            
            let badgeClass = 'bg-secondary';
            if (doItem.status === 'Delivered') badgeClass = 'bg-success text-success bg-opacity-10 border border-success border-opacity-30';
            else if (doItem.status === 'In Transit') badgeClass = 'bg-info text-info bg-opacity-10 border border-info border-opacity-30';
            else if (doItem.status === 'Pending Validation') badgeClass = 'bg-warning text-warning bg-opacity-10 border border-warning border-opacity-30';
            else if (doItem.status === 'Prepared') badgeClass = 'bg-primary text-primary bg-opacity-10 border border-primary border-opacity-30';
            
            recentDoTbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-primary">${doItem.doNumber}</td>
                    <td class="text-truncate" style="max-width: 180px;">${doItem.destination}</td>
                    <td>${driverName}</td>
                    <td>${doItem.date}</td>
                    <td><span class="badge ${badgeClass} text-xs">${doItem.status}</span></td>
                </tr>
            `;
        });

        // 4. Render Recent Audit Logs
        const recentLogsList = document.getElementById('recent-logs-list');
        const recentLogs = logs.slice(0, 4);
        recentLogs.forEach(log => {
            let textClass = 'text-primary';
            let iconClass = 'bi-info-circle';
            if (log.type.includes('Stock')) { textClass = 'text-info'; iconClass = 'bi-box'; }
            else if (log.type.includes('QC')) { textClass = 'text-success'; iconClass = 'bi-patch-check'; }
            else if (log.type.includes('Login')) { textClass = 'text-warning'; iconClass = 'bi-person-circle'; }

            const timeStr = new Date(log.timestamp).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            recentLogsList.innerHTML += `
                <div class="d-flex align-items-start gap-3 border-bottom border-light-dark pb-2 last-no-border">
                    <div class="bg-light-dark p-2 rounded text-indigo">
                        <i class="bi ${iconClass}"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between mb-0.5">
                            <span class="fw-bold text-sm text-primary">${log.user}</span>
                            <span class="text-xs text-muted">${timeStr}</span>
                        </div>
                        <p class="text-xs text-secondary mb-0 text-truncate">${log.details}</p>
                    </div>
                </div>
            `;
        });
    });

    // Theme-aware chart refresh
    function refreshChartsTheme(theme) {
        const isDark = theme === 'dark';
        const fgColor = isDark ? '#94A3B8' : '#475569';
        const gridColor = isDark ? '#334155' : '#E2E8F0';
        const strokeColor = isDark ? '#1E293B' : '#FFFFFF';
        
        if (window.trendChart) {
            window.trendChart.updateOptions({
                chart: { foreColor: fgColor },
                grid: { borderColor: gridColor },
                theme: { mode: theme }
            });
        }
        if (window.donutChart) {
            window.donutChart.updateOptions({
                chart: { foreColor: fgColor },
                theme: { mode: theme },
                stroke: { colors: [strokeColor] }
            });
        }
    }
</script>
@endsection
