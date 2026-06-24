@extends('layouts.main')

@section('title', 'Proof of Delivery - CV Mugijaya Logistics ERP')

@section('styles')
<style>
    .signature-wrapper {
        border: 2px dashed var(--border-color);
        background-color: var(--card-bg);
        border-radius: 6px;
        position: relative;
        height: 180px;
        width: 100%;
        overflow: hidden;
    }
    .signature-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        cursor: crosshair;
        background-color: #ffffff;
    }
</style>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Proof of Delivery (POD)</h4>
        <p class="text-muted text-sm m-0">Input bukti pengiriman digital, tanda tangan elektronik penerima, dan foto dokumentasi serah terima.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: POD Input Form -->
    <div class="col-12 col-lg-5">
        <div class="erp-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-arrow-up-fill text-indigo me-2"></i>Submit Bukti Penerimaan</h6>
            
            <form id="pod-form">
                <div class="mb-3">
                    <label for="pod-do" class="form-label">Pilih DO (Sedang Transit)</label>
                    <select class="form-select" id="pod-do" required onchange="onDoChange()">
                        <option value="" disabled selected>-- Pilih Delivery Order --</option>
                        <!-- Loaded dynamically -->
                    </select>
                </div>

                <div id="do-preview-box" class="mb-3 bg-light-dark p-2.5 rounded border border-light-dark d-none text-xs">
                    <div class="fw-bold text-primary mb-1">Detail Tujuan Pengiriman:</div>
                    <div class="mb-1"><i class="bi bi-geo-alt me-1 text-danger"></i><span id="preview-dest"></span></div>
                    <div><i class="bi bi-person me-1 text-info"></i>Driver: <span id="preview-driver"></span></div>
                </div>

                <div class="mb-3">
                    <label for="pod-recipient" class="form-label">Nama Penerima Barang</label>
                    <input type="text" class="form-control" id="pod-recipient" required placeholder="Contoh: Pak Slamet (Security)">
                </div>

                <div class="mb-3">
                    <label class="form-label d-flex justify-content-between align-items-center">
                        <span>Tanda Tangan Elektronik (Recipient Signature)</span>
                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="clearSignature()"><i class="bi bi-trash"></i> Bersihkan</button>
                    </label>
                    <div class="signature-wrapper">
                        <canvas id="signature-pad" class="signature-canvas"></canvas>
                    </div>
                    <input type="hidden" id="signature-data" required>
                </div>

                <div class="mb-4">
                    <label for="pod-photo" class="form-label">Foto Dokumentasi Serah Terima</label>
                    <input type="file" class="form-control" id="pod-photo" accept="image/*" onchange="previewPhoto(event)">
                    <div class="text-xs text-muted mt-1">Simulasikan unggah foto kardus kargo di lokasi penerima.</div>
                    <img id="photo-preview-el" class="img-fluid rounded mt-2 d-none border border-light-dark" style="max-height: 150px; object-fit: cover;">
                </div>

                <button type="submit" class="btn btn-primary-gradient w-100 py-2">
                    <i class="bi bi-check-circle-fill me-1"></i> Submit Bukti Pengiriman (POD)
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: List of Completed PODs -->
    <div class="col-12 col-lg-7">
        <div class="erp-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-journal-album text-success me-2"></i>Arsip Tanda Terima (Completed POD)</h6>
            
            <div class="d-flex flex-column gap-3" id="completed-pod-list">
                <!-- Loaded dynamically -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let canvas, ctx, isDrawing = false;

    // Initialize Canvas Signature Pad
    function initSignaturePad() {
        canvas = document.getElementById('signature-pad');
        if (!canvas) return;
        
        ctx = canvas.getContext('2d');
        
        // Adjust canvas resolution internal size to match displayed container width/height
        adjustCanvasSize();
        
        ctx.strokeStyle = '#6366f1';
        ctx.lineWidth = 3;
        ctx.lineCap = 'round';

        // Mouse Events
        canvas.addEventListener('mousedown', (e) => {
            isDrawing = true;
            const pos = getMousePos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        });
        canvas.addEventListener('mousemove', (e) => {
            if (!isDrawing) return;
            const pos = getMousePos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        });
        canvas.addEventListener('mouseup', () => {
            isDrawing = false;
            saveSignatureData();
        });
        canvas.addEventListener('mouseleave', () => {
            isDrawing = false;
        });

        // Touch Events (For Mobile Support)
        canvas.addEventListener('touchstart', (e) => {
            isDrawing = true;
            const touch = e.touches[0];
            const pos = getTouchPos(touch);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            e.preventDefault();
        });
        canvas.addEventListener('touchmove', (e) => {
            if (!isDrawing) return;
            const touch = e.touches[0];
            const pos = getTouchPos(touch);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            e.preventDefault();
        });
        canvas.addEventListener('touchend', () => {
            isDrawing = false;
            saveSignatureData();
        });

        // Handle window resizing
        window.addEventListener('resize', adjustCanvasSize);
    }

    function adjustCanvasSize() {
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = rect.height;
        // Restore stroke styling as resizing clears it
        ctx.strokeStyle = '#6366f1';
        ctx.lineWidth = 3;
        ctx.lineCap = 'round';
    }

    function getMousePos(e) {
        const rect = canvas.getBoundingClientRect();
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }

    function getTouchPos(touch) {
        const rect = canvas.getBoundingClientRect();
        return {
            x: touch.clientX - rect.left,
            y: touch.clientY - rect.top
        };
    }

    function clearSignature() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById('signature-data').value = '';
    }

    function saveSignatureData() {
        // Save base64 representation of drawing
        document.getElementById('signature-data').value = canvas.toDataURL();
    }

    // Preview mock uploaded photo
    let mockPhotoBase64 = '';
    function previewPhoto(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('photo-preview-el');
            output.src = reader.result;
            output.classList.remove('d-none');
            mockPhotoBase64 = reader.result;
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Render page views
    function populatePodSelectors() {
        const dos = db.getData('deliveryOrders');
        const podSelect = document.getElementById('pod-do');
        
        // Clear but keep first
        podSelect.innerHTML = '<option value="" disabled selected>-- Pilih Delivery Order --</option>';
        
        // Only select DOs with status = 'In Transit'
        const transitDos = dos.filter(d => d.status === 'In Transit');
        
        transitDos.forEach(d => {
            podSelect.innerHTML += `<option value="${d.id}">${d.doNumber} - ${d.destination.split(',')[0]}</option>`;
        });

        if (transitDos.length === 0) {
            podSelect.innerHTML = '<option value="" disabled>-- Tidak ada DO sedang transit --</option>';
        }
    }

    function onDoChange() {
        const doId = parseInt(document.getElementById('pod-do').value);
        const dos = db.getData('deliveryOrders');
        const drivers = db.getData('drivers');
        
        const d = dos.find(doObj => doObj.id === doId);
        const previewBox = document.getElementById('do-preview-box');
        
        if (d) {
            const driverObj = drivers.find(drv => drv.id === d.driverId);
            document.getElementById('preview-dest').innerText = d.destination;
            document.getElementById('preview-driver').innerText = driverObj ? driverObj.name : 'Unassigned';
            previewBox.classList.remove('d-none');
        } else {
            previewBox.classList.add('d-none');
        }
    }

    function renderCompletedPods() {
        const dos = db.getData('deliveryOrders');
        const drivers = db.getData('drivers');
        const container = document.getElementById('completed-pod-list');
        container.innerHTML = '';

        const completedDos = dos.filter(d => d.status === 'Delivered' && d.pod !== null);

        completedDos.forEach(d => {
            const driverObj = drivers.find(drv => drv.id === d.driverId);
            const localTime = new Date(d.pod.signedAt).toLocaleString('id-ID');
            
            // If there's an image uploader, show it or show generic cargo image
            const photoSrc = d.pod.photoUrl || 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=300&q=80';

            container.innerHTML += `
                <div class="erp-card p-3 bg-light-dark border border-light-dark">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-primary text-sm">${d.doNumber}</span>
                        <span class="badge bg-success text-xs"><i class="bi bi-check-circle"></i> Delivered</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-8 col-sm-9">
                            <div class="text-xs text-secondary mb-1"><strong>Penerima:</strong> ${d.pod.signedBy}</div>
                            <div class="text-xs text-secondary mb-1"><strong>Tgl Serah Terima:</strong> ${localTime}</div>
                            <div class="text-xs text-secondary mb-2"><strong>Driver Pengirim:</strong> ${driverObj ? driverObj.name : 'Unknown'}</div>
                            
                            <div class="d-flex align-items-center gap-2">
                                <div class="text-xs text-muted">Tanda Tangan:</div>
                                <div class="bg-white p-1 rounded border border-light-dark" style="height: 40px; width: 100px;">
                                    <img src="${d.pod.signatureImage}" class="img-fluid h-100 w-100 object-fit-contain" alt="Signature">
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-sm-3 text-end">
                            <img src="${photoSrc}" class="img-fluid rounded border border-light-dark object-fit-cover" style="height: 70px; width: 70px;" alt="Cargo photo">
                        </div>
                    </div>
                </div>
            `;
        });

        if (completedDos.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-folder-x d-block mb-2 fs-3"></i>Belum ada arsip tanda terima POD yang tersimpan.</div>';
        }
    }

    // Submit form
    document.getElementById('pod-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const doId = parseInt(document.getElementById('pod-do').value);
        const recipient = document.getElementById('pod-recipient').value.trim();
        const sigData = document.getElementById('signature-data').value;

        if (!sigData) {
            alert('Tanda tangan elektronik wajib digambar terlebih dahulu!');
            return;
        }

        const dos = db.getData('deliveryOrders');
        const d = dos.find(doObj => doObj.id === doId);

        if (d) {
            // Construct POD data structure
            const podObject = {
                signedBy: recipient,
                signedAt: new Date().toISOString(),
                signatureImage: sigData,
                photoUrl: mockPhotoBase64 || null
            };

            // Update DO status to "Delivered" and attach POD
            db.updateItem('deliveryOrders', doId, {
                status: "Delivered",
                pod: podObject
            });

            // Set Driver back to "Available" since delivery is done
            if (d.driverId) {
                db.updateItem('drivers', d.driverId, { status: "Available" });
            }

            // Log system audit
            const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
            db.logAction(activeUser.name, "Proof of Delivery", "POD Submission", `Berhasil submit POD untuk ${d.doNumber} (Diterima oleh ${recipient})`, "Success");

            alert(`POD untuk DO ${d.doNumber} berhasil disubmit. Status DO diubah ke Delivered.`);

            // Reset forms
            document.getElementById('pod-form').reset();
            document.getElementById('do-preview-box').classList.add('d-none');
            document.getElementById('photo-preview-el').classList.add('d-none');
            clearSignature();
            mockPhotoBase64 = '';

            // Refresh tables
            populatePodSelectors();
            renderCompletedPods();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        initSignaturePad();
        populatePodSelectors();
        renderCompletedPods();

        // Support URL parameter DO mapping
        const urlParams = new URLSearchParams(window.location.search);
        const shortcutId = parseInt(urlParams.get('id'));
        if (shortcutId) {
            const podSelect = document.getElementById('pod-do');
            // Check if DO is in selectors (could be transit status)
            const optExists = Array.from(podSelect.options).some(opt => parseInt(opt.value) === shortcutId);
            if (optExists) {
                podSelect.value = shortcutId;
                onDoChange();
            }
        }
    });
</script>
@endsection
