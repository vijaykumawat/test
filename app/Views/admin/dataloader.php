<!doctype html>
<html lang="en">

<head>
    <?= $this->include('admin/link'); ?>
    <link rel="stylesheet" href="<?= base_url('/assets/css/common.css') ?>" />
    <style>
    .btn-excel {
        background-color: #28a745;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
    }

    .btn-excel:hover {
        background-color: #218838;
    }

    .mapping-card {
        padding: .9rem;
        border: 1px solid #dde2e6;
        border-radius: .625rem;
        background: #ffffff;
    }

    .mapping-column {
        min-height: 200px;
        max-height: 240px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: .55rem;
        padding: .75rem;
    }

    .mapping-dropzone {
        border: 1px dashed #adb5bd;
        background: #f8f9fa;
        padding: .7rem .85rem;
        margin-bottom: .65rem;
        border-radius: .5rem;
        transition: border-color .15s ease, background .15s ease;
    }

    .mapping-dropzone.hovered,
    .mapping-dropzone:hover {
        border-color: #6c757d;
        background: #eef1f4;
    }

    .drop-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .4rem;
        font-size: .9rem;
        font-weight: 600;
    }

    .status {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .2rem .55rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
    }

    .status.mapped {
        background: #d1e7dd;
        color: #0f5132;
    }

    .status.unmapped {
        background: #fff3cd;
        color: #664d03;
    }

    .drop-value {
        margin-top: .4rem;
        font-size: .85rem;
        color: #495057;
    }

    .csv-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .32rem .6rem;
        margin: .15rem;
        border-radius: 999px;
        background: #e9ecef;
        color: #212529;
        cursor: grab;
        user-select: none;
        transition: transform .12s ease, background .12s ease;
        font-size: .83rem;
    }

    .csv-chip:hover {
        background: #d0d4d8;
        transform: translateY(-1px);
    }

    .csv-chip.dragging {
        opacity: .55;
        transform: scale(.97);
    }

    .chip-icon {
        margin-right: .35rem;
    }

    .csv-empty-state {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 140px;
        padding: .9rem;
        border: 1px dashed #ced4da;
        border-radius: .5rem;
        background: #f8f9fa;
        color: #6c757d;
        font-size: .9rem;
        text-align: center;
    }

    .csv-preview {
        max-height: 120px;
        overflow: auto;
        border: 1px solid #e9ecef;
        border-radius: .5rem;
        padding: .75rem;
        background: #ffffff;
    }

    .alert-box {
        margin-bottom: 1rem;
    }

    /* Ensure removing the topstrip leaves no empty gap */
    html,
    body {
        margin: 0;
        padding: 0;
    }

    #main-wrapper,
    .page-wrapper {
        padding-top: 0 !important;
    }

    .body-wrapper,
    .body-wrapper-inner {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    /* header/navbar safe reset */
    .app-header,
    .navbar {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

  
    #csvDropArea {
        border: 2px dashed #ccc;
        border-radius: 6px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    #csvDropArea.dragover {
        background-color: #f8f9fa;
        border-color: #198754;
        /* Bootstrap green */
    }

    
    </style>
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dbFields = [
                'regDate', 'regDateMonth', 'regNumber', 'ownerName',
                'address', 'vehicleMaker', 'vehicleModel', 'fuelType', 'saleAmt',
                'seatCapacity', 'mobile', 'expiryDate', 'prevInsuCompany', 'finance', 'telecaller'
            ];

            const fileInput = document.getElementById('csvUpload');
            const openMappingBtn = document.getElementById('openMappingBtn');
            const mappingModalEl = document.getElementById('mappingModal');
            const mappingModal = mappingModalEl ? new bootstrap.Modal(mappingModalEl, {
                backdrop: 'static',
                keyboard: false
            }) : null;
            const mappingCountBadge = document.getElementById('mappingCountBadge');
            const dbFieldsContainer = document.getElementById('dbFieldsContainer');
            const csvHeadersContainer = document.getElementById('csvHeadersContainer');
            const mappingInput = document.getElementById('mappingInput');
            const proceedMappingBtn = document.getElementById('proceedMappingBtn');
            const uploadBtn = document.getElementById('uploadBtn');
            const csvPreview = document.getElementById('csvPreview');
            const alertBox = document.getElementById('alertBox');

            let csvHeaders = [];
            let currentMapping = {};
            let lastFile = null;
            let deleteModalInstance = null;

            function clearChildren(el) {
                while (el && el.firstChild) el.removeChild(el.firstChild);
            }

            function parseCsvHeadersFromFile(file, cb) {
                const reader = new FileReader();
                reader.onload = e => {
                    const text = e.target.result || '';
                    const firstLine = text.split(/\r?\n/)[0] || '';
                    const headers = parseCsvLine(firstLine);
                    cb(headers.map(h => h.replace(/^\uFEFF/, '').trim()));
                };
                reader.readAsText(file.slice(0, 65536));
            }

            function parseCsvLine(line) {
                const res = [];
                let cur = '',
                    inQuotes = false;
                for (let i = 0; i < line.length; i++) {
                    const ch = line[i];
                    if (ch === '"') {
                        if (inQuotes && line[i + 1] === '"') {
                            cur += '"';
                            i++;
                        } else inQuotes = !inQuotes;
                    } else if (ch === ',' && !inQuotes) {
                        res.push(cur);
                        cur = '';
                    } else cur += ch;
                }
                res.push(cur);
                return res;
            }

            function renderMappingUI() {
                clearChildren(dbFieldsContainer);
                clearChildren(csvHeadersContainer);

                const mappedHeaders = new Set(Object.values(currentMapping).filter(Boolean).map(h => h
                    .toLowerCase()));

                dbFields.forEach(f => {
                    
                    const dz = document.createElement('div');
                    dz.className = 'mapping-dropzone';
                    dz.dataset.field = f;
                    const mappedValue = currentMapping[f];
                    const statusClass = mappedValue ? 'mapped' : 'unmapped';
                    const statusText = mappedValue ? '✔ mapped' : '⚠ unmapped';

                    dz.innerHTML =
                        `<div class="drop-title"><span>${f}</span><span class="status ${statusClass}">${statusText}</span></div><div class="drop-value">${mappedValue || '<em>Drag a header here</em>'}</div>`;
                    dz.addEventListener('dragover', e => e.preventDefault());
                    dz.addEventListener('dragenter', () => dz.classList.add('hovered'));
                    dz.addEventListener('dragleave', () => dz.classList.remove('hovered'));
                    dz.addEventListener('drop', e => {
                        dz.classList.remove('hovered');
                        onDrop(e);
                    });
                    dbFieldsContainer.appendChild(dz);
                });

                let unmappedCount = 0;
                csvHeaders.forEach(h => {
                    if (mappedHeaders.has(h.toLowerCase())) {
                        return;
                    }
                    unmappedCount++;
                    const chip = document.createElement('div');
                    chip.className = 'csv-chip';
                    chip.draggable = true;
                    chip.innerHTML = `<span class="chip-icon">☰</span>${h}`;
                    chip.dataset.header = h;
                    chip.addEventListener('dragstart', onDragStart);
                    csvHeadersContainer.appendChild(chip);
                });

                if (unmappedCount === 0) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'csv-empty-state';
                    placeholder.textContent = 'All headers mapped';
                    csvHeadersContainer.appendChild(placeholder);
                }

                if (mappingCountBadge) {
                    mappingCountBadge.textContent = `${unmappedCount} unmapped`;
                    mappingCountBadge.className =
                        `badge ${unmappedCount > 0 ? 'bg-warning text-dark' : 'bg-success text-white'} ms-2`;
                }

                if (proceedMappingBtn) {
                    proceedMappingBtn.disabled = unmappedCount > 0;
                    proceedMappingBtn.textContent = unmappedCount > 0 ? 'Complete Mapping' : 'Proceed';
                }

                // Preview first few rows
                if (lastFile) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const text = e.target.result || '';
                        const rows = text.split(/\r?\n/).filter(r => r.trim() !== '');
                        const headers = rows[0] ? parseCsvLine(rows[0]) : [];
                        let previewHtml = '<div class="table-responsive border rounded-2"><table class="table mb-0"><thead class="table-info"><tr>';
                        headers.forEach(h => previewHtml += `<th>${h}</th>`);
                        previewHtml += '</tr></thead><tbody>';
                        rows.slice(1, 3).forEach(r => {
                            const cells = parseCsvLine(r);
                            previewHtml += '<tr>';
                            cells.forEach(c => previewHtml += `<td>${c}</td>`);
                            previewHtml += '</tr>';
                        });
                        previewHtml += '</tbody></table></div>';
                        csvPreview.innerHTML = previewHtml;
                    };
                    reader.readAsText(lastFile.slice(0, 200000));
                } else {
                    csvPreview.innerHTML =
                        '<div class="csv-empty-state">CSV preview will appear after selecting a file</div>';
                }

                validateMapping();
            }

            function onDragStart(e) {
                e.dataTransfer.setData('text/plain', e.target.dataset.header);
            }

            function onDrop(e) {
                e.preventDefault();
                const header = e.dataTransfer.getData('text/plain');
                const field = e.currentTarget.dataset.field;
                if (header && field) {
                    // remove header from any other mapping
                    for (const k in currentMapping)
                        if (currentMapping[k] === header) currentMapping[k] = null;
                    currentMapping[field] = header;
                    mappingInput.value = JSON.stringify(currentMapping);
                    renderMappingUI();
                    validateMapping();
                }
            }

            function validateMapping() {
                const missing = dbFields.filter(f => !currentMapping[f] || currentMapping[f] === '');
                if (missing.length === 0) {
                    uploadBtn.disabled = false;
                    setAlert('Mapping complete. Ready to upload.', 'success');
                    return true;
                }
                uploadBtn.disabled = true;
                setAlert('Mapping incomplete: ' + missing.join(', '), 'warning');
                return false;
            }

            function setAlert(msg, type) {
                if (!alertBox) return;
                alertBox.innerHTML =
                    `<div class="alert ${type==='success'?'bg-success-subtle text-success':'bg-warning-subtle text-warning'} alert-dismissible fade show mb-3" role="alert">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            }

            fileInput.addEventListener('change', e => {
                const file = e.target.files[0];
                if (!file) return;
                lastFile = file;
                if (mappingModal) mappingModal.show();
                parseCsvHeadersFromFile(file, headers => {
                    csvHeaders = headers;
                    currentMapping = {};
                    mappingInput.value = '';

                    const lowerCsv = csvHeaders.map(h => h.toLowerCase());
                    dbFields.forEach(f => {
                        const index = lowerCsv.indexOf(f.toLowerCase());
                        if (index !== -1) {
                            currentMapping[f] = csvHeaders[index];
                        }
                    });

                    mappingInput.value = JSON.stringify(currentMapping);
                    renderMappingUI();

                    if (Object.keys(currentMapping).length === dbFields.length) {
                        setAlert('All required fields auto-mapped. Ready to upload.',
                            'success');
                    } else {
                        setAlert('File loaded. Please map the remaining fields.', 'warning');
                    }
                });
            });

            openMappingBtn.addEventListener('click', () => {
                if (!fileInput.files[0]) {
                    setAlert('Please select a CSV file first.', 'warning');
                    return;
                }
                if (mappingModal) mappingModal.show();
            });

            if (proceedMappingBtn) {
                proceedMappingBtn.addEventListener('click', () => {
                    if (validateMapping()) {
                        if (mappingModal) mappingModal.hide();
                        setAlert('Mapping complete. You may now upload.', 'success');
                    } else {
                        setAlert('Please complete all required mappings before proceeding.', 'warning');
                    }
                });
            }

            document.getElementById('uploadBtn').addEventListener('click', async () => {
                if (!lastFile) {
                    setAlert('No CSV selected', 'warning');
                    return;
                }
                if (!mappingInput.value || !validateMapping()) {
                    setAlert('Please complete mapping before upload', 'warning');
                    return;
                }

                try {
                    const form = new FormData();
                    form.append('csv_file', lastFile);
                    form.append('mapping', mappingInput.value);

                    uploadBtn.disabled = true;
                    uploadBtn.textContent = 'Uploading...';

                    const resp = await fetch('<?= site_url('admin/upload-data') ?>', {
                        method: 'POST',
                        body: form
                    });
                    const json = await resp.json();
                    if (json.success) {
                        setAlert(json.message || 'Uploaded', 'success');
                    } else {
                        setAlert(json.message || 'Upload failed', 'warning');
                    }
                } catch (err) {
                    setAlert('Upload error: ' + err.message, 'warning');
                } finally {
                    uploadBtn.disabled = false;
                    uploadBtn.textContent = 'Upload';
                }
            });

            // Setup delete confirmation modal listener
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', async () => {
                    confirmDeleteBtn.disabled = true;
                    confirmDeleteBtn.textContent = 'Deleting...';

                    try {
                        const resp = await fetch('<?= site_url('admin/remove-all-data') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        });
                        const json = await resp.json();
                        
                        if (json.success) {
                            const alertBox = document.getElementById('alertBox');
                            alertBox.innerHTML =
                                `<div class="alert bg-success-subtle text-success alert-dismissible fade show mb-3" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-circle-check me-2 fs-4"></i>
                                        ${json.message || 'All data deleted successfully!'}
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>`;
                            
                            // Close modal using backdrop click or hide
                            if (deleteModalInstance) {
                                deleteModalInstance.hide();
                            }
                            
                            // Refresh page after 2 seconds
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            const alertBox = document.getElementById('alertBox');
                            alertBox.innerHTML =
                                `<div class="alert bg-danger-subtle text-danger alert-dismissible fade show mb-3" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-alert-circle me-2 fs-4"></i>
                                        ${json.message || 'Failed to delete data.'}
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>`;
                            confirmDeleteBtn.disabled = false;
                            confirmDeleteBtn.textContent = 'Delete All Data';
                        }
                    } catch (err) {
                        const alertBox = document.getElementById('alertBox');
                        alertBox.innerHTML =
                            `<div class="alert bg-danger-subtle text-danger alert-dismissible fade show mb-3" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-alert-circle me-2 fs-4"></i>
                                    Delete error: ${err.message}
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`;
                        confirmDeleteBtn.disabled = false;
                        confirmDeleteBtn.textContent = 'Delete All Data';
                    }
                });
            }

        });

        // Remove All Data with Confirmation
        function removeAllData() {
            deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteConfirmModal'), {
                backdrop: 'static',
                keyboard: false
            });
            deleteModalInstance.show();
        }

        // Sidebar and other functions can go here
        
        </script>

        <!-- Sidebar Start -->
        <?php include 'sidebar.php'; ?>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
             <?= $this->include('admin/header'); ?>
           <!--  Header End -->
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- start Event Registration -->
                            <div class="card">
                                <form action="<?= site_url('admin/upload-data') ?>" method="post"
                                    enctype="multipart/form-data" class="upload-box" id="uploadForm">
                                    <div class="card-body">

                                        <h4 class="card-title"><i> <img
                                                    src="<?= base_url('assets/images/logos/1.png') ?>" width="30"
                                                    height="30" alt="Icon"> </i>&nbsp; Data Loader</h4>
                                        <p class="card-subtitle mb-2">CSV files only • Drag & Drop here • If data is
                                            available, it won't be overwritten</p>
                                        <?php if (session()->has('success')): ?>
                                        <div class="alert bg-success-subtle text-success alert-dismissible fade show mb-3"
                                            role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-circle-check me-2 fs-4"></i>
                                                <?= session('success') ?>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (session()->has('error')): ?>
                                        <div class="alert bg-danger-subtle text-danger alert-dismissible fade show mb-3"
                                            role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-alert-circle me-2 fs-4"></i>
                                                <?= session('error') ?>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (session()->has('warning')): ?>
                                        <div class="alert bg-warning-subtle text-warning alert-dismissible fade show mb-3"
                                            role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-alert-triangle me-2 fs-4"></i>
                                                <?= session('warning') ?>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>
                                        <div id="alertBox"></div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Select File</label>
                                                    <div class="input-group flex-nowrap">
                                                        <div class="custom-file">
                                                            <div class="upload-box border border-dashed p-4 text-center"
                                                                id="csvDropArea">
                                                                <p class="mb-2">Drag & Drop CSV file here</p>
                                                                <p class="small text-muted">or click below to select</p>
                                                                <input id="csvUpload" type="file"
                                                                    class="form-control mt-2" name="csv_file"
                                                                    accept=".csv,text/csv" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-3 border-top">
                                        <div class="d-flex flex-wrap gap-6 align-items-center">
                                            <div>

                                            </div>
                                            <div class="ms-auto d-flex flex-wrap gap-6 align-items-center">
                                                <div class="ms-auto">
                                                    <button type="button" onclick="removeAllData()"
                                                        class="btn btn-outline-danger btn-sm shadow-sm">
                                                        <i class="ti ti-trash me-2"></i>
                                                        Remove all Data
                                                    </button>
                                                </div>
                                                <div class="ms-auto">
                                                    <button type="button" id="openMappingBtn"
                                                        class="btn btn-outline-secondary btn-sm shadow-sm me-2">Toggle
                                                        Mapping</button>
                                                    <button type="button" id="uploadBtn"
                                                        class="btn btn-outline-success btn-sm shadow-sm" disabled>
                                                        <i class="ti ti-upload me-2"></i>
                                                        Upload
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- end Event Registration -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <!-- start Event Registration -->
                            <div class="card">
                                <div class="table-responsive border rounded-2">
                                    <div style="margin-top: 20px;" class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="mb-0">&nbsp;&nbsp;&nbsp;&nbsp; Preview</h6>
                                        <small class="text-muted" style="margin-right: 30px;">First 2 rows</small>
                                    </div>
                                    <div id="csvPreview" class="csv-preview">

                                    </div>
                                </div>
                                <div class="pagination" id="pagination"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="mappingModal" tabindex="-1" aria-labelledby="mappingModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mappingModalLabel">Map CSV to Database Fields <span
                                        id="mappingCountBadge" class="badge bg-warning text-dark ms-2">0
                                        unmapped</span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row gx-3">
                                    <div class="col-lg-5">
                                        <div class="mapping-card">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="mb-0">Required DB Fields</h6>
                                                <span class="badge bg-secondary">Required</span>
                                            </div>
                                            <div id="dbFieldsContainer" class="mapping-column"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="mapping-card">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="mb-0">CSV Headers</h6>
                                                <span class="badge bg-secondary">Draggable</span>
                                            </div>
                                            <div id="csvHeadersContainer" class="mapping-column mb-3"></div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="mappingInput" name="mapping" value="" />
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-dark btn-sm shadow-sm"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="proceedMappingBtn"
                                    class="btn btn-outline-success btn-sm shadow-sm" disabled>Complete
                                    Mapping</button>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger-subtle">
                                <h5 class="modal-title text-danger" id="deleteConfirmLabel">
                                    <i class="ti ti-alert-triangle me-2"></i>Confirm Delete
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2"><strong>Warning!</strong> This action will permanently delete all records from the data table.</p>
                                <p class="text-muted mb-0">This action cannot be undone. Are you sure you want to proceed?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="confirmDeleteBtn"
                                    class="btn btn-danger btn-sm shadow-sm">Delete All Data</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?= $this->include('admin/script'); ?>
    
    <script>
    // Force-clear any top padding/margin that runtime scripts may add
    (function() {
        function clearTopSpace() {
            try {
                var sels = ['body', '#main-wrapper', '.page-wrapper', '.body-wrapper', '.body-wrapper-inner',
                    '.app-header', '.navbar', 'html'
                ];
                sels.forEach(function(s) {
                    var el = (s === 'body' || s === 'html') ? (s === 'body' ? document.body : document
                        .documentElement) : document.querySelector(s);
                    if (el) {
                        el.style.paddingTop = '0px';
                        el.style.marginTop = '0px';
                        el.style.top = '0px';
                    }
                });
            } catch (e) {
                console && console.error && console.error(e)
            }
        }
        // run after load and slightly after to override any delayed script
        window.addEventListener('load', function() {
            clearTopSpace();
            setTimeout(clearTopSpace, 250);
            setTimeout(clearTopSpace, 1000);
        });
        document.addEventListener('DOMContentLoaded', clearTopSpace);
    })();
    </script>
    <script>
    var baseUrl = "<?= base_url() ?>";
</script>

</body>

</html>