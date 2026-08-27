<!doctype html>
<html lang="en">

<head>
    <?= $this->include('admin/link'); ?>
    <link rel="stylesheet" href="<?= base_url('/assets/css/toast.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/common.css') ?>" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        /* Truncate overflowing text with ellipsis (page-scoped) */
        .truncate {
            display: inline-block;
            max-width: 160px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            font-size: 12px;
        }

        /* Page-scoped no-pad table rule */
        table.no-pad > :not(caption) > * > * {
            /* padding: 16px 16px; */
            padding: 0;
            color: var(--bs-table-color-state, var(--bs-table-color-type, var(--bs-table-color)));
            background-color: var(--bs-table-bg);
            border-bottom-width: var(--bs-border-width);
            box-shadow: inset 0 0 0 9999px var(--bs-table-bg-state, var(--bs-table-bg-type, var(--bs-table-accent-bg)));
        }

        table.no-pad th,
        table.no-pad td {
            font-size: 12px;
            line-height: 1.2;
        }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

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
                                        <button id="exportBtn" class="btn btn-secondary" style="display:none;">Export to
                                            CSV</button>
                                    </div>

                                    <!-- Alert box -->
                                    <div id="alertBox"></div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle no-pad" id="resultsTable" style="margin-top:34px !important;">
                                            <thead class="table-info">
                                                <tr>
                                                    <th><div class="truncate">Holder Name</div></th>
                                                    <th><div class="truncate">Vehicle No.</div></th>
                                                    <th><div class="truncate">Insurance Type</div></th>
                                                    <th><div class="truncate">Company</div></th>
                                                    <th><div class="truncate">Mobile</div></th>
                                                    <th><div class="truncate">Telecaller</div></th>
                                                    <th><div class="truncate">Premium</div></th>
                                                    <th><div class="truncate">Cashback</div></th>
                                                    <th><div class="truncate">Type</div></th>
                                                    <th><div class="truncate">Issue Date</div></th>
                                                    <th><div class="truncate">Expiry Date</div></th>
                                                    <th><div class="truncate">Download</div></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableBody">
                                                <tr>
                                                    <td colspan="12" class="loading">Loading policies...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- End Policies Table -->
                            <div class="modal fade" id="policyPreviewModal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Policy Preview</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe id="policyPreviewFrame" src="" width="100%" height="600px"
                                                style="border:none;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
            const response = await fetch('<?= site_url('admin/search-policy-api') ?>?per_page=10000');
            const data = await response.json();

            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            if (!data.success || data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="11" class="text-center text-muted">No policies found</td></tr>';
                exportBtn.style.display = 'none';
                setAlert(data.message || 'No records found', 'error');
                return;
            }

            data.data.forEach(policy => {
                const row = `
                <tr>
                    <td><div class="truncate"><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.holder_name}</a></div></td>
                    <td><div class="truncate"><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.vehicle_number}</a></div></td>
                    <td><div class="truncate"><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.insurance_type}</a></div></td>
                    <td><div class="truncate"><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.company_name}</a></div></td>
                    <td><div class="truncate"><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.mobileNo}</a></div></td>
                    <td><div class="truncate"><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.telecaller_name}</a></div></td>
                    <td><div class="truncate"><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.premium}</a></div></td>
                    <td><div class="truncate"><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.cashback}</a></div></td>
                    <td><div class="truncate"><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.policyType}</a></div></td>
                    <td><div class="truncate"><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.issue_date}</a></div></td>
                    <td><div class="truncate"><a href="<?= site_url('admin/edit-policy-view') ?>/${policy.policy_id}">${policy.expiry_date}</a></div></td>
                    <td><div class="truncate"><a href="${downloadUrlBase}/${policy.policy_id}" download><i class="ti ti-download fs-6"></i></a>
                        &nbsp;
                        <button type="button" class="btn btn-sm  me-2 previewBtn" 
                                data-id="${policy.policy_id}" data-bs-toggle="modal" data-bs-target="#policyPreviewModal">
                            <i class="ti ti-eye"></i>
                        </button></div></td>
                </tr>
            `;
                tbody.innerHTML += row;
            });

            // Show export button
            exportBtn.style.display = 'inline-block';
            exportBtn.onclick = () => {
                window.location.href = `<?= site_url('admin/export-all-policy') ?>`;
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
                    paginate: {
                        previous: 'Previous',
                        next: 'Next'
                    }
                }
            });

            //setAlert('Policies loaded successfully', 'success');
        } catch (error) {
            setAlert('Error: ' + error.message, 'error');
            exportBtn.style.display = 'none';
        }
    }

    // Load on page ready
    document.addEventListener('DOMContentLoaded', fetchPolicies);

    document.addEventListener('click', function(e) {
        if (e.target.closest('.previewBtn')) {
            const policyId = e.target.closest('.previewBtn').getAttribute('data-id');
            const iframe = document.getElementById('policyPreviewFrame');
            iframe.src = `<?= site_url('admin/preview-policy') ?>/${policyId}`;
        }
    });
    </script>
</body>

</html>