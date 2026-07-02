<!doctype html>
<html lang="en">
<head>
    <?= $this->include('admin/link'); ?>
    <link rel="stylesheet" href="<?= base_url('/assets/css/toast.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/common.css') ?>" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main wrapper -->
    <div class="body-wrapper">
        <!-- Header -->
        <?= $this->include('admin/header'); ?>

        <div class="body-wrapper-inner">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Policies Table -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex mb-3 align-items-center justify-content-between">
                                    <h4 class="card-title mb-0">All Policies</h4>
                                    <button id="exportBtn" class="btn btn-secondary" style="display:none;">Export to CSV</button>
                                </div>

                                <!-- Alert box -->
                                <div id="alertBox"></div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="resultsTable">
                                        <thead class="table-info">
                                        <tr>
                                            <th>Holder Name</th>
                                            <th>Vehicle No.</th>
                                            <th>Insurance Type</th>
                                            <th>Mobile</th>
                                            <th>Telecaller</th>
                                            <th>Issue Date</th>
                                            <th>Expiry Date</th>
                                            <th>Download</th>
                                        </tr>
                                        </thead>
                                        <tbody id="tableBody">
                                        <tr>
                                            <td colspan="8" class="loading">Loading policies...</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- End Policies Table -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/script'); ?>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

<script>
let dataTable;
const alertBox = document.getElementById('alertBox');
const exportBtn = document.getElementById('exportBtn');

function setAlert(message, type) {
    const alertClass = type === 'success' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';
    alertBox.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
}

async function fetchPolicies() {
    const downloadUrlBase = '<?= site_url('admin/download-policy') ?>';

    try {
        const response = await fetch('<?= site_url('admin/search-policy-api') ?>');
        const data = await response.json();

        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        if (!data.success || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No policies found</td></tr>';
            exportBtn.style.display = 'none';
            setAlert(data.message || 'No records found', 'error');
            return;
        }

        data.data.forEach(policy => {
            const row = `
                <tr>
                    <td><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.holder_name}</a></td>
                    <td>${policy.vehicle_number}</td>
                    <td>${policy.insurance_type}</td>
                    <td>${policy.mobileNo}</td>
                    <td>${policy.telecaller_name}</td>
                    <td>${policy.issue_date}</td>
                    <td>${policy.expiry_date}</td>
                    <td><a href="${downloadUrlBase}/${policy.policy_id}" download><i class="ti ti-download fs-6"></i></a></td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

        // Show export button
        exportBtn.style.display = 'inline-block';
        exportBtn.onclick = () => {
            window.location.href = `<?= site_url('admin/export-expired') ?>`;
        };

        // Initialize DataTable
        if (dataTable) dataTable.destroy();
        dataTable = new DataTable('#resultsTable', {
            paging: true,
            pageLength: 25,
            searching: true,
            ordering: true,
            language: {
                search: 'Filter:',
                paginate: { previous: 'Previous', next: 'Next' }
            }
        });

        setAlert('Policies loaded successfully', 'success');
    } catch (error) {
        setAlert('Error: ' + error.message, 'error');
        exportBtn.style.display = 'none';
    }
}

// Load on page ready
document.addEventListener('DOMContentLoaded', fetchPolicies);
</script>
</body>
</html>
