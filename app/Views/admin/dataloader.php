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

<?php
$restrictedUserName = 'TestUser';
$restrictedEmployeeId = 'cef99519ba925515';
$currentEmployeeName = session()->get('employeeName');
$currentEmployeeId = session()->get('employeeId');
$isRestrictedUser = (strtolower((string) $currentEmployeeName) === strtolower($restrictedUserName)) || (string) $currentEmployeeId === $restrictedEmployeeId;
?>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Field sets per table type: 'data' -> Telecaller Data, 'expiry' -> Expiry Data
            const dataDbFields = [
                'regDate', 'regDateMonth', 'regNumber', 'ownerName',
                'address', 'vehicleMaker', 'vehicleModel', 'fuelType', 'saleAmt',
                'seatCapacity', 'cubicCapacity', 'mobile', 'expiryDate', 'prevInsuCompany', 'finance',
                'telecaller'
            ];

            // Expiry Data has only two columns
            const expiryDbFields = ['regNumber', 'employeeId'];

            // Active flow decided by the selected table ('data' | 'expiry')
            let activeMode = 'data';

            function getActiveDbFields() {
                return activeMode === 'expiry' ? expiryDbFields : dataDbFields;
            }

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

            // Expiry Data modal elements
            const expiryModalEl = document.getElementById('expiryMappingModal');
            const expiryModal = expiryModalEl ? new bootstrap.Modal(expiryModalEl, {
                backdrop: 'static',
                keyboard: false
            }) : null;
            const expiryCountBadge = document.getElementById('expiryCountBadge');
            const expiryDbFieldsContainer = document.getElementById('expiryDbFieldsContainer');
            const expiryCsvHeadersContainer = document.getElementById('expiryCsvHeadersContainer');
            const expiryMappingInput = document.getElementById('expiryMappingInput');
            const proceedExpiryMappingBtn = document.getElementById('proceedExpiryMappingBtn');

            const uploadBtn = document.getElementById('uploadBtn');
            const csvPreview = document.getElementById('csvPreview');
            const alertBox = document.getElementById('alertBox');

            let csvHeaders = [];
            let currentMapping = {};
            let expiryMapping = {};
            let lastFile = null;
            let csvRowCount = 0;
            let deleteModalInstance = null;
            const isRestrictedUser =
                <?= json_encode($isRestrictedUser, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
            const maxAllowedRows = 20;
            const rowLimitModalEl = document.getElementById('rowLimitModal');
            const rowLimitModal = rowLimitModalEl ? new bootstrap.Modal(rowLimitModalEl, {
                backdrop: 'static',
                keyboard: false
            }) : null;
            const rowLimitMessageEl = document.getElementById('rowLimitMessage');
            const tableSelect = document.querySelector('select[name="table"]');
            const tableWarningModalEl = document.getElementById('tableWarningModal');
            const tableWarningModal = tableWarningModalEl ? new bootstrap.Modal(tableWarningModalEl) : null;

            // Block the file picker until a table is selected
            fileInput.addEventListener('click', e => {
                if (!tableSelect || !tableSelect.value) {
                    e.preventDefault();
                    if (tableWarningModal) tableWarningModal.show();
                }
            });

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

            function renderMappingUI(mode = activeMode) {
                const fields = mode === 'expiry' ? expiryDbFields : dataDbFields;
                const mappingStore = mode === 'expiry' ? expiryMapping : currentMapping;
                const dbContainer = mode === 'expiry' ? expiryDbFieldsContainer : dbFieldsContainer;
                const csvContainer = mode === 'expiry' ? expiryCsvHeadersContainer : csvHeadersContainer;
                const countBadge = mode === 'expiry' ? expiryCountBadge : mappingCountBadge;
                const proceedBtn = mode === 'expiry' ? proceedExpiryMappingBtn : proceedMappingBtn;

                clearChildren(dbContainer);
                clearChildren(csvContainer);

                const mappedHeaders = new Set(Object.values(mappingStore).filter(Boolean).map(h => h
                    .toLowerCase()));

                fields.forEach(f => {

                    const dz = document.createElement('div');
                    dz.className = 'mapping-dropzone';
                    dz.dataset.field = f;
                    dz.dataset.mode = mode;
                    const mappedValue = mappingStore[f];
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
                    dbContainer.appendChild(dz);
                });

                let unmappedCsvHeaderCount = 0;
                csvHeaders.forEach(h => {
                    if (mappedHeaders.has(h.toLowerCase())) {
                        return;
                    }
                    unmappedCsvHeaderCount++;
                    const chip = document.createElement('div');
                    chip.className = 'csv-chip';
                    chip.draggable = true;
                    chip.innerHTML = `<span class="chip-icon">☰</span>${h}`;
                    chip.dataset.header = h;
                    chip.addEventListener('dragstart', onDragStart);
                    csvContainer.appendChild(chip);
                });

                if (unmappedCsvHeaderCount === 0) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'csv-empty-state';
                    placeholder.textContent = 'All CSV headers are listed';
                    csvContainer.appendChild(placeholder);
                }

                const unmappedDbFieldCount = fields.filter(f => !mappingStore[f] || mappingStore[f] ===
                    '').length;

                if (countBadge) {
                    countBadge.textContent = `${unmappedDbFieldCount} db fields unmapped`;
                    countBadge.className =
                        `badge ${unmappedDbFieldCount > 0 ? 'bg-warning text-dark' : 'bg-success text-white'} ms-2`;
                }

                if (proceedBtn) {
                    proceedBtn.disabled = unmappedDbFieldCount > 0;
                    proceedBtn.textContent = unmappedDbFieldCount > 0 ? 'Complete Mapping' : 'Proceed';
                }

                // Preview first few rows
                if (lastFile) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const text = e.target.result || '';
                        const rows = text.split(/\r?\n/).filter(r => r.trim() !== '');
                        const headers = rows[0] ? parseCsvLine(rows[0]) : [];
                        let previewHtml =
                            '<div class="table-responsive border rounded-2"><table class="table mb-0"><thead class="table-info"><tr>';
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

                validateMapping(mode);
            }

            function onDragStart(e) {
                e.dataTransfer.setData('text/plain', e.target.dataset.header);
            }

            function onDrop(e) {
                e.preventDefault();
                const header = e.dataTransfer.getData('text/plain');
                const field = e.currentTarget.dataset.field;
                const mode = e.currentTarget.dataset.mode || activeMode;
                if (header && field) {
                    const mappingStore = mode === 'expiry' ? expiryMapping : currentMapping;
                    // remove header from any other mapping
                    for (const k in mappingStore)
                        if (mappingStore[k] === header) mappingStore[k] = null;
                    mappingStore[field] = header;
                    if (mode === 'expiry') {
                        expiryMappingInput.value = JSON.stringify(expiryMapping);
                    } else {
                        mappingInput.value = JSON.stringify(currentMapping);
                    }
                    renderMappingUI(mode);
                    validateMapping(mode);
                }
            }

            function validateMapping(mode = activeMode) {
                const fields = mode === 'expiry' ? expiryDbFields : dataDbFields;
                const mappingStore = mode === 'expiry' ? expiryMapping : currentMapping;
                const missing = fields.filter(f => !mappingStore[f] || mappingStore[f] === '');
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
                const alertClass = type === 'success' ?
                    'bg-success-subtle text-success' :
                    type === 'danger' ?
                    'bg-danger-subtle text-danger' :
                    'bg-warning-subtle text-warning';
                alertBox.innerHTML =
                    `<div class="alert ${alertClass} alert-dismissible fade show mb-3" role="alert">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            }

            function showRowLimitModal(message) {
                if (rowLimitMessageEl) {
                    rowLimitMessageEl.textContent = message;
                }
                if (rowLimitModal) {
                    rowLimitModal.show();
                }
            }

            fileInput.addEventListener('change', e => {
                const file = e.target.files[0];
                if (!file) return;
                lastFile = file;

                // Decide which flow to run based on the selected table
                activeMode = (tableSelect && tableSelect.value === 'expiry') ? 'expiry' : 'data';

                const reader = new FileReader();
                reader.onload = evt => {
                    const text = evt.target.result || '';
                    const rows = text.split(/\r?\n/).filter(r => r.trim() !== '');
                    const headers = rows[0] ? parseCsvLine(rows[0]) : [];
                    csvRowCount = Math.max(0, rows.length - 1);

                    csvHeaders = headers;

                    const activeFields = getActiveDbFields();
                    const lowerCsv = csvHeaders.map(h => h.toLowerCase());

                    if (activeMode === 'expiry') {
                        expiryMapping = {};
                        expiryMappingInput.value = '';
                        activeFields.forEach(f => {
                            const index = lowerCsv.indexOf(f.toLowerCase());
                            if (index !== -1) {
                                expiryMapping[f] = csvHeaders[index];
                            }
                        });
                        expiryMappingInput.value = JSON.stringify(expiryMapping);
                    } else {
                        currentMapping = {};
                        mappingInput.value = '';
                        activeFields.forEach(f => {
                            const index = lowerCsv.indexOf(f.toLowerCase());
                            if (index !== -1) {
                                currentMapping[f] = csvHeaders[index];
                            }
                        });
                        mappingInput.value = JSON.stringify(currentMapping);
                    }

                    renderMappingUI(activeMode);

                    if (isRestrictedUser && csvRowCount > maxAllowedRows) {
                        if (activeMode === 'expiry') {
                            if (expiryModal) expiryModal.hide();
                        } else if (mappingModal) {
                            mappingModal.hide();
                        }
                        uploadBtn.disabled = true;
                        showRowLimitModal(
                            `This account is limited to ${maxAllowedRows} data rows per upload. This file contains ${csvRowCount} rows.`
                            );
                        setAlert(
                            `Upload blocked: this account can only upload up to ${maxAllowedRows} rows. Your file contains ${csvRowCount} rows.`,
                            'danger');
                        return;
                    }

                    if (activeMode === 'expiry') {
                        if (expiryModal) expiryModal.show();
                    } else if (mappingModal) {
                        mappingModal.show();
                    }

                    const mappingStore = activeMode === 'expiry' ? expiryMapping : currentMapping;
                    if (Object.keys(mappingStore).length === activeFields.length) {
                        setAlert('All required fields auto-mapped. Ready to upload.',
                            'success');
                    } else {
                        setAlert('File loaded. Please map the remaining fields.', 'warning');
                    }
                };
                reader.readAsText(file.slice(0, 200000));
            });

            openMappingBtn.addEventListener('click', () => {
                if (!fileInput.files[0]) {
                    setAlert('Please select a CSV file first.', 'warning');
                    return;
                }
                if (activeMode === 'expiry') {
                    if (expiryModal) expiryModal.show();
                } else if (mappingModal) {
                    mappingModal.show();
                }
            });

            if (proceedMappingBtn) {
                proceedMappingBtn.addEventListener('click', () => {
                    if (validateMapping('data')) {
                        if (mappingModal) mappingModal.hide();
                        setAlert('Mapping complete. You may now upload.', 'success');
                    } else {
                        setAlert('Please complete all required mappings before proceeding.', 'warning');
                    }
                });
            }

            if (proceedExpiryMappingBtn) {
                proceedExpiryMappingBtn.addEventListener('click', () => {
                    if (validateMapping('expiry')) {
                        if (expiryModal) expiryModal.hide();
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
                const mappingValue = activeMode === 'expiry' ? expiryMappingInput.value : mappingInput.value;
                if (!mappingValue || !validateMapping(activeMode)) {
                    setAlert('Please complete mapping before upload', 'warning');
                    return;
                }

                if (isRestrictedUser && csvRowCount > maxAllowedRows) {
                    showRowLimitModal(
                        `This account is limited to ${maxAllowedRows} data rows per upload. This file contains ${csvRowCount} rows.`
                        );
                    setAlert(
                        `Upload blocked: this account can only upload up to ${maxAllowedRows} rows. Your file contains ${csvRowCount} rows.`,
                        'danger');
                    return;
                }

                try {
                    const form = new FormData();
                    form.append('csv_file', lastFile);
                    form.append('table', tableSelect ? tableSelect.value : '');
                    form.append('mapping', mappingValue);

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
                        const resp = await fetch('<?= site_url('admin/remove-previous-data') ?>', {
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
            const confirmDeleteAllBtn = document.getElementById('confirmDeleteAllBtn');
            if (confirmDeleteAllBtn) {
                confirmDeleteAllBtn.addEventListener('click', async () => {
                    confirmDeleteAllBtn.disabled = true;
                    confirmDeleteAllBtn.textContent = 'Deleting...';

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
                            confirmDeleteAllBtn.disabled = false;
                            confirmDeleteAllBtn.textContent = 'Delete All Data';
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
                        confirmDeleteAllBtn.disabled = false;
                        confirmDeleteAllBtn.textContent = 'Delete All Data';
                    }
                });
            }

        });

        // Remove All Data with Confirmation
        function removePreviousData() {
            deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteConfirmModal'), {
                backdrop: 'static',
                keyboard: false
            });
            deleteModalInstance.show();
        }

        function removeAllData() {
            deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteAllConfirmModal'), {
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
                                                    <label class="form-label">Select Table</label>
                                                    <div class="form-group">
                                                        <select class="form-control select2" placeholder="Select Status"
                                                            name="table" style="width: 100%;" autofocus required>
                                                            <option></option>
                                                            <option value="data">Telecaller Data</option>
                                                            <option value="expiry">Expiry Data</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

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
                                                        Remove All Data
                                                    </button>
                                                </div>
                                                <div class="ms-auto">
                                                    <button type="button" onclick="removePreviousData()"
                                                        class="btn btn-outline-danger btn-sm shadow-sm">
                                                        <i class="ti ti-trash me-2"></i>
                                                        Remove Previous Data
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
                                    <div style="margin-top: 20px;"
                                        class="d-flex align-items-center justify-content-between mb-2">
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

                <!-- Expiry Data Mapping Modal -->
                <div class="modal fade" id="expiryMappingModal" tabindex="-1"
                    aria-labelledby="expiryMappingModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="expiryMappingModalLabel">Map CSV to Expiry Data Columns
                                    <span id="expiryCountBadge" class="badge bg-warning text-dark ms-2">0
                                        unmapped</span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted small mb-3">Expiry Data requires these columns:
                                    <strong>RegNumber</strong>, <strong>EmployeeId</strong></p>
                                <div class="row gx-3">
                                    <div class="col-lg-5">
                                        <div class="mapping-card">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="mb-0">Required DB Fields</h6>
                                                <span class="badge bg-secondary">Required</span>
                                            </div>
                                            <div id="expiryDbFieldsContainer" class="mapping-column"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="mapping-card">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="mb-0">CSV Headers</h6>
                                                <span class="badge bg-secondary">Draggable</span>
                                            </div>
                                            <div id="expiryCsvHeadersContainer" class="mapping-column mb-3"></div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="expiryMappingInput" name="expiry_mapping" value="" />
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-dark btn-sm shadow-sm"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="proceedExpiryMappingBtn"
                                    class="btn btn-outline-success btn-sm shadow-sm" disabled>Complete
                                    Mapping</button>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row Limit Modal -->
                <div class="modal fade" id="rowLimitModal" tabindex="-1" aria-labelledby="rowLimitLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger-subtle">
                                <h5 class="modal-title text-danger" id="rowLimitLabel">
                                    <i class="ti ti-alert-triangle me-2"></i>Upload Limit Exceeded
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2"><strong>Upload blocked.</strong></p>
                                <p id="rowLimitMessage" class="text-muted mb-0"></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-danger btn-sm shadow-sm"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Not Selected Warning Modal -->
                <div class="modal fade" id="tableWarningModal" tabindex="-1" aria-labelledby="tableWarningLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-warning-subtle">
                                <h5 class="modal-title text-warning" id="tableWarningLabel">
                                    <i class="ti ti-alert-triangle me-2"></i>Warning
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-0 fw-bold">First select table</p>
                                <p class="text-muted mb-0 mt-2">Please choose a table (Data or Expiry) before
                                    selecting a file.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-warning btn-sm shadow-sm"
                                    data-bs-dismiss="modal">OK</button>
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
                                <p class="mb-2"><strong>Warning!</strong> This action will permanently delete all
                                    Previous records from the data table.</p>
                                <p class="text-muted mb-0">This action cannot be undone. Are you sure you want to
                                    proceed?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="confirmDeleteBtn"
                                    class="btn btn-danger btn-sm shadow-sm">Delete Previous Data</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Delete All Confirmation Modal -->
                <div class="modal fade" id="deleteAllConfirmModal" tabindex="-1" aria-labelledby="deleteAllConfirmLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger-subtle">
                                <h5 class="modal-title text-danger" id="deleteAllConfirmLabel">
                                    <i class="ti ti-alert-triangle me-2"></i>Confirm Delete
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2"><strong>Warning!</strong> This action will permanently delete all
                                    records from the data table.</p>
                                <p class="text-muted mb-0">This action cannot be undone. Are you sure you want to
                                    proceed?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="confirmDeleteAllBtn"
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