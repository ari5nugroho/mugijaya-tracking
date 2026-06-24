@extends('layouts.main')

@section('title', 'Surat Jalan - CV Mugijaya Logistics ERP')

@section('styles')
<style>
    /* Document sheet container inside dark theme */
    .document-sheet {
        background-color: #ffffff;
        color: #1f2937;
        border-radius: 8px;
        padding: 3rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        border: 1px solid var(--border-color);
        max-width: 850px;
        margin: 0 auto;
        position: relative;
    }
    
    .document-sheet table {
        color: #1f2937 !important;
        border-color: #d1d5db !important;
    }

    .document-sheet th {
        background-color: #f3f4f6 !important;
        color: #111827 !important;
        border-bottom: 2px solid #9ca3af !important;
    }

    .document-sheet td {
        border-bottom: 1px solid #e5e7eb !important;
    }

    .document-sheet hr {
        border-color: #9ca3af;
    }

    /* Printable styles */
    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 12pt;
        }
        .app-wrapper, .sidebar, .top-navbar, #sidebar-target, #navbar-target, .top-navbar-placeholder, .no-print {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .content-body {
            padding: 0 !important;
        }
        .document-sheet {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            background: transparent !important;
            color: #000000 !important;
        }
    }
</style>
@endsection

@section('content')
<!-- Action Controls (No Print) -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2 no-print">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('delivery.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Daftar DO
        </a>
        <span id="back-detail-link">
            <!-- Populated dynamically -->
        </span>
    </div>
    <div>
        <button class="btn btn-primary-gradient px-4" onclick="window.print()">
            <i class="bi bi-printer-fill me-1"></i> Cetak Surat Jalan
        </button>
    </div>
</div>

<!-- Printable Sheet -->
<div class="document-sheet">
    <!-- Letterhead -->
    <div class="row mb-4">
        <div class="col-8">
            <h4 class="fw-bold text-dark m-0">CV MUGIJAYA</h4>
            <p class="text-xs text-muted m-0" style="line-height: 1.4;">
                <strong>Logistics & Warehouse Management System</strong><br>
                Jl. Kaligawe Raya No.12, Semarang, Jawa Tengah<br>
                Telp: (024) 7654321 | Email: support@mugijaya.com
            </p>
        </div>
        <div class="col-4 text-end">
            <h5 class="fw-bold text-uppercase border-bottom border-dark pb-2 mb-2 text-indigo">Surat Jalan</h5>
            <div class="text-xs text-muted">
                Nomor: <span class="fw-bold text-dark" id="sj-do-number">DO-2026-XXXX</span><br>
                Tanggal: <span class="fw-bold text-dark" id="sj-date">15 Juni 2026</span>
            </div>
        </div>
    </div>

    <hr class="my-3 text-dark">

    <!-- Parties Involved Grid -->
    <div class="row g-4 mb-4 text-xs">
        <div class="col-6">
            <span class="text-muted d-block text-uppercase fw-semibold mb-1">Pengirim (Asal):</span>
            <div class="bg-light p-2.5 rounded border border-secondary border-opacity-10 text-dark">
                <strong id="sj-wh-name">Gudang Utama Semarang</strong><br>
                <span id="sj-wh-address">Jl. Kaligawe Raya No.12, Semarang</span><br>
                Manajer: <span id="sj-wh-manager">Siti Rahma</span>
            </div>
        </div>
        <div class="col-6">
            <span class="text-muted d-block text-uppercase fw-semibold mb-1">Penerima (Tujuan):</span>
            <div class="bg-light p-2.5 rounded border border-secondary border-opacity-10 text-dark">
                <strong id="sj-cust-name">PT. Pelanggan Sejahtera</strong><br>
                <span id="sj-cust-address">Jl. Pemuda No.10, Semarang</span>
            </div>
        </div>
    </div>

    <!-- Transport Info -->
    <div class="bg-light p-3 rounded border border-secondary border-opacity-10 mb-4 text-xs text-dark">
        <div class="row">
            <div class="col-4">
                <span class="text-muted">Nama Driver:</span><br>
                <strong id="sj-drv-name">Rian Hidayat</strong>
            </div>
            <div class="col-4">
                <span class="text-muted">No. Kendaraan (Plat):</span><br>
                <strong id="sj-drv-plate">H 1234 AB</strong>
            </div>
            <div class="col-4">
                <span class="text-muted">Kelas Armada:</span><br>
                <strong id="sj-drv-vehicle">CDE Box (Medium)</strong>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="mb-4">
        <table class="table table-bordered text-xs align-middle">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th style="width: 150px;">SKU</th>
                    <th>Deskripsi Nama Barang</th>
                    <th style="width: 100px;" class="text-center">Kuantitas</th>
                    <th style="width: 100px;" class="text-center">Satuan</th>
                    <th style="width: 120px;" class="text-end">Bobot</th>
                </tr>
            </thead>
            <tbody id="sj-items-tbody">
                <!-- Loaded dynamically -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-bold">Total Muatan:</td>
                    <td class="text-center fw-bold text-dark" id="sj-total-qty">0</td>
                    <td class="text-center fw-bold text-dark">Pcs</td>
                    <td class="text-end fw-bold text-dark" id="sj-total-weight">0.0 Kg</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Footnotes / Legal Declaration -->
    <p class="text-muted text-xs mb-5" style="line-height: 1.4; text-align: justify;">
        * Harap periksa segel dan fisik barang sebelum menandatangani surat jalan ini. 
        Dengan menandatangani dokumen ini, penerima menyatakan bahwa barang telah diterima 
        dalam kondisi yang baik, lengkap, dan sesuai dengan deskripsi manifes di atas.
    </p>

    <!-- Signatures Section -->
    <div class="row text-center text-xs mt-5">
        <div class="col-4">
            <span class="text-muted">Petugas Gudang (Pengirim)</span>
            <div style="height: 70px;"></div>
            <strong class="border-top border-dark d-inline-block pt-1 px-4" id="sig-wh-staff">Siti Rahma</strong>
        </div>
        <div class="col-4">
            <span class="text-muted">Driver (Kurir)</span>
            <div style="height: 70px;"></div>
            <strong class="border-top border-dark d-inline-block pt-1 px-4" id="sig-drv-name">Rian Hidayat</strong>
        </div>
        <div class="col-4">
            <span class="text-muted">Penerima Barang</span>
            <div style="height: 70px;"></div>
            <strong class="border-top border-dark d-inline-block pt-1 px-4">(____________________)</strong>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const doId = parseInt(urlParams.get('id'));

        const dos = db.getData('deliveryOrders');
        let order = dos.find(d => d.id === doId);

        if (!order) {
            if (dos.length > 0) order = dos[0];
            else {
                alert('Tidak ada data Delivery Order!');
                window.location.href = "{{ route('delivery.index') }}";
                return;
            }
        }

        // Bind back link
        document.getElementById('back-detail-link').innerHTML = `
            <a href="{{ route('delivery.detail') }}?id=${order.id}" class="btn btn-sm btn-outline-info">
                <i class="bi bi-eye-fill"></i> Detail DO
            </a>
        `;

        // Document Header Details
        document.getElementById('sj-do-number').innerText = order.doNumber;
        document.getElementById('sj-date').innerText = order.date;

        // Warehouse details
        const warehouses = db.getData('warehouses');
        const wh = warehouses.find(w => w.id === order.originId);
        if (wh) {
            document.getElementById('sj-wh-name').innerText = wh.name;
            document.getElementById('sj-wh-address').innerText = wh.address;
            document.getElementById('sj-wh-manager').innerText = wh.manager;
            document.getElementById('sig-wh-staff').innerText = wh.manager;
        }

        // Customer details
        document.getElementById('sj-cust-name').innerText = order.destination.split(',')[0] || 'PT. Pelanggan Sejahtera';
        document.getElementById('sj-cust-address').innerText = order.destination;

        // Driver details
        const drivers = db.getData('drivers');
        const driver = drivers.find(d => d.id === order.driverId);
        if (driver) {
            document.getElementById('sj-drv-name').innerText = driver.name;
            document.getElementById('sj-drv-plate').innerText = driver.licensePlate;
            document.getElementById('sj-drv-vehicle').innerText = driver.vehicleClass;
            document.getElementById('sig-drv-name').innerText = driver.name;
        } else {
            document.getElementById('sj-drv-name').innerText = 'Belum Ditentukan';
            document.getElementById('sj-drv-plate').innerText = '-';
            document.getElementById('sj-drv-vehicle').innerText = '-';
            document.getElementById('sig-drv-name').innerText = 'Driver';
        }

        // Items details
        const products = db.getData('products');
        const tbody = document.getElementById('sj-items-tbody');
        tbody.innerHTML = '';

        let totalQty = 0;
        let totalW = 0.0;

        order.items.forEach((item, index) => {
            const prod = products.find(p => p.sku === item.sku);
            const prodName = prod ? prod.name : 'Unknown Item';
            const weight = prod ? prod.weight : 0.0;
            const unit = prod ? prod.unit : 'Pcs';
            const totalRowWeight = weight * item.quantity;

            totalQty += item.quantity;
            totalW += totalRowWeight;

            tbody.innerHTML += `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td class="fw-bold">${item.sku}</td>
                    <td>${prodName}</td>
                    <td class="text-center fw-bold">${item.quantity}</td>
                    <td class="text-center">${unit}</td>
                    <td class="text-end">${totalRowWeight.toFixed(2)} Kg</td>
                </tr>
            `;
        });

        document.getElementById('sj-total-qty').innerText = totalQty;
        document.getElementById('sj-total-weight').innerText = `${totalW.toFixed(2)} Kg`;
    });
</script>
@endsection
