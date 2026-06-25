@extends('layouts.main')

@section('title', 'Stok & Mutasi - CV Mugijaya Logistics ERP')

@section('content')
<!-- Success Alert -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background-color: rgba(22, 163, 74, 0.2); color: #4ade80;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(1);"></button>
    </div>
@endif

<!-- Validation Errors -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background-color: rgba(220, 38, 38, 0.2); color: #f87171;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong class="d-block mb-1">Terjadi Kesalahan Validasi:</strong>
                <ul class="mb-0 text-sm ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(1);"></button>
    </div>
@endif

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
                
                <!-- Filter form -->
                <form action="{{ route('inventory.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap" id="filter-search-form">
                    <div style="min-width: 180px;">
                        <select class="form-select form-select-sm bg-light-dark border-light-dark text-white text-xs" name="warehouse_id" onchange="this.form.submit()">
                            <option value="all">Semua Gudang</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->code }} - {{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group input-group-sm" style="max-width: 250px;">
                        <span class="input-group-text bg-light-dark border-light-dark text-muted py-1.5"><i class="bi bi-search text-xs"></i></span>
                        <input type="text" name="search" class="form-control bg-light-dark border-light-dark text-white text-xs" placeholder="Cari SKU/Nama..." value="{{ request('search') }}">
                        @if(request('search') || (request('warehouse_id') && request('warehouse_id') !== 'all'))
                            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary border-light-dark bg-light-dark d-flex align-items-center"><i class="bi bi-x-lg text-xs"></i></a>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary-gradient btn-sm px-3 text-xs">Cari</button>
                </form>
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
                        @forelse($stocks as $s)
                            @php
                                $isLow = $s->quantity <= $s->minimum_stock;
                                $badgeClass = $isLow ? 'bg-danger text-danger bg-opacity-10 border border-danger border-opacity-30' : 'bg-success text-success bg-opacity-10 border border-success border-opacity-30';
                                $statusStr = $isLow ? 'Low Stock' : 'Aman';
                            @endphp
                            <tr>
                                <td class="fw-bold text-indigo">{{ $s->product->sku }}</td>
                                <td class="fw-semibold text-primary">
                                    {{ $s->product->name }}
                                </td>
                                <td class="text-xs">{{ $s->warehouse->code }} - {{ $s->warehouse->name }}</td>
                                <td class="fw-bold">{{ $s->quantity }} {{ $s->product->unit }}</td>
                                <td>{{ $s->minimum_stock }} {{ $s->product->unit }}</td>
                                <td><span class="badge {{ $badgeClass }} text-xs">{{ $statusStr }}</span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning" onclick="openMinStockModal({{ json_encode($s) }})" title="Atur Limit"><i class="bi bi-sliders"></i> Adjust Limit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-box2-fill text-muted mb-2 d-block fs-3"></i>
                                    Tidak ada stok barang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination links -->
            <div class="d-flex justify-content-center mt-3">
                {{ $stocks->links() }}
            </div>
        </div>
    </div>

    <!-- Right: Low Stock Alert summary & Quick logs -->
    <div class="col-12 col-xl-4">
        <div class="erp-card p-4 mb-4 border border-danger border-opacity-20 bg-danger bg-opacity-5">
            <h6 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-octagon-fill me-2"></i>Peringatan Restock</h6>
            <div class="d-flex flex-column gap-2.5" id="low-stocks-list">
                @forelse($lowStocks as $ls)
                    <div class="d-flex align-items-center justify-content-between border-bottom border-danger border-opacity-10 pb-2 last-no-border">
                        <div>
                            <div class="text-xs fw-bold text-primary">{{ $ls->product->name }}</div>
                            <div class="text-xs text-muted">{{ $ls->warehouse->name }}</div>
                        </div>
                        <span class="badge bg-danger text-xs py-1">{{ $ls->quantity }} / {{ $ls->minimum_stock }} {{ $ls->product->unit }}</span>
                    </div>
                @empty
                    <div class="text-xs text-muted py-2 text-center">Seluruh stok aman.</div>
                @endforelse
            </div>
        </div>

        <div class="erp-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history text-indigo me-2"></i>Log Mutasi Terakhir</h6>
            <div class="d-flex flex-column gap-3" id="mutation-logs">
                @forelse($recentMovements as $rm)
                    @php
                        $badgeColor = 'text-success bg-success bg-opacity-10 border border-success border-opacity-20';
                        if ($rm->type === 'OUT' || $rm->quantity_change < 0) {
                            $badgeColor = 'text-danger bg-danger bg-opacity-10 border border-danger border-opacity-20';
                        } elseif ($rm->type === 'TRANSFER') {
                            $badgeColor = 'text-info bg-info bg-opacity-10 border border-info border-opacity-20';
                        } elseif ($rm->type === 'ADJUSTMENT') {
                            $badgeColor = 'text-warning bg-warning bg-opacity-10 border border-warning border-opacity-20';
                        }
                    @endphp
                    <div class="d-flex align-items-start gap-2 border-bottom border-light-dark pb-2 last-no-border">
                        <span class="badge {{ $badgeColor }} text-xs fw-bold" style="min-width: 65px; text-align: center;">{{ $rm->type }}</span>
                        <div class="min-w-0 flex-grow-1">
                            <p class="text-xs text-primary mb-0.5 fw-semibold" style="white-space: normal; line-height: 1.4;">
                                <strong>{{ $rm->product->sku }}</strong>: {{ $rm->quantity_change > 0 ? '+' : '' }}{{ $rm->quantity_change }} {{ $rm->product->unit }} di {{ $rm->warehouse->name }}.
                                <span class="text-secondary d-block text-xxs mt-0.5">{{ $rm->notes }}</span>
                            </p>
                            <div class="d-flex justify-content-between text-xxs text-muted mt-1">
                                <span>Oleh: {{ $rm->user ? $rm->user->name : 'System' }}</span>
                                <span>{{ $rm->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-muted py-2 text-center">Belum ada mutasi stok.</div>
                @endforelse
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
            <form id="adjust-stock-form" action="{{ route('inventory.mutate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="adj-product" class="form-label">Pilih Produk</label>
                        <select class="form-select" id="adj-product" name="product_id" required>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->sku }} - {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="adj-type" class="form-label">Tipe Penyesuaian</label>
                        <select class="form-select" id="adj-type" name="type" required onchange="toggleTransferFields()">
                            <option value="IN" {{ old('type') == 'IN' ? 'selected' : '' }}>Stock In (Barang Masuk)</option>
                            <option value="OUT" {{ old('type') == 'OUT' ? 'selected' : '' }}>Stock Out (Barang Keluar)</option>
                            <option value="ADJUSTMENT" {{ old('type') == 'ADJUSTMENT' ? 'selected' : '' }}>Stock Adjustment (Koreksi/Set Stok)</option>
                            <option value="TRANSFER" {{ old('type') == 'TRANSFER' ? 'selected' : '' }}>Transfer Stock (Mutasi Antar Gudang)</option>
                        </select>
                    </div>

                    <!-- Single Warehouse selection (default) -->
                    <div id="single-warehouse-group" class="mb-3">
                        <label for="adj-warehouse" class="form-label">Gudang</label>
                        <select class="form-select" id="adj-warehouse" name="warehouse_id">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->code }} - {{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Double Warehouse selection (Transfer only) -->
                    <div id="transfer-warehouse-group" class="mb-3 d-none">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="adj-source-warehouse" class="form-label">Gudang Asal</label>
                                <select class="form-select" id="adj-source-warehouse" name="source_warehouse_id">
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}" {{ old('source_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->code }} - {{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="adj-dest-warehouse" class="form-label">Gudang Tujuan</label>
                                <select class="form-select" id="adj-dest-warehouse" name="destination_warehouse_id">
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}" {{ old('destination_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->code }} - {{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="adj-quantity" class="form-label">Jumlah (Pcs/Unit)</label>
                        <input type="number" class="form-control" id="adj-quantity" name="quantity" required min="1" placeholder="Contoh: 50" value="{{ old('quantity') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="adj-notes" class="form-label">Keterangan / Alasan</label>
                        <input type="text" class="form-control" id="adj-notes" name="notes" placeholder="Contoh: Restock Supplier PT ABC" required value="{{ old('notes') }}">
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
            <form id="edit-min-stock-form" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-min-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label d-block text-muted">Produk & Gudang</label>
                        <div class="fw-bold text-primary" id="edit-min-prod-name">SMART TV LED</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-min-val" class="form-label">Jumlah Stok Minimum</label>
                        <input type="number" class="form-control" id="edit-min-val" name="minimum_stock" required min="0">
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
<script>
    // Toggle transfer field groups conditionally based on adjustment type selected
    function toggleTransferFields() {
        const type = document.getElementById('adj-type').value;
        const singleGroup = document.getElementById('single-warehouse-group');
        const transferGroup = document.getElementById('transfer-warehouse-group');
        const qtyLabel = document.querySelector('label[for="adj-quantity"]');

        if (type === 'TRANSFER') {
            singleGroup.classList.add('d-none');
            transferGroup.classList.remove('d-none');
            qtyLabel.innerText = "Jumlah Transfer (Pcs/Unit)";
        } else if (type === 'ADJUSTMENT') {
            singleGroup.classList.remove('d-none');
            transferGroup.classList.add('d-none');
            qtyLabel.innerText = "Jumlah Koreksi Baru (Pcs/Unit)";
        } else {
            singleGroup.classList.remove('d-none');
            transferGroup.classList.add('d-none');
            qtyLabel.innerText = "Jumlah (Pcs/Unit)";
        }
    }

    // Open edit minimum stock modal
    function openMinStockModal(stock) {
        if (stock) {
            document.getElementById('edit-min-id').value = stock.id;
            document.getElementById('edit-min-prod-name').innerText = `${stock.product.sku} - ${stock.product.name} (${stock.warehouse.name})`;
            document.getElementById('edit-min-val').value = stock.minimum_stock;

            // Dynamically set action URL
            const form = document.getElementById('edit-min-stock-form');
            form.action = `/inventory/${stock.id}`;

            const modal = new bootstrap.Modal(document.getElementById('editMinStockModal'));
            modal.show();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Sync local storage audit logs
        @if(session('log_action'))
            const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
            if (activeUser && window.db) {
                db.logAction(
                    activeUser.name,
                    "{{ session('log_action.category') }}",
                    "{{ session('log_action.action') }}",
                    "{{ session('log_action.details') }}",
                    "Success"
                );
            }
        @endif

        // Trigger toggle fields initially to ensure correct input states
        toggleTransferFields();

        // Check if modal needs to be re-opened due to validation failure
        @if(session('open_modal'))
            const modal = new bootstrap.Modal(document.getElementById("{{ session('open_modal') }}"));
            modal.show();
        @endif
    });
</script>
@endsection
