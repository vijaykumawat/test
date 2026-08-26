<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flexy Free Bootstrap Admin Template by WrapPixel</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/toast.css') ?>" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
    body {
        font-family: "Segoe UI", Arial, sans-serif;
        margin: 0;
        background-color: #f5f7fa;
        color: #333;
    }

    .wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        width: 100%;
    }

    .topbar {
        background: #ffffff;
        border-bottom: 1px solid #ddd;
        padding: 14px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .topbar-menu {
        display: flex;
        gap: 30px;
        align-items: center;
    }

    .topbar-menu a {
        text-decoration: none;
        color: #333;
        font-weight: 500;
        font-size: 14px;
        transition: color 0.3s;
    }

    .topbar-menu a:hover {
        color: #0069d9;
    }

    .topbar-btn {
        background: #0069d9;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
    }

    .container {
        display: flex;
        flex: 1;
        width: 100%;
        max-width: none;
        padding: 0;
        margin: 0;
    }

    .sidebar {
        width: 240px;
        background: #e8e8e8;
        color: #333;
        min-height: 100vh;
        padding-top: 20px;
        box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
        text-align: center;
        font-size: 18px;
        margin-bottom: 30px;
        color: #333;
    }

    .sidebar a {
        display: block;
        padding: 14px 22px;
        color: #333;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.3s, color 0.3s;
    }

    .sidebar a:hover {
        background: #d0d0d0;
        color: #0069d9;
    }

    .sidebar a.active {
        background: #0069d9;
        color: #fff;
    }

    .main {
        flex: 1;
        padding: 30px;
    }

    h2 {
        font-size: 22px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    table {
        margin: 20px 0;
        border-collapse: collapse;
        width: 100%;
        background-color: #ffffff;
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
        font-size: 14px;
    }

    th {
        background-color: #f0f2f5;
        font-weight: 600;
    }

    tr:nth-child(even) td {
        background-color: #fafafa;
    }

    .download-icon {
        text-decoration: none;
        color: #0069d9;
        font-size: 16px;
    }

    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .search-wrapper input {
        padding: 12px 40px 12px 16px;
        font-size: 14px;
        border: 2px solid #0069d9;
        border-radius: 8px;
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 105, 217, 0.1);
        width: 400px;
    }

    .search-wrapper input:focus {
        outline: none;
        border-color: #0053aa;
        box-shadow: 0 4px 12px rgba(0, 105, 217, 0.25);
    }

    .search-wrapper input::placeholder {
        color: #999;
    }

    .search-icon {
        position: absolute;
        right: 12px;
        color: #0069d9;
        font-size: 18px;
        pointer-events: none;
    }

    .pagination {
        margin-top: 20px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        margin: 0 2px;
        border: 1px solid #ddd;
        text-decoration: none;
        color: #0069d9;
        cursor: pointer;
        border-radius: 4px;
    }

    .pagination a:hover {
        background: #0069d9;
        color: #fff;
    }

    .pagination .active {
        background: #0069d9;
        color: #fff;
        border-color: #0069d9;
    }

    .loading {
        color: #666;
        font-style: italic;
    }

    .form-select {
        width: 120px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    /* Centered, prominent expiry entry area (Reg No + date + Save/Skip) */
    .expiry-entry-area {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 24px;
        margin: 15px auto 30px auto;
        width: 100%;
    }

    .expiry-reg-badge {
        font-size: 48px !important;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .expiry-date-input {
        min-width: 300px;
        max-width: 340px;
        padding: 14px 18px;
        font-size: 26px;
        font-weight: 700;
        border: 3px solid #ced4da;
        border-radius: 10px;
        background-color: #ffffff;
        text-align: center;
    }

    .expiry-date-input:focus {
        outline: none;
        border-color: #0069d9;
        box-shadow: 0 0 0 .2rem rgba(0, 105, 217, .15);
    }

    #saveExpiryBtn {
        font-size: 24px;
        font-weight: 700;
        padding: 12px 40px;
        border-radius: 10px;
    }

    #skipExpiryBtn {
        font-size: 24px;
        font-weight: 700;
        padding: 12px 32px;
        border-radius: 10px;
    }

    /* Ensure no top gap after removing app-topstrip */
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

    .app-header,
    .navbar {
        top: 0 !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    /* fix left sidebar when layout is fixed */
    #main-wrapper[data-layout="vertical"][data-sidebar-position="fixed"] .left-sidebar {
        top: 0 !important;
    }

    .body-wrapper .container-fluid,
    .body-wrapper .container-sm,
    .body-wrapper .container-md,
    .body-wrapper .container-lg,
    .body-wrapper .container-xl,
    .body-wrapper .container-xxl {
        padding-top: 100px;
    }

    .blur {
        background: url("http://www.wohn-blogger.de/wp-content/themes/itheme2/skins/gray/images/body-bg.png") repeat scroll 0 0 #D1D1D1;
        color: #4b584b;
    }

    .alSale {
        width: 100%;
        height: 100%;
        background-color: #F88379;
    }
    </style>
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Sidebar Start -->
        <?php include 'sidebar.php'; ?>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <?php include 'header.php'; ?>

            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="expiry-entry-area">
                                        <button type="button"
                                            class="btn bg-warning-subtle text-warning">
                                            <span class="badge text-bg-warning expiry-reg-badge"
                                                id="vehicleNumber"
                                                data-id="<?= esc($currentRecord['id'] ?? '') ?>">
                                                <?= esc($currentRecord['regNumber'] ?? 'No pending records') ?>
                                            </span>
                                        </button>
                                        <input type="date" id="expiryDateInput"
                                            class="expiry-date-input" title="Enter expiry date" value="<?= date('Y') ?>-01-01"
                                            autofocus>
                                        <button type="button" id="saveExpiryBtn"
                                            class="btn btn-success shadow-sm">Save</button>
                                        <button type="button" id="skipExpiryBtn"
                                            class="btn btn-outline-secondary shadow-sm">Skip</button>
                                    </div>

                                    <script>
                                    function formatVehicleBadge() {
                                        const badgeEl = document.getElementById("vehicleNumber");
                                        const rawText = badgeEl.textContent.trim();
                                        // Insert dash after 2 chars, 2 digits, 2 chars, then leave last 4 digits
                                        const formattedText = rawText.replace(/^([A-Z]{2})(\d{2})([A-Z]{2})(\d{4})$/,
                                        "$1-$2-$3-$4");
                                        badgeEl.textContent = formattedText;
                                    }
                                    formatVehicleBadge();
                                    </script>

                                    <script>
                                    // On page load, place cursor/focus on the expiry date input
                                    // so editing starts at "dd", then advances to "mm" and "yyyy"
                                    window.addEventListener('load', function() {
                                        setTimeout(function() {
                                            const expiryInput = document.getElementById("expiryDateInput");
                                            if (expiryInput) {
                                                expiryInput.focus();
                                            }
                                        }, 100);
                                    });

                                    // Default value for the expiry date input after each save
                                    // (mirrors the server-side value="<?= date('Y') ?>-01-01")
                                    function getDefaultExpiryValue() {
                                        return new Date().getFullYear() + "-01-01";
                                    }

                                    // Save expiry date via AJAX (Save button click or Enter key press)
                                    // <input type="date"> can briefly report an empty value right after
                                    // the last segment is typed, so read it with a short retry first
                                    async function readExpiryValue() {
                                        const expiryInput = document.getElementById("expiryDateInput");
                                        let value = expiryInput.value;
                                        if (!value) {
                                            await new Promise(resolve => setTimeout(resolve, 300));
                                            value = expiryInput.value;
                                        }
                                        return value;
                                    }

                                    async function saveExpiryDate() {
                                        const badgeEl = document.getElementById("vehicleNumber");
                                        const saveBtn = document.getElementById("saveExpiryBtn");
                                        const expiryInput = document.getElementById("expiryDateInput");

                                        const recordId = badgeEl.dataset.id;
                                        if (!recordId) {
                                            alert("No pending record to update.");
                                            return;
                                        }

                                        const expiryDate = await readExpiryValue();
                                        if (!expiryDate) {
                                            alert("Please select an expiry date.");
                                            expiryInput.focus();
                                            return;
                                        }

                                        saveBtn.disabled = true;
                                        try {
                                            const body = new URLSearchParams();
                                            body.append("id", recordId);
                                            body.append("expiryDate", expiryDate);

                                            const resp = await fetch("<?= site_url('employee/save-expiry-date') ?>", {
                                                method: "POST",
                                                headers: { "X-Requested-With": "XMLHttpRequest" },
                                                body: body
                                            });
                                            const json = await resp.json();

                                            if (json.success) {
                                                if (json.nextRecord) {
                                                    badgeEl.dataset.id = json.nextRecord.id;
                                                    badgeEl.textContent = json.nextRecord.regNumber;
                                                    formatVehicleBadge();
                                                } else {
                                                    badgeEl.dataset.id = "";
                                                    badgeEl.textContent = "All done!";
                                                }

                                                // Immediately reflect the saved record in the results table
                                                const savedRow = document.querySelector(
                                                    '#resultsTable tbody tr[data-id="' + recordId + '"]'
                                                );
                                                if (savedRow) {
                                                    const dateCell = savedRow.cells[1];
                                                    if (dateCell) {
                                                        dateCell.textContent = expiryDate;
                                                    }
                                                    const statusCell = savedRow.cells[2];
                                                    if (statusCell) {
                                                        statusCell.innerHTML =
                                                            '<span class="badge bg-success">Complete</span>';
                                                    }
                                                }

                                                expiryInput.value = getDefaultExpiryValue();
                                                expiryInput.focus();
                                                showSavedModal(json.message || "Expiry date saved successfully.");
                                            } else {
                                                alert(json.message || "Failed to save expiry date.");
                                            }
                                        } catch (err) {
                                            alert("Error: " + err.message);
                                        } finally {
                                            saveBtn.disabled = false;
                                        }
                                    }

                                    // Permanently skip the current record (status = 2 in DB).
                                    // Skipped records never come back in the pending queue.
                                    async function skipExpiryRecord() {
                                        const badgeEl = document.getElementById("vehicleNumber");
                                        const skipBtn = document.getElementById("skipExpiryBtn");
                                        const expiryInput = document.getElementById("expiryDateInput");

                                        const recordId = badgeEl.dataset.id;
                                        if (!recordId) {
                                            alert("No record to skip.");
                                            return;
                                        }

                                        skipBtn.disabled = true;
                                        try {
                                            const body = new URLSearchParams();
                                            body.append("id", recordId);

                                            const resp = await fetch("<?= site_url('employee/skip-expiry-date') ?>", {
                                                method: "POST",
                                                headers: { "X-Requested-With": "XMLHttpRequest" },
                                                body: body
                                            });
                                            const json = await resp.json();

                                            if (json.success) {
                                                if (json.nextRecord) {
                                                    badgeEl.dataset.id = json.nextRecord.id;
                                                    badgeEl.textContent = json.nextRecord.regNumber;
                                                    formatVehicleBadge();
                                                } else {
                                                    badgeEl.dataset.id = "";
                                                    badgeEl.textContent = "All done!";
                                                }

                                                // Mark the skipped row in the results table immediately
                                                const skippedRow = document.querySelector(
                                                    '#resultsTable tbody tr[data-id="' + recordId + '"]'
                                                );
                                                if (skippedRow) {
                                                    const statusCell = skippedRow.cells[2];
                                                    if (statusCell) {
                                                        statusCell.innerHTML =
                                                            '<span class="badge bg-secondary">Skipped</span>';
                                                    }
                                                }

                                                expiryInput.value = getDefaultExpiryValue();
                                                expiryInput.focus();
                                            } else {
                                                alert(json.message || "Failed to skip record.");
                                            }
                                        } catch (err) {
                                            alert("Error: " + err.message);
                                        } finally {
                                            skipBtn.disabled = false;
                                        }
                                    }

                                    // Show success modal after a record is saved (auto-hides after 2s).
                                    // Once the modal closes (auto-hide or OK/close button), the cursor
                                    // returns to the expiry date input for the next record.
                                    let savedModalHideTimer = null;
                                    function showSavedModal(message) {
                                        const modalEl = document.getElementById("expirySavedModal");
                                        if (!modalEl || typeof bootstrap === "undefined") return;
                                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                                        document.getElementById("expirySavedMessage").textContent = message;
                                        modal.show();
                                        if (savedModalHideTimer) {
                                            clearTimeout(savedModalHideTimer);
                                        }
                                        savedModalHideTimer = setTimeout(function() {
                                            modal.hide();
                                        }, 2000);

                                        // Refocus the expiry date input as soon as the modal is fully closed
                                        modalEl.addEventListener('hidden.bs.modal', function onFocusBack() {
                                            modalEl.removeEventListener('hidden.bs.modal', onFocusBack);
                                            const expiryInput = document.getElementById("expiryDateInput");
                                            if (expiryInput) {
                                                expiryInput.focus();
                                            }
                                        });
                                    }

                                    document.addEventListener("DOMContentLoaded", function() {
                                        const saveBtn = document.getElementById("saveExpiryBtn");
                                        const skipBtn = document.getElementById("skipExpiryBtn");
                                        const expiryInput = document.getElementById("expiryDateInput");

                                        if (saveBtn) {
                                            saveBtn.addEventListener("click", saveExpiryDate);
                                        }
                                        if (skipBtn) {
                                            skipBtn.addEventListener("click", skipExpiryRecord);
                                        }
                                        if (expiryInput) {
                                            expiryInput.addEventListener("keydown", function(e) {
                                                if (e.key === "Enter") {
                                                    e.preventDefault();
                                                    saveExpiryDate();
                                                }
                                            });
                                        }
                                    });
                                    </script>
                                </div>
                            </div>

                            <!-- Expiry Date Saved Success Modal -->
                            <div class="modal fade" id="expirySavedModal" tabindex="-1"
                                aria-labelledby="expirySavedModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success-subtle">
                                            <h5 class="modal-title text-success" id="expirySavedModalLabel">
                                                <i class="ti ti-circle-check me-2"></i>Success
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="mb-0" id="expirySavedMessage">Expiry date saved successfully.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-success btn-sm shadow-sm"
                                                data-bs-dismiss="modal">OK</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <h2>Expiry Data</h2>
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                        <div></div>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                             <div class="search-wrapper">
                                                 <input type="text" id="searchBox"
                                                     placeholder="Search by reg no, expiry date or status...">
                                                 <span class="search-icon">🔍</span>
                                             </div>
                                             <select id="rowsPerPage" class="form-select">
                                                <option value="10">10</option>
                                                <option value="25" selected>25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                                <option value="200">200</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="table-responsive border rounded-2">
                                        <table class="table mb-0" id='resultsTable'>
                                            <thead class="table-info">
                                                <!-- start row -->
                                                <tr>

                                                    <th>Reg. No.</th>
                                                    <th>Expiry Date</th>
                                                    <th>Status</th>

                                                </tr>
                                            </thead>
                                            <!-- Rows are loaded in chunks via AJAX (server-side processing) -->
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="<?= base_url('/assets/libs/jquery/dist/jquery.min.js') ?>"></script>
    <script src="<?= base_url('/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('/assets/js/sidebarmenu.js') ?>"></script>
    <script src="<?= base_url('/assets/js/app.min.js') ?>"></script>
    <script src="<?= base_url('/assets/libs/apexcharts/dist/apexcharts.min.js') ?>"></script>
    <script src="<?= base_url('/assets/libs/simplebar/dist/simplebar.js') ?>"></script>
    <script src="<?= base_url('/assets/js/dashboard.js') ?>"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#resultsTable')) {
            var expiryTable = $('#resultsTable').DataTable({
                // Server-side processing: only the current page chunk is
                // fetched from the server, so the page loads fast even
                // with 10,000+ records.
                processing: true,
                serverSide: true,
                ajax: "<?= site_url('employee/expiry-data-api') ?>",
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100, 200],
                order: [[0, 'asc']],
                columns: [
                    { data: 'regNumber' },
                    { data: 'expiryDate', defaultContent: '-' },
                    { data: 'status', className: 'text-center' }
                ],
                // Keep data-id on each row so the Save/Skip buttons can
                // update the matching row immediately after saving.
                createdRow: function(row, data) {
                    $(row).attr('data-id', data.id);
                    $(row).css('font-size', '.9rem');
                },
                language: {
                    emptyTable: "No records found.",
                    zeroRecords: "No matching records found."
                }
            });

            // Global search (server-side): supports reg no, expiry date
            // and status labels like pending / complete(d) / skip(ped)
            $('#searchBox').on('keyup', function() {
                expiryTable.search(this.value).draw();
            });

            // Rows per page selector
            $('#rowsPerPage').on('change', function() {
                expiryTable.page.len(parseInt(this.value, 10) || 25).draw();
            });
        }
    });
    </script>

</body>

</html>