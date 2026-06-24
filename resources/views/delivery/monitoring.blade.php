@extends('layouts.main')

@section('title', 'Monitoring Kiriman - CV Mugijaya Logistics ERP')

@section('styles')
<!-- ApexCharts JS -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Monitoring Pengiriman & Distribusi</h4>
        <p class="text-muted text-sm m-0">Pantau pergerakan armada secara real-time, pantau efisiensi waktu, dan tangani insiden lapangan.</p>
    </div>
    <button class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()">
        <i class="bi bi-arrow-clockwise me-1"></i> Refresh Data
    </button>
</div>

<!-- Stats Summary Row -->
<div class="row g-3 mb-4">
    <!-- Stat 1: In Transit -->
    <div class="col-6 col-md-3">
        <div class="erp-card stat-card card-accent-border">
            <div class="stat-card-info">
                <span class="stat-card-label">Sedang Dikirim</span>
                <span class="stat-card-value text-info" id="mon-in-transit">0</span>
                <span class="text-muted text-xs">Armada di jalan</span>
            </div>
            <div class="stat-card-icon info">
                <i class="bi bi-truck-flatbed"></i>
            </div>
        </div>
    </div>
    <!-- Stat 2: Delivered Today -->
    <div class="col-6 col-md-3">
        <div class="erp-card stat-card">
            <div class="stat-card-info">
                <span class="stat-card-label">Selesai Dikirim</span>
                <span class="stat-card-value text-success" id="mon-delivered">0</span>
                <span class="text-muted text-xs">Hari ini</span>
            </div>
            <div class="stat-card-icon success">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
    </div>
    <!-- Stat 3: Unassigned -->
    <div class="col-6 col-md-3">
        <div class="erp-card stat-card">
            <div class="stat-card-info">
                <span class="stat-card-label">Tunda Assign</span>
                <span class="stat-card-value text-warning" id="mon-unassigned">0</span>
                <span class="text-muted text-xs">Menunggu driver</span>
            </div>
            <div class="stat-card-icon warning">
                <i class="bi bi-person-exclamation"></i>
            </div>
        </div>
    </div>
    <!-- Stat 4: Incidents -->
    <div class="col-6 col-md-3">
        <div class="erp-card stat-card">
            <div class="stat-card-info">
                <span class="stat-card-label">Laporan Insiden</span>
                <span class="stat-card-value text-danger" id="mon-incidents">0</span>
                <span class="text-danger text-xs fw-semibold">Butuh resolusi</span>
            </div>
            <div class="stat-card-icon warning bg-danger-glow text-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Column 1: Speed/Duration chart -->
    <div class="col-12 col-lg-7">
        <div class="erp-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill text-indigo me-2"></i>Persentase Pengiriman Tepat Waktu (On-Time Performance)</h6>
            <div id="chart-ontime-trends" style="min-height: 280px;"></div>
        </div>
    </div>
    <!-- Column 2: Status breakdown chart -->
    <div class="col-12 col-lg-5">
        <div class="erp-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill text-success me-2"></i>Komposisi Status Delivery Order</h6>
            <div id="chart-do-status" style="min-height: 280px;"></div>
        </div>
    </div>
</div>

<!-- Live Deliveries Table -->
<div class="erp-card p-4">
    <h6 class="fw-bold mb-3"><i class="bi bi-broadcast text-danger me-2"></i>Status Perjalanan Pengiriman Aktif (Real-Time In-Transit)</h6>
    <div class="table-responsive">
        <table class="table align-middle text-sm mb-0">
            <thead>
                <tr>
                    <th>Nomor DO</th>
                    <th>Driver / Armada</th>
                    <th>Tujuan Pengiriman</th>
                    <th>Tanggal Kirim</th>
                    <th>Estimasi Perjalanan</th>
                    <th>Progres Muatan</th>
                    <th class="text-end">Aksi Operasional</th>
                </tr>
            </thead>
            <tbody id="transit-deliveries-tbody">
                <!-- Loaded dynamically -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dos = db.getData('deliveryOrders') || [];
        const drivers = db.getData('drivers') || [];
        const incidents = db.getData('incidents') || [];

        // 1. Calculate Metrics
        const inTransitCount = dos.filter(d => d.status === 'In Transit').length;
        const deliveredCount = dos.filter(d => d.status === 'Delivered').length;
        const unassignedCount = dos.filter(d => !d.driverId).length;
        const openIncidentsCount = incidents.filter(i => i.status !== 'Resolved').length;

        document.getElementById('mon-in-transit').innerText = inTransitCount;
        document.getElementById('mon-delivered').innerText = deliveredCount;
        document.getElementById('mon-unassigned').innerText = unassignedCount;
        document.getElementById('mon-incidents').innerText = openIncidentsCount;

        // 2. Populate Active Table (In Transit)
        const activeTbody = document.getElementById('transit-deliveries-tbody');
        activeTbody.innerHTML = '';

        const activeDos = dos.filter(d => d.status === 'In Transit');

        activeDos.forEach(d => {
            const drv = drivers.find(driverObj => driverObj.id === d.driverId);
            const drvName = drv ? drv.name : 'Unknown';
            const plate = drv ? drv.licensePlate : '-';
            const vehicle = drv ? drv.vehicleClass : '-';
            
            // Simulate some random journey progress percentage (e.g. 40% - 85%) for visual fidelity
            const mockProgress = Math.floor(Math.random() * 45) + 40;
            
            // Estimate time based on destination length
            const mockEst = d.destination.toLowerCase().includes('jakarta') ? '4 Jam' : (d.destination.toLowerCase().includes('surabaya') ? '6 Jam' : '1.5 Jam');

            activeTbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-primary">${d.doNumber}</td>
                    <td>
                        <div class="fw-semibold text-primary">${drvName}</div>
                        <div class="text-xs text-muted font-monospace">${plate} - ${vehicle}</div>
                    </td>
                    <td class="text-truncate" style="max-width: 180px;" title="${d.destination}">
                        ${d.destination.split(',')[0]}<br>
                        <span class="text-xs text-muted">${d.destination.split(',').slice(1).join(',')}</span>
                    </td>
                    <td>${d.date}</td>
                    <td><span class="badge bg-light-dark text-info"><i class="bi bi-clock me-1"></i>~ ${mockEst}</span></td>
                    <td style="width: 150px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress capacity-progress flex-grow-1" style="height: 6px;">
                                <div class="progress-bar progress-bar-glow bg-info" style="width: ${mockProgress}%"></div>
                            </div>
                            <span class="text-xxs text-muted">${mockProgress}%</span>
                        </div>
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('tracking.index') }}" class="btn btn-xs btn-outline-warning text-xs" title="Lacak Lokasi"><i class="bi bi-geo-alt-fill"></i> Lacak</a>
                            <a href="{{ route('delivery.incident') }}?doNumber=${d.doNumber}" class="btn btn-xs btn-outline-danger text-xs" title="Laporkan Kendala"><i class="bi bi-exclamation-triangle-fill"></i> Kendala</a>
                            <a href="{{ route('delivery.detail') }}?id=${d.id}" class="btn btn-xs btn-outline-info text-xs" title="Lihat Detail"><i class="bi bi-eye"></i></a>
                        </div>
                    </td>
                </tr>
            `;
        });

        if (activeDos.length === 0) {
            activeTbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-broadcast-pin text-indigo fs-3 d-block mb-2"></i>
                        Tidak ada armada pengiriman yang sedang di jalan saat ini.
                    </td>
                </tr>
            `;
        }

        // 3. ApexCharts: On-Time Performance (Trends over last 6 months)
        const onTimeOptions = {
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                background: 'transparent'
            },
            theme: { mode: 'dark' },
            colors: ['#6366f1'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            series: [{
                name: 'Tingkat Tepat Waktu (%)',
                data: [94.5, 96.2, 95.8, 97.4, 98.1, 98.4]
            }],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            grid: {
                borderColor: '#374151',
                strokeDashArray: 4
            },
            tooltip: { theme: 'dark' }
        };
        const onTimeChart = new ApexCharts(document.querySelector("#chart-ontime-trends"), onTimeOptions);
        onTimeChart.render();

        // 4. ApexCharts: DO Status Composition
        const statuses = { 'Delivered': 0, 'In Transit': 0, 'Prepared': 0, 'Pending Validation': 0 };
        dos.forEach(d => {
            if (statuses[d.status] !== undefined) {
                statuses[d.status]++;
            }
        });

        const statusOptions = {
            chart: {
                type: 'donut',
                height: 280,
                background: 'transparent'
            },
            theme: { mode: 'dark' },
            series: Object.values(statuses),
            labels: Object.keys(statuses),
            colors: ['#10b981', '#06b6d4', '#8b5cf6', '#f59e0b'],
            legend: {
                position: 'bottom',
                fontSize: '11px',
                fontFamily: 'Outfit, sans-serif'
            },
            stroke: { width: 0 },
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            name: { show: true, fontFamily: 'Outfit, sans-serif' },
                            value: { 
                                show: true, 
                                fontSize: '22px', 
                                fontFamily: 'Outfit, sans-serif',
                                fontWeight: 'bold',
                                formatter: function (val) { return val; } 
                            },
                            total: {
                                show: true,
                                label: 'Total DO',
                                fontFamily: 'Outfit, sans-serif',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            tooltip: { theme: 'dark' }
        };
        const statusChart = new ApexCharts(document.querySelector("#chart-do-status"), statusOptions);
        statusChart.render();
    });
</script>
@endsection
