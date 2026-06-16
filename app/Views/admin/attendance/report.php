<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance Report - Admin Panel</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        html, body { margin: 0; padding: 0; }
        #main-wrapper, .page-wrapper { padding-top: 0 !important; }
        .body-wrapper, .body-wrapper-inner { margin-top: 0 !important; padding-top: 0 !important; }
        .app-header, .navbar { top: 0 !important; margin-top: 0 !important; padding-top: 0 !important; }
        #main-wrapper[data-layout="vertical"][data-sidebar-position="fixed"] .left-sidebar { top: 0 !important; }

        .status-badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
        }

        .status-present {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-absent {
            background-color: #f8d7da;
            color: #842029;
        }

        .status-leave {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-half-day {
            background-color: #fff3cd;
            color: #664d03;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .filter-row {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <?php include '../sidebar.php'; ?>

        <div class="body-wrapper">
            <?php include '../header.php'; ?>

            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">
                                        <i class="ti ti-report me-2"></i>Attendance Report
                                    </h4>

                                    <div id="alertBox"></div>

                                    <!-- Filter Section -->
                                    <div class="filter-section">
                                        <form id="filterForm">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="startDate" class="form-label">Start Date <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" id="startDate" 
                                                               value="<?= date('Y-m-01') ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="endDate" class="form-label">End Date <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" id="endDate" 
                                                               value="<?= date('Y-m-d') ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label for="employeeFilter" class="form-label">Employee</label>
                                                        <select class="form-select" id="employeeFilter">
                                                            <option value="">All Employees</option>
                                                            <?php foreach ($employees as $emp): ?>
                                                                <option value="<?= $emp['employeeId'] ?>">
                                                                    <?= htmlspecialchars($emp['name']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label for="statusFilter" class="form-label">Status</label>
                                                        <select class="form-select" id="statusFilter">
                                                            <option value="">All Status</option>
                                                            <?php foreach ($statuses as $status): ?>
                                                                <option value="<?= $status ?>"><?= $status ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label class="form-label">&nbsp;</label>
                                                        <button type="submit" class="btn btn-primary w-100">
                                                            <i class="ti ti-search"></i> Search
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-success btn-sm" id="exportBtn" style="display:none;">
                                                    <i class="ti ti-download"></i> Export to CSV
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Report Table -->
                                    <div class="table-responsive">
                                        <table id="reportTable" class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Employee Name</th>
                                                    <th>Job Title</th>
                                                    <th>Date</th>
                                                    <th>Check In</th>
                                                    <th>Check Out</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reportBody">
                                            </tbody>
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
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    <script>
        let dataTable;
        const alertBox = document.getElementById('alertBox');
        const filterForm = document.getElementById('filterForm');
        const exportBtn = document.getElementById('exportBtn');

        function setAlert(message, type) {
            const alertClass = type === 'success' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';
            alertBox.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="ti ${type === 'success' ? 'ti-circle-check' : 'ti-alert-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }

        function getStatusBadge(status) {
            const statusMap = {
                'Present': 'status-present',
                'Absent': 'status-absent',
                'Leave': 'status-leave',
                'Half Day': 'status-half-day'
            };
            const className = statusMap[status] || '';
            return `<span class="status-badge ${className}">${status}</span>`;
        }

        async function loadReport() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const employeeId = document.getElementById('employeeFilter').value;
            const status = document.getElementById('statusFilter').value;

            if (!startDate || !endDate) {
                setAlert('Please select date range', 'error');
                return;
            }

            try {
                const response = await fetch('<?= site_url('admin/attendance/get-report') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        start_date: startDate,
                        end_date: endDate,
                        employee_id: employeeId,
                        status: status
                    })
                });

                const data = await response.json();

                if (data.success) {
                    displayReport(data.data);
                    exportBtn.style.display = 'inline-block';
                    
                    // Update export button
                    exportBtn.onclick = () => {
                        window.location.href = `<?= site_url('admin/attendance/export') ?>?start_date=${startDate}&end_date=${endDate}&employee_id=${employeeId}`;
                    };
                } else {
                    setAlert(data.message || 'Error loading report', 'error');
                    exportBtn.style.display = 'none';
                }
            } catch (error) {
                setAlert('Error: ' + error.message, 'error');
                exportBtn.style.display = 'none';
            }
        }

        function displayReport(records) {
            const tbody = document.getElementById('reportBody');
            tbody.innerHTML = '';

            if (records.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No records found</td></tr>';
                if (dataTable) dataTable.destroy();
                return;
            }

            records.forEach(record => {
                const row = `
                    <tr>
                        <td><strong>${htmlEscape(record.employee_name)}</strong></td>
                        <td>${htmlEscape(record.jobTitle || 'N/A')}</td>
                        <td>${record.attendance_date}</td>
                        <td>${record.check_in_time || '-'}</td>
                        <td>${record.check_out_time || '-'}</td>
                        <td>${getStatusBadge(record.status)}</td>
                        <td>${htmlEscape(record.remarks || '-')}</td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn" data-id="${record.id}" title="Edit">
                                <i class="ti ti-edit"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            if (dataTable) {
                dataTable.destroy();
            }

            dataTable = new DataTable('#reportTable', {
                paging: true,
                pageLength: 15,
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
        }

        function htmlEscape(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Form submission
        filterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            loadReport();
        });

        // Load initial report
        loadReport();
    </script>
</body>

</html>
