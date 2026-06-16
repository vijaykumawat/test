<!DOCTYPE html>
<html>
<head>
    <title>Expired Policies - Current Month</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; margin: 0; background-color: #f5f7fa; color: #333; }
        .wrapper { display: flex; flex-direction: column; min-height: 100vh; width: 100%; }
        .topbar { background: #ffffff; border-bottom: 1px solid #ddd; padding: 14px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .topbar-menu { display: flex; gap: 30px; align-items: center; }
        .topbar-menu a { text-decoration: none; color: #333; font-weight: 500; font-size: 14px; transition: color 0.3s; }
        .topbar-menu a:hover { color: #0069d9; }
        .topbar-btn { background: #0069d9; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer; }
        .container { display: flex; flex: 1; width: 100%; max-width: none; padding: 0; margin: 0; }
        .sidebar { width: 240px; background: #e8e8e8; color: #333; min-height: 100vh; padding-top: 20px; box-shadow: 2px 0 6px rgba(0,0,0,0.1); }
        .sidebar h2 { text-align: center; font-size: 18px; margin-bottom: 30px; color: #333; }
        .sidebar a { display: block; padding: 14px 22px; color: #333; text-decoration: none; font-weight: 500; transition: background 0.3s, color 0.3s; }
        .sidebar a:hover { background: #d0d0d0; color: #0069d9; }
        .sidebar a.active { background: #0069d9; color: #fff; }
        .main { flex: 1; padding: 30px; }
        h2 { font-size: 22px; margin-bottom: 20px; font-weight: 600; }
        table { margin: 20px 0; border-collapse: collapse; width: 100%; background-color: #ffffff; border: 1px solid #ddd; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; font-size: 14px; }
        th { background-color: #f0f2f5; font-weight: 600; }
        tr:nth-child(even) td { background-color: #fafafa; }
        .download-icon { text-decoration: none; color: #0069d9; font-size: 16px; }
        .search-wrapper { position: relative; display: flex; align-items: center; gap: 10px; }
        .search-wrapper input { padding: 12px 40px 12px 16px; font-size: 14px; border: 2px solid #0069d9; border-radius: 8px; background-color: #ffffff; box-shadow: 0 2px 4px rgba(0, 105, 217, 0.1); width: 400px; }
        .search-wrapper input:focus { outline: none; border-color: #0053aa; box-shadow: 0 4px 12px rgba(0, 105, 217, 0.25); }
        .search-wrapper input::placeholder { color: #999; }
        .search-icon { position: absolute; right: 12px; color: #0069d9; font-size: 18px; pointer-events: none; }
        .pagination { margin-top: 20px; }
        .pagination a, .pagination span { padding: 8px 12px; margin: 0 2px; border: 1px solid #ddd; text-decoration: none; color: #0069d9; cursor: pointer; border-radius: 4px; }
        .pagination a:hover { background: #0069d9; color: #fff; }
        .pagination .active { background: #0069d9; color: #fff; border-color: #0069d9; }
        .loading { color: #666; font-style: italic; }
        .form-select { width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-excel { background-color: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .btn-excel:hover { background-color: #218838; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="topbar">
        <div class="topbar-menu">
            <a href="<?= site_url('admin') ?>">🏠 Home</a>
            <a href="<?= site_url('admin/upload') ?>">📊 Dashboard</a>
            <a href="<?= site_url('admin/search') ?>">🔍 Search</a>
        </div>
        <button class="topbar-btn" onclick="window.location.href='<?= site_url('admin/upload') ?>';">➕ Add Policy</button>
    </div>
    <div class="container">
       <?php include 'sidebar.php'; ?>
        <div class="main">
            <h2>Expired Policies - <?= htmlspecialchars($month) ?></h2>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="display: flex; gap: 10px;">
                    <button class="btn-excel" onclick="downloadExcel()">📥 Download Excel</button>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <div class="search-wrapper">
                        <input type="text" id="searchBox" onkeyup="loadPolicies()" placeholder="Search expired policies...">
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
            <div class="table-responsive">
                <table id="expiredResultsTable" class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Policy No.</th>
                            <th>Holder Name</th>
                            <th>Vehicle No.</th>
                            <th>Insurance Type</th>
                            <th>Issue Date</th>
                            <th>Expiry Date</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="8" class="loading">Loading policies...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination" id="pagination"></div>
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
        
        fetch('<?= site_url('admin/expired-current-api') ?>?q=' + encodeURIComponent(search) + '&page=' + currentPage + '&per_page=' + perPage)
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
                                <td>${start + idx}</td>
                                <td>${policy.policy_number}</td>
                                <td>${policy.holder_name}</td>
                                <td>${policy.vehicle_number}</td>
                                <td>${policy.insurance_type}</td>
                                <td>${policy.issue_date}</td>
                                <td>${policy.expiry_date}</td>
                                <td><a class="download-icon" href="${downloadUrlBase}/${policy.policy_id}" download>⬇️</a></td>
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
                document.getElementById('tableBody').innerHTML = '<tr><td colspan="8" class="loading">Error loading policies.</td></tr>';
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
</body>
</html>
