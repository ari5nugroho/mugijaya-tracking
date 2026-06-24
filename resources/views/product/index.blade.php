@extends('layouts.main')

@section('title', 'Product Management - CV Mugijaya Logistics ERP')

@section('styles')
<!-- DataTables BS5 CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Product Management</h4>
        <p class="text-muted text-sm m-0">Katalog utama barang/produk, SKU, dimensi, berat, dan harga dasar satuan.</p>
    </div>
    <button class="btn btn-primary-gradient d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="bi bi-plus-square-fill"></i> Tambah Produk Baru
    </button>
</div>

<div class="erp-card p-4">
    <div class="table-responsive">
        <table id="products-table" class="table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Berat Satuan</th>
                    <th>Harga Dasar</th>
                    <th>Total Stok</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via script -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modals Section -->
<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-square-fill text-indigo me-2"></i>Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-product-form">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="add-sku" class="form-label">SKU (Stock Keeping Unit)</label>
                            <input type="text" class="form-control" id="add-sku" required placeholder="Contoh: PRD-ELC-005">
                        </div>
                        <div class="col-md-6">
                            <label for="add-name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="add-name" required placeholder="Contoh: Rice Cooker Digital 2L">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="add-category" class="form-label">Kategori</label>
                            <select class="form-select" id="add-category" required>
                                <option value="Electronics">Electronics</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Food & Beverage">Food & Beverage</option>
                                <option value="Apparel">Apparel</option>
                                <option value="Household">Household</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="add-price" class="form-label">Harga Dasar (IDR)</label>
                            <input type="number" class="form-control" id="add-price" required placeholder="Contoh: 450000">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="add-weight" class="form-label">Berat Satuan (Kg)</label>
                            <input type="number" step="0.01" class="form-control" id="add-weight" required placeholder="Contoh: 2.5">
                        </div>
                        <div class="col-md-6">
                            <label for="add-unit" class="form-label">Satuan Unit</label>
                            <input type="text" class="form-control" id="add-unit" required placeholder="Contoh: Pcs, Pack, Box" value="Pcs">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-indigo me-2"></i>Edit Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-product-form">
                <input type="hidden" id="edit-id">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="edit-sku" class="form-label">SKU (Stock Keeping Unit)</label>
                            <input type="text" class="form-control" id="edit-sku" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="edit-name" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="edit-category" class="form-label">Kategori</label>
                            <select class="form-select" id="edit-category" required>
                                <option value="Electronics">Electronics</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Food & Beverage">Food & Beverage</option>
                                <option value="Apparel">Apparel</option>
                                <option value="Household">Household</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-price" class="form-label">Harga Dasar (IDR)</label>
                            <input type="number" class="form-control" id="edit-price" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit-weight" class="form-label">Berat Satuan (Kg)</label>
                            <input type="number" step="0.01" class="form-control" id="edit-weight" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-unit" class="form-label">Satuan Unit</label>
                            <input type="text" class="form-control" id="edit-unit" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">Simpan Perubahan</button>
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

    function renderProductsTable() {
        const products = db.getData('products');
        const stocks = db.getData('stocks');
        
        if ($.fn.DataTable.isDataTable('#products-table')) {
            table.destroy();
        }

        const tbody = document.querySelector('#products-table tbody');
        tbody.innerHTML = '';

        products.forEach(p => {
            // Sum stock levels for this SKU
            const productStocks = stocks.filter(s => s.sku === p.sku);
            const totalStock = productStocks.reduce((sum, s) => sum + s.stockCurrent, 0);

            tbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-indigo">${p.sku}</td>
                    <td class="fw-semibold text-primary">${p.name}</td>
                    <td><span class="badge bg-light-dark border border-light-dark text-secondary">${p.category}</span></td>
                    <td>${p.weight} ${p.unit === 'Pcs' ? 'Kg' : 'Kg/' + p.unit}</td>
                    <td class="fw-semibold">Rp ${p.price.toLocaleString('id-ID')}</td>
                    <td><span class="fw-bold ${totalStock <= 20 ? 'text-danger' : 'text-success'}">${totalStock} ${p.unit}</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-info me-1" onclick="openEditModal(${p.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${p.id})" title="Hapus"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        });

        table = $('#products-table').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ produk",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ produk",
                paginate: {
                    next: "<i class='bi bi-chevron-right'></i>",
                    previous: "<i class='bi bi-chevron-left'></i>"
                }
            }
        });
    }

    // Open edit modal
    function openEditModal(id) {
        const products = db.getData('products');
        const p = products.find(prod => prod.id === id);
        if (p) {
            document.getElementById('edit-id').value = p.id;
            document.getElementById('edit-sku').value = p.sku;
            document.getElementById('edit-name').value = p.name;
            document.getElementById('edit-category').value = p.category;
            document.getElementById('edit-price').value = p.price;
            document.getElementById('edit-weight').value = p.weight;
            document.getElementById('edit-unit').value = p.unit;

            const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
            modal.show();
        }
    }

    // Add form handler
    document.getElementById('add-product-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const skuVal = document.getElementById('add-sku').value.trim().toUpperCase();
        
        // Check if SKU already exists
        const products = db.getData('products');
        if (products.some(p => p.sku === skuVal)) {
            alert('SKU sudah terdaftar! Masukkan SKU yang unik.');
            return;
        }

        const newProduct = {
            sku: skuVal,
            name: document.getElementById('add-name').value.trim(),
            category: document.getElementById('add-category').value,
            price: parseInt(document.getElementById('add-price').value),
            weight: parseFloat(document.getElementById('add-weight').value),
            unit: document.getElementById('add-unit').value.trim()
        };

        db.insertItem('products', newProduct);

        // Create initial empty stocks in warehouses
        const warehouses = db.getData('warehouses');
        const stocks = db.getData('stocks');
        warehouses.forEach(wh => {
            stocks.push({
                id: stocks.length > 0 ? Math.max(...stocks.map(s => s.id)) + 1 : 1,
                sku: skuVal,
                warehouseId: wh.id,
                stockCurrent: 0,
                stockMin: 10
            });
        });
        db.setData('stocks', stocks);

        // Audit log
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        db.logAction(activeUser.name, "Product Management", "Create Product", `Menambahkan produk baru SKU: ${newProduct.sku} - ${newProduct.name}`, "Success");

        bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
        document.getElementById('add-product-form').reset();
        renderProductsTable();
    });

    // Edit form handler
    document.getElementById('edit-product-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('edit-id').value;
        const updatedProduct = {
            name: document.getElementById('edit-name').value.trim(),
            category: document.getElementById('edit-category').value,
            price: parseInt(document.getElementById('edit-price').value),
            weight: parseFloat(document.getElementById('edit-weight').value),
            unit: document.getElementById('edit-unit').value.trim()
        };

        db.updateItem('products', id, updatedProduct);

        // Audit log
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        db.logAction(activeUser.name, "Product Management", "Update Product", `Mengubah detail produk SKU: ${document.getElementById('edit-sku').value}`, "Success");

        bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
        renderProductsTable();
    });

    // Delete Product
    function deleteProduct(id) {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini? Semua data stok terkait SKU ini juga akan ikut terhapus.')) {
            const products = db.getData('products');
            const p = products.find(prod => prod.id === id);
            if (p) {
                // Delete stock relationships
                let stocks = db.getData('stocks');
                stocks = stocks.filter(s => s.sku !== p.sku);
                db.setData('stocks', stocks);

                // Delete product
                db.deleteItem('products', id);

                // Audit log
                const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
                db.logAction(activeUser.name, "Product Management", "Delete Product", `Menghapus produk SKU: ${p.sku} - ${p.name}`, "Success");

                renderProductsTable();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderProductsTable();

        // Check URL parameters for shortcut action
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'new') {
            const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
            modal.show();
        }
    });
</script>
@endsection
