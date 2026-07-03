<!doctype html>
<html lang="en">

<head>
    <?= $this->include('admin/link'); ?>
    <link rel="stylesheet" href="<?= base_url('/assets/css/toast.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/common.css') ?>" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <style>
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

    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid #0069d9;
        border-radius: 8px;
        padding: 6px 12px;
        margin-left: 0.5em;
        box-shadow: 0 2px 4px rgba(0, 105, 217, 0.1);
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 6px;
    }

    /* Remove forced border */
    .border {
        border: none !important;
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
            <?php include 'header.php'; ?>

            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex mb-3 align-items-center">
                                        <h4 class="card-title mb-0">All Data</h4>
                                        <div class="ms-auto">
                                            <button type="button" onclick="downloadExcel()"
                                                class="btn btn-outline-success btn-sm shadow-sm">
                                                <i class="ti ti-download me-2"></i> Download Excel
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive border rounded-2">
                                        <table id="resultsTable" class="table table-striped table-hover align-middle">
                                            <thead class="table-info">
                                                <tr>
                                                    <th>Record ID</th>
                                                    <th>Reg Date</th>
                                                    <th>Reg Number</th>
                                                    <th>Owner Name</th>
                                                    <th>Mobile</th>
                                                    <th>Telecaller</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($rows)): ?>
                                                <?php foreach ($rows as $row): ?>
                                                <tr>
                                                    <td><?= esc($row['recordId']) ?></td>
                                                    <td><?= esc($row['regDate']) ?></td>
                                                    <td><?= esc($row['regNumber']) ?></td>
                                                    <td><?= esc($row['ownerName']) ?></td>
                                                    <td><?= esc($row['mobile']) ?></td>
                                                    <td><?= esc($row['telecaller']) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php else: ?>
                                                <tr>
                                                    <td colspan="6">No records found.</td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                            <!-- End Info Table -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $this->include('admin/script'); ?>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
  if (!$.fn.DataTable.isDataTable('#resultsTable')) {
    $('#resultsTable').DataTable({
      pageLength: 25,
      lengthMenu: [10, 25, 50, 100, 200],
      language: {
        search: "Search:",
        paginate: { previous: "Prev", next: "Next" }
      }
    });
  }
});

    function downloadExcel() {
        window.location.href = "<?= site_url('admin/export-all-data') ?>";
    }
    </script>
</body>

</html>