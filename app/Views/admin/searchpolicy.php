<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flexy Free Bootstrap Admin Template by WrapPixel</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/toast.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('/assets/css/common.css') ?>" />
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
            <?= $this->include('admin/header'); ?>

            <!--  Header End -->
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <!--  start Info Table -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex mb-1 align-items-center">
                                        <div>
                                            <h4 class="card-title mb-0">All Policies</h4>
                                        </div>

                                    </div>
                                    <br>
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                        <div></div>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <div class="search-wrapper">
                                                <input type="text" id="searchBox" onkeyup="loadPolicies()"
                                                    placeholder="Search by policy no, holder name, or vehicle...">
                                                <span class="search-icon">🔍</span>
                                            </div>
                                            <select id="rowsPerPage" class="form-select" onchange="loadPolicies()">
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
                                                    <!--<th>#</th>-->
                                                    <!-- <th>Policy No.</th> -->
                                                    <th>Holder Name</th>
                                                    <th>Vehicle No.</th>
                                                    <th>Insurance Type</th>
                                                    <th>Mobile</th>
                                                    <th>Telecaller</th>
                                                    <th>Issue Date</th>
                                                    <th>Expiry Date</th>
                                                    <th>Downlaod</th>
                                                </tr>
                                                <!-- end row -->
                                            </thead>
                                            <tbody id="tableBody">
                                                <tr>
                                                    <td colspan="8" class="loading">Loading policies...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="pagination" id="pagination"></div>
                                </div>
                            </div>
                            <!--  end Info Table -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
    let currentPage = 1;
    let allPolicies = [];

    function loadPolicies() {
        currentPage = 1;
        fetchPolicies();
    }

    function fetchPolicies() {
        const search = document.getElementById('searchBox').value;
        const perPage = document.getElementById('rowsPerPage').value;
        const downloadUrlBase = '<?= site_url('admin/download-policy') ?>';
        fetch('<?= site_url('admin/search-policy-api') ?>?q=' + encodeURIComponent(search) + '&page=' + currentPage +
                '&per_page=' + perPage)
            .then(response => response.json())
            .then(data => {
                allPolicies = data.data;
                const tbody = document.getElementById('tableBody');
                tbody.innerHTML = '';

                if (data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="loading">No expired policies found.</td></tr>';
                } else {
                    const start = (currentPage - 1) * parseInt(perPage) + 1;
                    data.data.forEach((policy, idx) => {
                        const row = `
                            <tr>
                                <!--<td>${start + idx}</td> -->
                                <!--<td>${policy.policy_number}</td> -->
                                <td><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}" class="me-3"> ${policy.holder_name}</a></td>
                                <td><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}" class="me-3">${policy.vehicle_number}</a></td>
                                <td>${policy.insurance_type}</td>
                                <td>${policy.mobileNo}</td>
                                <td>${policy.telecaller_name}</td>
                                <td>${policy.issue_date}</td>
                                <td>${policy.expiry_date}</td>
                                <td> 
                                  <div class="d-flex align-items-center">
                                    <!--            
                                    <a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}" class="me-3">
                                      <i class="ti ti-edit fs-6"></i>
                                    </a>-->
                                    <a class="" href="${downloadUrlBase}/${policy.policy_id}" download><i class="ti ti-download fs-6"></i></a>
                                    <!--
                                    <a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}" class="me-3">
                                      <i class="ti ti-download fs-6"></i>
                                    </a>

                                    
                                    <a href="<?= site_url('admin/delete-policy-api') ?>">
                                      <i class="ti ti-trash fs-6"></i>
                                    </a> -->
                                  </div>
                                </td>
                                <!--<td><a class="download-icon" href="${downloadUrlBase}/${policy.policy_id}" download>⬇️</a></td>-->
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                }

                // Render pagination
                renderPagination(data.total_pages, data.page);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('tableBody').innerHTML =
                    '<tr><td colspan="8" class="loading">Error loading policies.</td></tr>';
            });
    }

    function renderPagination(totalPages, currentPageNum) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const link = document.createElement('a');
            link.textContent = i;
            link.href = '#';
            link.className = i === currentPageNum ? 'active' : '';
            link.onclick = (e) => {
                e.preventDefault();
                currentPage = i;
                fetchPolicies();
                window.scrollTo(0, 0);
            };
            pagination.appendChild(link);
        }
    }

    function downloadExcel() {
        window.location.href = '<?= site_url('admin/export-expired') ?>';
    }

    // Load on page load
    document.addEventListener('DOMContentLoaded', loadPolicies);
    </script>

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
    </script>
    <script>
    const baseUrl = "<?= base_url() ?>";
    const searchCustomerUrl = "<?= site_url('admin/searchCustomerAjax') ?>";
</script>

<script src="<?= base_url('assets/js/customer-search.js') ?>"></script>

</body>

</html>