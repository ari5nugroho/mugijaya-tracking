@extends('layouts.main')

@section('title', 'Laporan Insiden - CV Mugijaya Logistics ERP')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Laporan & Penanganan Insiden</h4>
        <p class="text-muted text-sm m-0">Catat kendala teknis, hambatan lalu lintas, kecelakaan, atau kerusakan kargo selama distribusi berjalan.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Report Form -->
    <div class="col-12 col-lg-5">
        <div class="erp-card p-4">
            <h6 class="fw-bold mb-3 text-danger">
                <i class="bi bi-exclamation-octagon-fill me-2"></i>Laporkan Insiden Baru
            </h6>
            <form id="incident-report-form">
                <div class="mb-3">
                    <label for="incident-do" class="form-label">Nomor Delivery Order (DO)</label>
                    <select class="form-select" id="incident-do" required>
                        <option value="" disabled selected>-- Pilih DO Terkait --</option>
                        <!-- Loaded dynamically -->
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="incident-type" class="form-label">Jenis Hambatan</label>
                        <select class="form-select" id="incident-type" required>
                            <option value="" disabled selected>-- Pilih Jenis --</option>
                            <option value="Kecelakaan Ringan">Kecelakaan Ringan</option>
                            <option value="Kerusakan Armada">Kerusakan Armada / Mesin</option>
                            <option value="Ban Bocor">Ban Bocor</option>
                            <option value="Cuaca Buruk">Cuaca Buruk (Banjir/Badai)</option>
                            <option value="Kemacetan Total">Kemacetan Total</option>
                            <option value="Kerusakan Kargo">Kerusakan Kargo / Barang</option>
                            <option value="Penerima Tidak Ada">Penerima Tidak Ditemukan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="incident-severity" class="form-label font-weight-bold">Tingkat Urgensi</label>
                        <select class="form-select" id="incident-severity" required>
                            <option value="" disabled selected>-- Pilih Urgensi --</option>
                            <option value="Low">Low (Hambatan Ringan)</option>
                            <option value="Medium">Medium (Terlambat Jam)</option>
                            <option value="High">High (Kritis / Gagal Kirim)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="incident-reporter" class="form-label">Nama Pelapor (Driver/Staf)</label>
                    <input type="text" class="form-control text-sm" id="incident-reporter" required placeholder="Contoh: Rian Hidayat (Driver)">
                </div>

                <div class="mb-3">
                    <label for="incident-desc" class="form-label">Deskripsi Kejadian Lapangan</label>
                    <textarea class="form-control text-sm" id="incident-desc" rows="3" required placeholder="Jelaskan secara singkat lokasi kejadian, kondisi barang, dan dampak pengiriman..."></textarea>
                </div>

                <div class="mb-4">
                    <label for="incident-resolution" class="form-label">Tindakan Resolusi Awal (Optional)</label>
                    <input type="text" class="form-control text-sm" id="incident-resolution" placeholder="Contoh: Mengganti ban cadangan, berteduh sementara">
                </div>

                <button type="submit" class="btn btn-danger w-100 py-2 fw-semibold">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Kirim Laporan Insiden
                </button>
            </form>
        </div>
    </div>

    <!-- Right: Incidents Log Table -->
    <div class="col-12 col-lg-7">
        <div class="erp-card p-4">
            <h6 class="fw-bold mb-3">
                <i class="bi bi-clock-history text-indigo me-2"></i>Log Aktivitas & Penanganan Kendala
            </h6>
            <div class="table-responsive">
                <table class="table text-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>DO Number</th>
                            <th>Pelapor</th>
                            <th>Jenis Insiden</th>
                            <th>Detail Kejadian</th>
                            <th>Resolusi / Status</th>
                        </tr>
                    </thead>
                    <tbody id="incidents-log-tbody">
                        <!-- Loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Resolve Incident Modal -->
<div class="modal fade" id="resolveIncidentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-success"><i class="bi bi-check-circle-fill me-2"></i>Selesaikan Laporan Kendala</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="resolve-incident-form">
                <div class="modal-body">
                    <input type="hidden" id="resolve-incident-id">
                    <div class="mb-3">
                        <label class="form-label text-xs">Nomor DO Terkait:</label>
                        <div class="fw-bold text-primary text-sm" id="resolve-do-num text-indigo">DO-2026-XXXX</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs">Deskripsi Kejadian Lapangan:</label>
                        <p class="text-secondary text-xs bg-light-dark p-2.5 rounded border border-light-dark" id="resolve-incident-desc">-</p>
                    </div>
                    <div class="mb-3">
                        <label for="resolve-input-resolution" class="form-label text-xs">Langkah Resolusi Akhir</label>
                        <textarea class="form-control text-sm" id="resolve-input-resolution" rows="3" required placeholder="Tuliskan tindakan akhir yang diambil (Contoh: Ban cadangan selesai dipasang, driver kembali jalan dan barang sudah terkirim aman)."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-semibold">Selesaikan Masalah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let resolveModal = null;

    function populateDoSelect() {
        const dos = db.getData('deliveryOrders') || [];
        const selectDo = document.getElementById('incident-do');
        selectDo.innerHTML = '<option value="" disabled selected>-- Pilih DO Terkait --</option>';

        dos.forEach(d => {
            selectDo.innerHTML += `<option value="${d.doNumber}">${d.doNumber} -> ${d.destination.split(',')[0]}</option>`;
        });

        // Parse URL Parameters
        const urlParams = new URLSearchParams(window.location.search);
        const paramDoNumber = urlParams.get('doNumber');
        if (paramDoNumber) {
            const found = dos.some(d => d.doNumber === paramDoNumber);
            if (found) {
                selectDo.value = paramDoNumber;
            }
        }
    }

    function renderIncidentsLog() {
        const incidents = db.getData('incidents') || [];
        const tbody = document.getElementById('incidents-log-tbody');
        tbody.innerHTML = '';

        incidents.forEach(inc => {
            let severityBadge = 'bg-info';
            if (inc.severity === 'High') severityBadge = 'bg-danger';
            else if (inc.severity === 'Medium') severityBadge = 'bg-warning text-dark';

            let resolutionHtml = '';
            if (inc.status === 'Resolved') {
                resolutionHtml = `
                    <div class="text-xs text-secondary mt-1">
                        <span class="badge bg-success text-xs mb-1">Resolved</span><br>
                        <strong>Resolusi:</strong> ${inc.resolution}
                    </div>
                `;
            } else {
                resolutionHtml = `
                    <div class="mt-1">
                        <span class="badge bg-danger text-xs mb-1.5">Open Incident</span><br>
                        <button class="btn btn-xs btn-outline-success py-0.5 px-2 text-xs" onclick="openResolveModal(${inc.id})">
                            <i class="bi bi-check-lg"></i> Tandai Selesai
                        </button>
                    </div>
                `;
            }

            tbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-primary font-monospace">${inc.doNumber}</td>
                    <td>
                        <span class="fw-semibold">${inc.reporter}</span><br>
                        <span class="text-muted text-xxs">${inc.date}</span>
                    </td>
                    <td>
                        <span class="badge bg-light-dark text-danger text-xs mb-1">${inc.type}</span><br>
                        <span class="badge ${severityBadge} text-xxs">${inc.severity}</span>
                    </td>
                    <td class="text-xs text-muted" style="max-width: 220px;" title="${inc.description}">
                        ${inc.description}
                    </td>
                    <td>${resolutionHtml}</td>
                </tr>
            `;
        });

        if (incidents.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Belum ada catatan insiden terdaftar.</td></tr>`;
        }
    }

    function openResolveModal(id) {
        const incidents = db.getData('incidents') || [];
        const inc = incidents.find(i => i.id === id);

        if (inc) {
            document.getElementById('resolve-incident-id').value = inc.id;
            document.getElementById('resolve-do-num').innerText = inc.doNumber;
            document.getElementById('resolve-incident-desc').innerText = inc.description;
            document.getElementById('resolve-input-resolution').value = inc.resolution || '';

            resolveModal = new bootstrap.Modal(document.getElementById('resolveIncidentModal'));
            resolveModal.show();
        }
    }

    // Form Submit Report
    document.getElementById('incident-report-form').addEventListener('submit', (e) => {
        e.preventDefault();

        const doNumberVal = document.getElementById('incident-do').value;
        const typeVal = document.getElementById('incident-type').value;
        const severityVal = document.getElementById('incident-severity').value;
        const reporterVal = document.getElementById('incident-reporter').value.trim();
        const descVal = document.getElementById('incident-desc').value.trim();
        const resolutionVal = document.getElementById('incident-resolution').value.trim();

        const newIncident = {
            doNumber: doNumberVal,
            reporter: reporterVal,
            date: new Date().toISOString().split('T')[0],
            type: typeVal,
            severity: severityVal,
            description: descVal,
            status: resolutionVal ? 'Resolved' : 'Open',
            resolution: resolutionVal
        };

        db.insertItem('incidents', newIncident);

        // Audit log
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        db.logAction(
            activeUser.name, 
            "Distribution Incidents", 
            "Report Incident", 
            `Insiden tipe ${typeVal} dilaporkan untuk DO ${doNumberVal} oleh ${reporterVal}`, 
            "Success"
        );

        alert(`Insiden berhasil dilaporkan dengan status: ${newIncident.status}.`);
        document.getElementById('incident-report-form').reset();
        populateDoSelect(); // Reset selects
        renderIncidentsLog();
    });

    // Form Submit Resolve
    document.getElementById('resolve-incident-form').addEventListener('submit', (e) => {
        e.preventDefault();

        const id = parseInt(document.getElementById('resolve-incident-id').value);
        const resVal = document.getElementById('resolve-input-resolution').value.trim();

        db.updateItem('incidents', id, {
            status: 'Resolved',
            resolution: resVal
        });

        // Audit log
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        const incidents = db.getData('incidents') || [];
        const inc = incidents.find(i => i.id === id);
        
        db.logAction(
            activeUser.name, 
            "Distribution Incidents", 
            "Resolve Incident", 
            `Menyelesaikan masalah logistik pada DO ${inc ? inc.doNumber : 'Unknown'}`, 
            "Success"
        );

        alert('Status insiden berhasil diperbarui ke RESOLVED.');
        resolveModal.hide();
        renderIncidentsLog();
    });

    document.addEventListener('DOMContentLoaded', () => {
        populateDoSelect();
        renderIncidentsLog();

        // Prefill reporter based on logged in user
        const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
        if (activeUser) {
            document.getElementById('incident-reporter').value = `${activeUser.name} (${activeUser.role})`;
        }
    });
</script>
@endsection
