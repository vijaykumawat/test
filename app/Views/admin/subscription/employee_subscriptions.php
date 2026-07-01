<!doctype html>
<html lang="en">

<head>
<?= $this->include('admin/link'); ?>
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
        <?= $this->include('admin/sidebar'); ?>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <?= $this->include('admin/header'); ?>
            <!--  Header End -->
            <div class="body-wrapper-inner">
                <div class="container-fluid">

                    <div class="page-titles mb-7 mb-md-5">
                        <div class="row">
                            <div class="col-lg-8 col-md-6 col-12 align-self-center">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb align-items-center">
                                        <li class="breadcrumb-item">
                                            <a class="text-muted text-decoration-none" href="<?= base_url('/admin') ?>">
                                                <i class="ti ti-home fs-5"></i>
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item" aria-current="page">Employees</li>
                                    </ol>
                                </nav>
                                <h2 class="mb-0 fw-bolder fs-8">Employee Management</h2>
                            </div>
                            <div class="col-lg-4 col-md-6 d-none d-md-flex align-items-center justify-content-end">

                                <a href="<?= base_url('/admin/employees/new') ?>"
                                    class="btn btn-primary d-flex align-items-center ms-2">
                                    <i class="ti ti-plus me-1"></i>
                                    Add New
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- start Default Size Light Table -->
                    <?php if (session()->has('success')): ?>
                    <div class="alert bg-success-subtle text-success alert-dismissible fade show mb-3" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-circle-check me-2 fs-4"></i>
                            <?= session('success') ?>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (session()->has('error')): ?>
                    <div class="alert bg-danger-subtle text-danger alert-dismissible fade show mb-3" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-circle me-2 fs-4"></i>
                            <?= session('error') ?>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (session()->has('warning')): ?>
                    <div class="alert bg-warning-subtle text-warning alert-dismissible fade show mb-3" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-triangle me-2 fs-4"></i>
                            <?= session('warning') ?>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex mb-2 align-items-center">
                                <div>
                                    <h5>Employee List</h5>
                                </div>
                            </div>
                            <div class="table-responsive border rounded-2">
                                <table class="table text-nowrap customize-table mb-0 align-middle">
                                    <thead class="text-dark fs-4">
                                        <tr>
                                            <th>
                                                <h6 class="fs-4 fw-semibold mb-0">Employee</h6>
                                            </th>
                                            <th>
                                                <h6 class="fs-4 fw-semibold mb-0">Emp</h6>
                                            </th>
                                            <th>
                                                <h6 class="fs-4 fw-semibold mb-0">Subscription</h6>
                                            </th>
                                            <th>
                                                <h6 class="fs-4 fw-semibold mb-0">End Date</h6>
                                            </th>
                                            <th>
                                                <h6 class="fs-4 fw-semibold mb-0">Action</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($employees)): ?>
                                        <?php foreach ($employees as $emp): ?>
                                        <tr>
                                            <!-- Employee Info -->
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php
                                                                    // Check if employee has a profile photo
                                                                    $photo = $emp['profilePhoto'] ?? null;

                                                                    if (empty($photo)) {
                                                                        // Fallback based on session gender
                                                                        $gender = strtolower($emp['gender']);;
                                                                        $photo = ($gender === 'male') ? 'user-1.jpg' : 'user-2.jpg';
                                                                    }
                                                                ?>
                                                    <img src="<?= base_url('uploads/profile/' . $photo) ?>"
                                                        class="rounded-circle" width="40" height="40" alt="profile-img">

                                                    <a href="<?= base_url('/admin/employee/' . $emp['employeeId']) ?>">
                                                        <div class="ms-3">
                                                            <h6 class="fs-4 fw-semibold mb-0"><?= esc($emp['name']); ?>
                                                            </h6>
                                                            <span
                                                                class="fw-normal"><?= esc($emp['jobTitle'] ?? 'Position'); ?></span>
                                                        </div>
                                                    </a>
                                                </div>
                                            </td>


                                            <!-- Employee Active/Inactive -->
                                            <td>
                                                <?php
                                                $empBadgeClass = $emp['isActive'] == 1
                                                    ? 'bg-success-subtle text-success'
                                                    : 'bg-danger-subtle text-danger';
                                                ?>
                                                <span class="badge <?= $empBadgeClass; ?>">
                                                    <?= $emp['isActive'] == 1 ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>

                                            <!-- Subscription Status -->
                                            <td>
  <?php
    // Calculate remaining days
    $endDate = strtotime($emp['endDate']);
    $today   = strtotime(date('Y-m-d'));
    $daysRemaining = ceil(($endDate - $today) / (60 * 60 * 24));

    // Decide text and color
    if ($daysRemaining < 0) {
        $subLabel     = "Expired Subscription";
        $subBadgeClass = "bg-danger-subtle text-danger";
    } elseif ($daysRemaining >= 10) {
        $subLabel     = $daysRemaining . " days remaining";
        $subBadgeClass = "bg-success-subtle text-success";
    } elseif ($daysRemaining >= 0 && $daysRemaining <= 9) {
        $subLabel     = $daysRemaining . " days remaining";
        $subBadgeClass = "bg-warning-subtle text-warning";
    } else { // 1 to 4 days
        $subLabel     = $daysRemaining . " days remaining";
        $subBadgeClass = "bg-danger-subtle text-danger";
    }
  ?>
  <span class="badge <?= $subBadgeClass; ?>">
    <?= $subLabel; ?>
  </span>
</td>

                                           <!-- End Date -->
<td>
  <?php
    $endDateClass = '';
    $endDateText  = esc($emp['endDate'] ?? '-');

    if (!empty($emp['endDate'])) {
        $endDate   = strtotime($emp['endDate']);
        $today     = strtotime(date('Y-m-d'));
        $daysRemaining = ceil(($endDate - $today) / (60 * 60 * 24));

        if ($daysRemaining < 0) {
            $endDateClass = 'bg-danger-subtle text-danger'; // expired
        } elseif ($daysRemaining >= 10) {
            $endDateClass = 'bg-success-subtle text-success'; // safe
        } elseif ($daysRemaining >= 0 && $daysRemaining <= 9) {
            $endDateClass = 'bg-warning-subtle text-warning'; // warning
        } else { // 1 to 4 days
            $endDateClass = 'bg-danger-subtle text-danger'; // urgent
        }
    }
  ?>
  <p class="mb-0 fw-normal fs-4 <?= $endDateClass; ?>">
    <?= $endDateText; ?>
  </p>
</td>
                                            <!-- Action -->
                                           <td>
                                                <?php
                                                    // Calculate remaining days
                                                    $endDate = strtotime($emp['endDate']);
                                                    $today   = strtotime(date('Y-m-d'));
                                                    $daysRemaining = ceil(($endDate - $today) / (60 * 60 * 24));

                                                    // Decide button class and disabled state
                                                    if ($daysRemaining <= 10 ) {
                                                        // Enable Renew button if 1–10 days remaining
                                                        $btnClass = 'btn-primary btn-sm';
                                                        $disabled = '';
                                                    } else {
                                                        // Disable button if more than 10 days left OR expired
                                                        $btnClass = 'bg-primary-subtle text-primary btn-sm';
                                                        $disabled = 'disabled';
                                                    }
                                                ?>
                                                <button class="btn <?= $btnClass; ?> purchaseSubscriptionBtn"
                                                        data-employee-id="<?= $emp['employeeId']; ?>" <?= $disabled; ?>>
                                                    Renew
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="5">No employees found.</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <!-- end Default Size Light Table -->
                    <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="<?= site_url('admin/renew-subscription') ?>" method="post"
                                    enctype="multipart/form-data" id="subscriptionForm">
                                    <div class="modal-header text-danger">
                                        <h5 class="modal-title bg-danger-subtle">Purchase Subscription</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>You must purchase a subscription to access employee management features.</p>
                                        <div class="mb-3">
                                            <label for="paymentScreenshot" class="form-label">Upload Payment
                                                Screenshot</label>
                                            <input type="file" class="form-control" id="paymentScreenshot"
                                                name="paymentScreenshot" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="employeeId" id="employeeIdField">
                                        <button type="submit" id="finalSubmitBtn" class="btn btn-danger" disabled>
                                             <span class="spinner-border spinner-border-sm d-none" id="submitSpinner"></span>
                                            <span id="submitText">Submit</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    </div>

    <?= $this->include('admin/script'); ?>
    <script>
    document.querySelectorAll('.purchaseSubscriptionBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Get employeeId from button attribute
            const empId = this.getAttribute('data-employee-id');
            document.getElementById('employeeIdField').value = empId;

            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('subscriptionModal'));
            modal.show();
        });
    });
    const paymentScreenshot = document.getElementById('paymentScreenshot');
    const finalSubmitBtn = document.getElementById('finalSubmitBtn');
    const submitSpinner = document.getElementById('submitSpinner');
    const submitText = document.getElementById('submitText');
    const subscriptionForm = document.getElementById('subscriptionForm');

    // Enable Submit only when a file is selected
    paymentScreenshot.addEventListener('change', function() {
        if (this.files.length > 0) {
            finalSubmitBtn.removeAttribute('disabled');
        } else {
            finalSubmitBtn.setAttribute('disabled', true);
        }
    });

        // Show processing on submit
    subscriptionForm.addEventListener('submit', function () {

        finalSubmitBtn.disabled = true;          // Prevent double click
        submitSpinner.classList.remove('d-none');
        submitText.innerText = 'Processing...';

    });
    </script>


</body>

</html>