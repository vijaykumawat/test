<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flexy Free Bootstrap Admin Template by WrapPixel</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/toast.css') ?>" />
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
                            <!--  start Info Table -->
                            <div class="card">
                                <div class="card-body">
                                        <?php if (session()->getFlashdata('success')): ?>
                                            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                                        <?php endif; ?>
                                        <?php if (session()->getFlashdata('error')): ?>
                                            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                                        <?php endif; ?>
                                        <?php if (session()->getFlashdata('warning')): ?>
                                            <div class="alert alert-warning"><?= esc(session()->getFlashdata('warning')) ?></div>
                                        <?php endif; ?>

                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                        <div></div>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <a href="javascript:void(0)"
                                                class="btn btn-primary d-flex align-items-center ms-2"
                                                data-bs-toggle="modal" data-bs-target="#uploadPolicyModal">
                                                <i class="ti ti-plus me-1"></i>
                                                Add Policy
                                            </a>
                                        </div>
                                    </div>
                                    <?php if (!empty($policies)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Policy No</th>
                                                    <th>Holder Name</th>
                                                    <th>Vehicle No</th>
                                                    <th>Company</th>
                                                    <th>Mobile</th>
                                                    <th>Premium</th>
                                                    <th>Cashback</th>
                                                    <th>Insurance Type</th>
                                                    <th>Issue Date</th>
                                                    <th>Expiry Date</th>
                                                    <!--<th>File</th>-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($policies as $index => $policy): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><a
                                                            href="<?= site_url('employee/edit-policy-view') ?>/<?= $policy['policy_id']?>"><?= esc($policy['policy_number'] ?? '') ?></a>
                                                    </td>
                                                    <td><?= esc($policy['holder_name'] ?? '') ?></td>
                                                    <td><?= esc($policy['vehicle_number'] ?? '') ?></td>
                                                    <td><?= esc($policy['company_name'] ?? '') ?></td>
                                                    <td><?= esc($policy['mobileNo'] ?? '') ?></td>
                                                    <td><?= esc($policy['premium'] ?? '') ?></td>
                                                    <td><?= esc($policy['cashback'] ?? '') ?></td>
                                                    <td><?= esc($policy['insurance_type'] ?? '') ?></td>
                                                    <td><?= esc($policy['issue_date'] ?? '') ?></td>
                                                    <td><?= esc($policy['expiry_date'] ?? '') ?></td>
                                                    <!--
                                                    <td>
                                                        <a href="<?= site_url('employee/download-policy/'.$policy['policy_id']) ?>"
                                                            class="btn btn-sm btn-outline-primary" title="Download Policy">
                                                            <i class="ti ti-download"></i>
                                                        </a>
                                                        <?php if (!empty($policy['file_path'])): ?>
                                                            <a href="<?= base_url($policy['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>-->
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info mb-0">No policies uploaded yet.</div>
                                    <?php endif; ?>

                                </div>
                            </div>
                            <!--  end Info Table -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadPolicyModal" tabindex="-1" aria-labelledby="uploadPolicyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="uploadPolicyForm" action="<?= base_url('employee/upload-policy'); ?>" method="post"
                    enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadPolicyModalLabel">Upload Policy</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="recordId" value="<?= esc($recordId ?? '') ?>">
                        <div class="mb-3">
                            <label class="form-label">Select PDF Policy</label>
                            <input type="file" name="pdfs[]" class="form-control" accept="application/pdf" multiple
                                required>
                        </div>
                        <div class="small text-muted">The uploaded policy PDF will be extracted and saved
                            against this lead.</div>
                    </div>
                    <div class="modal-footer d-flex flex-column align-items-stretch w-100">
                        <div class="mt-1 mb-2 small" id="uploadPolicyFeedback"></div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Upload Policy</button>
                        </div>
                    </div>
                </form>
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
    <script>
    function searchTable() {
        let input = document.getElementById("searchBox").value.toLowerCase();
        let rows = document.querySelectorAll("#resultsTable tbody tr");
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(input) ? "" : "none";
        });
    }

    function filterTable() {
        let query = document.getElementById("searchBox").value.toLowerCase();
        let rows = document.querySelectorAll("#resultsTable tbody tr");

        rows.forEach(row => {
            // Collect searchable text from relevant columns
            let policyNo = row.cells[0]?.innerText.toLowerCase(); // Record ID / Policy No
            let holderName = row.cells[2]?.innerText.toLowerCase(); // Name
            let vehicle = row.cells[1]?.innerText.toLowerCase(); // Reg. No. or Vehicle

            if (
                policyNo.includes(query) ||
                holderName.includes(query) ||
                vehicle.includes(query)
            ) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
                    
    </script>

</body>

</html>