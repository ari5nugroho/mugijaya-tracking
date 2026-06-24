@extends('layouts.main')

@section('title', 'Audit Log - CV Mugijaya Logistics ERP')

@section('styles')
<!-- DataTables BS5 CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Audit Log Sistem</h4>
        <p class="text-muted text-sm m-0">Catatan riwayat aktivitas pengguna, perubahan data stok, status DO, dan akses login.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-danger" onclick="clearLogs()"><i class="bi bi-trash3-fill"></i> Bersihkan Log</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="restoreInitialLogs()"><i class="bi bi-arrow-counterclockwise"></i> Restore Default</button>
    </div>
</div>

<div class="erp-card p-4">
    <div class="table-responsive">
        <table id="logs-table" class="table align-middle text-sm" style="width:100%">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Pengguna / User</th>
                    <th>Aksi Utama</th>
                    <th>Tipe Perubahan</th>
                    <th>Rincian Keterangan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via script -->
            </tbody>
        </table>
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

    function renderLogsTable() {
        const logs = db.getData('auditLogs');

        if ($.fn.DataTable.isDataTable('#logs-table')) {
            table.destroy();
        }

        const tbody = document.querySelector('#logs-table tbody');
        tbody.innerHTML = '';

        logs.forEach(l => {
            const statusBadge = l.status === 'Success' ? 'bg-success text-success bg-opacity-10 border border-success border-opacity-30' : 'bg-danger text-danger bg-opacity-10 border border-danger border-opacity-30';

            // Format Timestamp to local ID string
            const timestampStr = new Date(l.timestamp).toLocaleString('id-ID');

            tbody.innerHTML += `
                <tr>
                    <td class="font-monospace text-xs text-secondary">${timestampStr}</td>
                    <td class="fw-semibold text-primary"><i class="bi bi-person text-muted me-1.5"></i>${l.user}</td>
                    <td class="fw-bold">${l.action}</td>
                    <td><span class="badge bg-light-dark text-secondary text-xs">${l.type}</span></td>
                    <td class="text-wrap" style="max-width: 300px;">${l.details}</td>
                    <td><span class="badge ${statusBadge} text-xs">${l.status}</span></td>
                </tr>
            `;
        });

        table = $('#logs-table').DataTable({
            responsive: true,
            order: [[0, 'desc']], // Sort by timestamp descending
            language: {
                search: "Cari Log:",
                lengthMenu: "Tampil _MENU_",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ log",
                paginate: {
                    next: "<i class='bi bi-chevron-right'></i>",
                    previous: "<i class='bi bi-chevron-left'></i>"
                }
            }
        });
    }

    function clearLogs() {
        if (confirm('Apakah Anda yakin ingin mengosongkan seluruh riwayat log sistem?')) {
            db.setData('auditLogs', []);
            renderLogsTable();
        }
    }

    function restoreInitialLogs() {
        if (confirm('Apakah Anda ingin memulihkan logs default?')) {
            localStorage.removeItem('erp_auditLogs');
            // Reinitialize DB class constructor logic
            db.initKey('auditLogs', INITIAL_AUDIT_LOGS);
            renderLogsTable();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderLogsTable();
    });
</script>
@endsection
