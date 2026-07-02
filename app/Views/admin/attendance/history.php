<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance History - Admin Panel</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        html, body { margin: 0; padding: 0; }
        #main-wrapper, .page-wrapper { padding-top: 0 !important; }
        .body-wrapper, .body-wrapper-inner { margin-top: 0 !important; padding-top: 0 !important; }
        .app-header, .navbar { top: 0 !important; margin-top: 0 !important; padding-top: 0 !important; }
        #main-wrapper[data-layout="vertical"][data-sidebar-position="fixed"] .left-sidebar { top: 0 !important; }

        .employee-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .employee-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 28px;
        }

        .employee-header .job-title {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
            margin-right: 8px;
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

        .table-responsive {
            border-radius: 4px;
            overflow: hidden;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .edit-btn, .delete-btn {
            padding: 4px 8px;
            font-size: 12px;
            margin: 0 2px;
        }

        .stats-row {
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }

        .stat-card h6 {
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <?= $this->include('admin/sidebar'); ?>
       
        <div class="body-wrapper">
         <?= $this->include('admin/header'); ?>
        
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <!-- Employee Header -->
                    <div class="employee-header mb-4">
                        <h2><?= htmlspecialchars($employee['name']) ?></h2>
                        <div class="job-title">
                            <i class="ti ti-briefcase me-2"></i><?= htmlspecialchars($employee['jobTitle'] ?? 'N/A') ?>
                        </div>
                    </div>

                    <div id="alertBox"></div>

                    <div class="row">
                        <div class="col-12">
                            <!-- Statistics Cards -->
                            <div class="stats-row">
                                <div class="row" id="statsContainer">
                                    <!-- Stats will be loaded here -->
                                </div>
                            </div>

                            <!-- Attendance History Table -->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">
                                        <i class="ti ti-history me-2"></i>Attendance History
                                    </h4>

                                    <div class="table-responsive">
                                        <table id="historyTable" class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Day</th>
                                                    <th>Check In</th>
                                                    <th>Check Out</th>
                                                    <th>Duration</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($records)): ?>
                                                    <?php foreach ($records as $record): ?>
                                                        <tr>
                                                            <td><strong><?= $record['attendance_date'] ?></strong></td>
                                                            <td><?= date('l', strtotime($record['attendance_date'])) ?></td>
                                                            <td><?= $record['check_in_time'] ?? '-' ?></td>
                                                            <td><?= $record['check_out_time'] ?? '-' ?></td>
                                                            <td>
                                                                <?php 
                                                                    if ($record['check_in_time'] && $record['check_out_time']) {
                                                                        $start = strtotime($record['check_in_time']);
                                                                        $end = strtotime($record['check_out_time']);
                                                                        $diff = ($end - $start) / 3600;
                                                                        echo round($diff, 2) . ' hrs';
                                                                    } else {
                                                                        echo '-';
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <span class="status-badge status-<?= str_replace(' ', '-', strtolower($record['status'])) ?>">
                                                                    <?= htmlspecialchars($record['status']) ?>
                                                                </span>
                                                            </td>
                                                            <td><?= htmlspecialchars($record['remarks'] ?? '-') ?></td>
                                                            <td>
                                                                <button class="btn btn-sm btn-warning edit-btn" 
                                                                        data-id="<?= $record['id'] ?>" 
                                                                        title="Edit">
                                                                    <i class="ti ti-edit"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-danger delete-btn" 
                                                                        data-id="<?= $record['id'] ?>" 
                                                                        title="Delete">
                                                                    <i class="ti ti-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted">No attendance records found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <?php if ($pager): ?>
                                        <nav aria-label="Page navigation">
                                            <?= $pager->links() ?>
                                        </nav>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Back Button -->
                            <div class="mt-4">
                                <a href="<?= site_url('/admin/attendance/report') ?>" class="btn btn-secondary">
                                    <i class="ti ti-arrow-left me-2"></i>Back to Report
                                </a>
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
        const alertBox = document.getElementById('alertBox');

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

        // Calculate statistics
        async function loadStatistics() {
            try {
                const response = await fetch('<?= site_url('admin/attendance/today-stats') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    displayStatistics(data);
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        }

        function displayStatistics(data) {
            const statsContainer = document.getElementById('statsContainer');
            const stats = [
                { label: 'Total Employees', value: data.total_employees, color: '#667eea' },
                { label: 'Present Today', value: data.present_today, color: '#0f5132' },
                { label: 'Absent Today', value: data.absent_today, color: '#842029' },
                { label: 'On Leave Today', value: data.leave_today, color: '#0c5460' }
            ];

            stats.forEach(stat => {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-3';
                col.innerHTML = `
                    <div class="stat-card" style="border-left-color: ${stat.color}">
                        <h6>${stat.label}</h6>
                        <div class="value" style="color: ${stat.color}">${stat.value}</div>
                    </div>
                `;
                statsContainer.appendChild(col);
            });
        }

        // Initialize DataTable
        const historyTable = new DataTable('#historyTable', {
            paging: false,
            searching: true,
            ordering: true,
            language: {
                search: 'Filter:'
            }
        });

        // Delete record
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!confirm('Are you sure you want to delete this record?')) return;

                const id = this.dataset.id;

                try {
                    const response = await fetch('<?= site_url('admin/attendance/delete') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            id: id
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        setAlert(data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        setAlert(data.message || 'Error deleting record', 'error');
                    }
                } catch (error) {
                    setAlert('Error: ' + error.message, 'error');
                }
            });
        });

        // Load statistics on page load
        loadStatistics();
    </script>
</body>

</html>
