@extends('layouts.main')

@section('title', 'Stok & Mutasi - CV Mugijaya Logistics ERP')

@section('styles')
<!-- DataTables BS5 CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Stok & Mutasi Barang</h4>
        <p class="text-muted text-sm m-0">Pantau tingkat persediaan per gudang, atur batas minimal, dan input mutasi stok.</p>
    </div>
    <button class="btn btn-primary-gradient d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
        <i class="bi bi-arrow-left-right"></i> Input Mutasi Stok
    </button>
</div>

<div class="row g-4 mb-4">
    <!-- Left: Stock list with filter -->
    <div class="col-12 col-xl-8">
        <div class="erp-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h6 class="fw-bold m-0"><i class="bi bi-box-fill text-indigo me-2"></i>Status Persediaan Real-time</h6>
                <div style="min-width: 200px;">
                    <select class="form-select form-select-sm" id="wh-filter" onchange="renderStocksTable()">
                        <option value="all">Semua Gudang</option>
                        <!-- Populated dynamically -->
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table id="stocks-table" class="table align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>Gudang</th>
                            <th>Stok Saat Ini</th>
                            <th>Batas Minimal</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Low Stock Alert summary & Quick logs -->
    <div class="col-12 col-xl-4">
        <div class="erp-card p-4 mb-4 border border-danger border-opacity-20 bg-danger bg-opacity-5">
            <h6 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-octagon-fill me-2"></i>Peringatan Restock</h6>
            <div class="d-flex flex-column gap-2.5" id="low-stocks-list">
                <!-- Populated dynamically -->
            </div>
        </div>

        <div class="erp-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history text-indigo me-2"></i>Log Mutasi Terakhir</h6>
            <div class="d-flex flex-column gap-3" id="mutation-logs">
                <!-- Populated dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Modals Section -->
<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-left-right text-indigo me-2"></i>Input Mutasi Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adjust-stock-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="adj-product" class="form-label">Pilih Produk (SKU)</label>
                        <select class="form-select" id="adj-product" required>
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adj-warehouse" class="form-label">Gudang Tujuan</label>
                        <select class="form-select" id="adj-warehouse" required>
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adj-type" class="form-label">Tipe Penyesuaian</label>
                        <select class="form-select" id="adj-type" required>
                            <option value="IN">Stock In (Barang Masuk)</option>
                            <option value="OUT">Stock Out (Barang Keluar)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adj-quantity" class="form-label">Jumlah (Pcs/Unit)</label>
                        <input type="number" class="form-control" id="adj-quantity" required min="1" placeholder="Contoh: 50">
                    </div>
                    <div class="mb-3">
                        <label for="adj-notes" class="form-label">Keterangan / Alasan</label>
                        <input type="text" class="form-control" id="adj-notes" placeholder="Contoh: Restock Supplier PT ABC" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">Kirim Penyesuaian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Min Stock Modal -->
<div class="modal fade" id="editMinStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-sliders text-indigo me-2"></i>Batas Pengingat Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-min-stock-form">
                <input type="hidden" id="edit-min-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label d-block text-muted">Produk</label>
                        <div class="fw-bold" id="edit-min-prod-name">SMART TV LED</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-min-val" class="form-label">Jumlah Stok Minimum</label>
                        <input type="number" class="form-control" id="edit-min-val" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">Simpan Batas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery (needed for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    let table;

    function renderStocksTable() {
        const stocks = db.getData('stocks');
        const warehouses = db.getData('warehouses');
        const products = db.getData('products');
        const filterWh = document.getElementById('wh-filter').value;
        
        if ($.fn.DataTable.isDataTable('#stocks-table')) {
            table.destroy();
        }

        const tbody = document.querySelector('#stocks-table tbody');
        tbody.innerHTML = '';

        let filteredStocks = stocks;
        if (filterWh !== 'all') {
            filteredStocks = stocks.filter(s => s.warehouseId === parseInt(filterWh));
        }

        filteredStocks.forEach(s => {
            const prod = products.find(p => p.sku === s.sku);
            const wh = warehouses.find(w => w.id === s.warehouseId);
            
            if (!prod || !wh) return; // skip orphans

            const isLow = s.stockCurrent <= s.stockMin;
            const badgeClass = isLow ? 'bg-danger text-danger bg-opacity-10 border border-danger border-opacity-30' : 'bg-success text-success bg-opacity-10 border border-success border-opacity-30';
            const statusStr = isLow ? 'Low Stock' : 'Aman';

            tbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-indigo">${s.sku}</td>
                    <td class="fw-semibold text-primary">${prod.name}</td>
                    <td class="text-xs">${wh.code} - ${wh.name}</td>
                    <td class="fw-bold">${s.stockCurrent} ${prod.unit}</td>
                    <td>${s.stockMin} ${prod.unit}</td>
                    <td><span class="badge ${badgeClass} text-xs">${statusStr}</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-warning" onclick="openMinStockModal(${s.id})" title="Atur Limit"><i class="bi bi-sliders"></i> Adjust Limit</button>
                    </td>
                </tr>
            `;
        });

        table = $('#stocks-table').DataTable({
            responsive: true,
            language: {
                search: "Cari SKU/Nama:",
                lengthMenu: "Tampil _MENU_",
                info: "_START_ - _END_ dari _TOTAL_",
                paginate: {
                    next: "<i class='bi bi-chevron-right'></i>",
                    previous: "<i class='bi bi-chevron-left'></i>"
                }
            }
        });
    }

    function renderSidePanels() {
        const stocks = db.getData('stocks');
        const warehouses = db.getData('warehouses');
        const products = db.getData('products');
        const logs = db.getData('auditLogs');

        // 1. Low stock panel
        const lowList = document.getElementById('low-stocks-list');
        lowList.innerHTML = '';
        
        let lowCount = 0;
        stocks.forEach(s => {
            const prod = products.find(p => p.sku === s.sku);
            const wh = warehouses.find(w => w.id === s.warehouseId);
            if (prod && wh && s.stockCurrent <= s.stockMin) {
                lowCount++;
                lowList.innerHTML += `
                    <div class="d-flex align-items-center justify-content-between border-bottom border-danger border-opacity-10 pb-2 last-no-border">
                        <div>
                            <div class="text-xs fw-bold text-primary">${prod.name}</div>
                            <div class="text-xs text-muted">${wh.name}</div>
                        </div>
                        <span class="badge bg-danger">${s.stockCurrent} / ${s.stockMin} ${prod.unit}</span>
                    </div>
                `;
            }
        });

        if (lowCount === 0) {
            lowList.innerHTML = '<div class="text-xs text-muted py-2 text-center">Seluruh stok aman.</div>';
        }

        // 2. Mutation logs panel (filter logs containing "Stock")
        const mutationList = document.getElementById('mutation-logs');
        mutationList.innerHTML = '';

        const stockLogs = logs.filter(l => l.action.toLowerCase().includes('stock') || l.type.toLowerCase().includes('in') || l.type.toLowerCase().includes('out')).slice(0, 5);
        
        stockLogs.forEach(l => {
            let badgeColor = 'text-success bg-success bg-opacity-10';
            if (l.type.toLowerCase().includes('out') || l.type.toLowerCase().includes('delete')) {
                badgeColor = 'text-danger bg-danger bg-opacity-10';
            }
            const timeStr = new Date(l.timestamp).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            mutationList.innerHTML += `
                <div class="d-flex align-items-start gap-2 border-bottom border-light-dark pb-2 last-no-border">
                    <span class="badge ${badgeColor} text-xs fw-bold">${l.type}</span>
                    <div class="min-w-0 flex-grow-1">
                        <p class="text-xs text-primary mb-0.5 fw-semibold">${l.details}</p>
                        <div class="d-flex justify-content-between text-xs text-muted">
                            <span>Oleh: ${l.user}</span>
                            <span>${timeStr}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        if (stockLogs.length === 0) {
            mutationList.innerHTML = '<div class="text-xs text-muted py-2 text-center">Belum ada mutasi stok.</div>';
        }
    }

    // Setup drop-down options for mutasi
    function populateSelectors() {
        const products = db.getData('products');
        const warehouses = db.getData('warehouses');

        const pSel = document.getElementById('adj-product');
        pSel.innerHTML = '';
        products.forEach(p => {
            pSel.innerHTML += `<option value="${p.sku}">${p.sku} - ${p.name}</option>`;
        });

        const wSel = document.getElementById('adj-warehouse');
        wSel.innerHTML = '';
        warehouses.forEach(w => {
            wSel.innerHTML += `<option value="${w.id}">${w.code} - ${w.name}</option>`;
        });
    }

    // Open adjustment limit modal
    function openMinStockModal(stockId) {
        const stocks = db.getData('stocks');
        const products = db.getData('products');
        const s = stocks.find(st => st.id === stockId);
        if (s) {
            const prod = products.find(p => p.sku === s.sku);
            document.getElementById('edit-min-id').value = s.id;
            document.getElementById('edit-min-prod-name').innerText = prod ? `${prod.sku} - ${prod.name}` : s.sku;
            document.getElementById('edit-min-val').value = s.stockMin;

            const modal = new bootstrap.Modal(document.getElementById('editMinStockModal'));
            modal.show();
        }
    }

    // Edit min stock submit handler
    document.getElementById('edit-min-stock-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('edit-min-id').value;
        const minVal = parseInt(document.getElementById('edit-min-val').value);

        const stocks = db.getData('stocks');
        const index = stocks.findIndex(s => s.id === parseInt(id));
        if (index !== -1) {
            stocks[index].stockMin = minVal;
            db.setData('stocks', stocks);

            // Log audit
            const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
            db.logAction(activeUser.name, "Stock Adjustment", "Adjust Limit", `Mengubah batas stok minimum ${stocks[index].sku} menjadi ${minVal}`, "Success");

            bootstrap.Modal.getInstance(document.getElementById('editMinStockModal')).hide();
            renderStocksTable();
            renderSidePanels();
        }
    });

    // Mutasi stok submit handler
    document.getElementById('adjust-stock-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const sku = document.getElementById('adj-product').value;
        const whId = parseInt(document.getElementById('adj-warehouse').value);
        const type = document.getElementById('adj-type').value;
        const qty = parseInt(document.getElementById('adj-quantity').value);
        const notes = document.getElementById('adj-notes').value.trim();

        const stocks = db.getData('stocks');
        const warehouses = db.getData('warehouses');
        const wh = warehouses.find(w => w.id === whId);

        // Find stock card
        let stockCard = stocks.find(s => s.sku === sku && s.warehouseId === whId);
        
        if (!stockCard) {
            // Create stock card if it doesn't exist
            stockCard = {
                id: stocks.length > 0 ? Math.max(...stocks.map(s => s.id)) + 1 : 1,
                sku: sku,
                warehouseId: whId,
                stockCurrent: 0,
                stockMin: 10
            };
            stocks.push(stockCard);
        }

        const oldStock = stockCard.stockCurrent;
        let newStock = oldStock;

        if (type === 'IN') {
            newStock += qty;
        } else {
            if (oldStock < qty) {
                alert(`Stok tidak mencukupi! Stok saat ini di gudang terpilih: ${oldStock}`);
                return;
            }
            newStock -= qty;
        }

        // Update database stocks
        stockCard.stockCurrent = newStock;
        db.setData('stocks', stocks);

        // Add vol weight changes to warehouse capacity used (simplified volumetric capacity simulation)
        const products = db.getData('products');
        const prod = products.find(p => p.sku === sku);
        if (prod && wh) {
            // Let's assume 1 unit product uses: 0.1 m³
            const deltaVol = (type === 'IN' ? qty : -qty) * 0.1;
            let newCapacityUsed = wh.capacityUsed + deltaVol;
            if (newCapacityUsed < 0) newCapacityUsed = 0;
            if (newCapacityUsed > wh.capacity) newCapacityUsed = wh.capacity;
            db.updateItem('warehouses', whId, { capacityUsed: Math.round(newCapacityUsed) });
        }

        // Log action in audit logs
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        const movementDetails = `Mutasi Stock ${type}: ${qty} Pcs ${sku} di ${wh ? wh.name : 'Gudang'} (${notes})`;
        db.logAction(activeUser.name, "Stock Mutation", `Stock ${type}`, movementDetails, "Success");

        bootstrap.Modal.getInstance(document.getElementById('adjustStockModal')).hide();
        document.getElementById('adjust-stock-form').reset();
        
        // Re-render
        renderStocksTable();
        renderSidePanels();
    });

    // Initialize elements
    document.addEventListener('DOMContentLoaded', () => {
        // Populate filter select options
        const warehouses = db.getData('warehouses');
        const whFilter = document.getElementById('wh-filter');
        warehouses.forEach(w => {
            whFilter.innerHTML += `<option value="${w.id}">${w.code} - ${w.name}</option>`;
        });

        populateSelectors();
        renderStocksTable();
        renderSidePanels();

        // Check URL parameters for shortcut action
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'adjust') {
            const modal = new bootstrap.Modal(document.getElementById('adjustStockModal'));
            modal.show();
        }
    });
</script>
@endsection
