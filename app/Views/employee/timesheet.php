<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Timesheet</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/toast.css') ?>" />
    <style>
        .timesheet-row-danger td,
        .timesheet-row-danger th {
            background-color: #f8d7da !important;
            color: #842029;
        }

        .timesheet-row-success td,
        .timesheet-row-success th {
            background-color: #d1e7dd !important;
            color: #0f5132;
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
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <?php include 'sidebar.php'; ?>

        <div class="body-wrapper">
            <?php include 'header.php'; ?>

            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <?php if (session()->getFlashdata('success')): ?>
                                        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                                    <?php endif; ?>
                                    <?php if (session()->getFlashdata('error')): ?>
                                        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                                        <div>
                                            <h4 class="card-title mb-1">Timesheet</h4>
                                            <p class="text-muted mb-0">
                                                Showing attendance for <?= esc($employee['name'] ?? 'Employee') ?>
                                            </p>
                                        </div>

                                        <form method="post" action="<?= base_url('/employee/timesheet') ?>" class="d-flex gap-2 align-items-center flex-wrap">
                                            <?= csrf_field() ?>
                                            <select name="month" class="form-select">
                                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                                    <option value="<?= $m ?>" <?= (int) $selectedMonth === $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                                <?php endfor; ?>
                                            </select>
                                            <select name="year" class="form-select">
                                                <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                                                    <option value="<?= $y ?>" <?= (int) $selectedYear === $y ? 'selected' : '' ?>><?= $y ?></option>
                                                <?php endfor; ?>
                                            </select>
                                            <button type="submit" class="btn btn-primary">View</button>
                                        </form>
                                    </div>

                                    <?php if (!empty($records)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Check In Time</th>
                                                        <th>Check Out Time</th>
                                                        <th>Duration</th>
                                                        <th>Status</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($records as $record): ?>
                                                        <tr class="<?= esc($record['row_class'] ?? '') ?>">
                                                            <td><?= esc($record['attendance_date'] ?? '-') ?></td>
                                                            <td><?= esc($record['check_in_time'] ?? '-') ?></td>
                                                            <td><?= esc($record['check_out_time'] ?? '-') ?></td>
                                                            <td><?= esc($record['duration_label'] ?? '-') ?></td>
                                                            <td><?= esc($record['status'] ?? '-') ?></td>
                                                            <td><?= esc($record['remarks'] ?? '-') ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-0">No attendance records found for this month.</div>
                                    <?php endif; ?>
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
</body>

</html>