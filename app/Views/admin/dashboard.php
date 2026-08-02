<!doctype html>
<html lang="en">

<head>
    <?= $this->include('admin/link'); ?>
    <link rel="stylesheet" href="<?= base_url('/assets/css/common.css') ?>" />
    <style>
    .policy-card {
        border-radius: 10px;
        background: linear-gradient(135deg, #e8f0ff, #f8fbff);
        transition: all 0.3s ease;
    }

    .toggle-control span {
        font-size: 0.8rem;
        /* smaller font */
    }

    .toggle-control button i {
        font-size: 0.9rem;
    }

    .toggle-control button {
        text-decoration: none !important;
    }

    .toggle-control button:hover {
        text-decoration: none !important;
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
                <div class="container-fluid" style="padding-top:125px;">
                    <div class="page-titles mb-7 mb-md-5">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <h4 class="fw-bolder fs-8">
                                            <i class="ti ti-trophy text-warning"></i> Top Performers
                                        </h4>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($topPerformers as $index => $emp): ?>
                                            <li class="list-group-item d-flex align-items-center">
                                                <?php
                                                $medal = '';
                                                    if ($index === 0) $medal = '🥇';
                                                    elseif ($index === 1) $medal = '🥈';
                                                    elseif ($index === 2) $medal = '🥉';
                                                ?>
                                                <span class="me-2 fs-6"><?= $medal; ?></span>
                                                <?php
                                                    $photo = $emp['profilePhoto'] ?? null;
                                                    if (empty($photo)) {
                                                                // normalize gender to lowercase
                                                        $gender = strtolower($emp['gender']);
                                                        // fallback based on gender
                                                        $photo = ($gender === 'male') ? 'user-1.jpg' : 'user-2.jpg';
                                                    }
                                                    
                                                ?>
                                                <a class="sidebar-link justify-content-between"
                                                    href="<?= site_url('admin/search-policy') ?>">
                                                    <img src="<?= base_url('uploads/profile/' . $photo); ?>"
                                                        class="rounded-circle me-2" width="32" height="32" alt="avatar">
                                                </a>
                                                <div>
                                                    <a class="sidebar-link justify-content-between"
                                                        href="<?= site_url('admin/search-policy') ?>">
                                                        <strong><?= esc($emp['name']); ?></strong><br>
                                                        <small class="text-muted"><?= $emp['total']; ?> Policies This
                                                            Month</small>
                                                    </a>
                                                </div>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Policies Card -->
                            <div class="col-lg-3">
                                <div class="card policy-card shadow-sm border-0">
                                    <div class="card-body d-flex justify-content-between align-items-start">
                                        <div>
                                            <h4 class="fw-bolder fs-5">Policies</h4>
                                            <h2 id="policyCount" class="text-primary fs-7 mb-0"><?= $allCount; ?>
                                            </h2>
                                            <p id="policyLabel" class="text-muted mb-0">All Policies</p>
                                        </div>
                                        <div class="toggle-control text-end">
                                            <span id="policyOption"
                                                class="text-secondary small fw-semibold">All-time</span>
                                            <button id="nextOption" class="btn btn-sm btn-link text-primary p-0 ms-1">
                                                <i class="ti ti-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <a href="<?= site_url('admin/search-policy') ?>"
                                            class="btn btn-primary mt-2">Open</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Calling Data Card -->
                            <div class="col-lg-4">
                                <div class="card welcome-card2 overflow-hidden bg-success-subtle border-0">
                                    <div class="card-body d-flex justify-content-between align-items-start">
                                        <div>
                                            <h4 class="fw-bolder fs-5">Calling Data</h4>
                                            <h2 id="dataCount" class="text-primary fs-7 mb-0"><?= $unusedData; ?></h2>
                                            <p id="dataLabel" class="text-muted mb-0">Unused Data</p>
                                        </div>
                                        <div class="toggle-control text-end">
                                            <span id="dataOption" class="text-secondary small fw-semibold">Unused</span>
                                            <button id="nextDataOption"
                                                class="btn btn-sm btn-link text-primary p-0 ms-1">
                                                <i class="ti ti-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <a href="<?= base_url('/admin/all-data') ?>"
                                            class="btn btn-success mt-2">Open</a>
                                    </div>
                                </div>
                            </div>


                            <div class="col-lg-6 ">
                                <div class="card ">
                                    <div class="card-body">
                                        <div class="d-md-flex align-items-center">
                                            <div>
                                                <h4 class="card-title">Data Calling Date</h4>
                                                <p class="card-subtitle">
                                                    Ensure a three‑day gap before contacting customers.
                                                </p>
                                            </div>
                                            <div class="ms-auto mt-3 mt-md-0">
                                                <select class="form-select theme-select"
                                                    aria-label="Default select example" fdprocessedid="4e6gr">
                                                    <option value="1"><?php echo date('F Y'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="table-responsive mt-4">
                                            <table class="table mb-0 text-nowrap varient-table align-middle fs-3">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="px-0 text-muted">
                                                            Telecaller
                                                        </th>
                                                        
                                                        <th scope="col" class="px-0 text-muted">
                                                            Calling Date
                                                        </th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($unusedDataRecords as $rec): ?>
                                                        <tr>
                                                            <td class="px-0">
                                                                <div class="d-flex align-items-center">
                                                                    <?php 
                                                                    $photo = $rec['profilePhoto'] ?? null;
                                                                    $gender = strtolower(trim($rec['gender'] ?? ''));
                                                                    if (empty($photo)) {
                                                                        $photo = ($gender === 'male') ? 'user-1.jpg' : 'user-2.jpg';
                                                                    }
                                                                    ?>
                                                                    <img src="<?= base_url('uploads/profile/' . $photo) ?>"
                                                                        class="rounded-circle" width="35" alt="flexy">
                                                                    <div class="ms-3">
                                                                        <h6 class="mb-0 fw-bolder"><?= esc($rec['employeeName']) ?></h6>
                                                                        <span class="text-muted"><?= esc($rec['jobTitle'] ?? '') ?></span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                           <?php
                                                            $expiry = strtotime($rec['expiryDate']);
                                                            $today  = strtotime(date('Y-m-d'));
                                                            $diff   = ($expiry - $today) / (60 * 60 * 24); // difference in days

                                                            $badgeClass = 'bg-success'; // default green
                                                            if ($diff <= 1) {
                                                                $badgeClass = 'bg-danger'; // red
                                                            } elseif ($diff >= 2 && $diff <= 3) {
                                                                $badgeClass = 'bg-warning'; // yellow
                                                            }
                                                            ?>
                                                            <td class="px-0">
                                                                <span class="badge <?= $badgeClass ?>">
                                                                    <?= date('d-m-Y', $expiry) ?>
                                                                </span>
                                                            </td>
                                                            
                                                        </tr>
                                                        <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 mt-4 mt-lg-0">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div>
                                                <h4 class="card-title mb-1">Telecaller Progress</h4>
                                                <p class="card-subtitle mb-0">Lead handling and policy sales progress</p>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table mb-0 align-middle">
                                                <thead>
                                                    <tr>
                                                        <th class="px-0 text-muted">Telecaller</th>
                                                        <th class="px-0 text-muted">Handled</th>
                                                        <th class="px-0 text-muted">Policies</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($telecallerProgress)): ?>
                                                        <?php foreach ($telecallerProgress as $progress): ?>
                                                            <?php
                                                            $totalLeads = (int) ($progress['total_leads'] ?? 0);
                                                            $handledLeads = (int) ($progress['handled_leads'] ?? 0);
                                                            $progressPercent = $totalLeads > 0 ? round(($handledLeads / $totalLeads) * 100) : 0;
                                                            $photo = $progress['profilePhoto'] ?? null;
                                                            $gender = strtolower(trim($progress['gender'] ?? ''));
                                                            if (empty($photo)) {
                                                                $photo = ($gender === 'male') ? 'user-1.jpg' : 'user-2.jpg';
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td class="px-0">
                                                                    <div class="d-flex align-items-center">
                                                                        <img src="<?= base_url('uploads/profile/' . $photo) ?>" class="rounded-circle" width="35" alt="flexy">
                                                                        <div class="ms-3">
                                                                            <h6 class="mb-0 fw-bolder"><?= esc($progress['name'] ?? '') ?></h6>
                                                                            <small class="text-muted"><?= $totalLeads; ?> leads</small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="px-0">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <div class="progress flex-grow-1" style="height: 8px; min-width: 90px;">
                                                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progressPercent; ?>%" aria-valuenow="<?= $progressPercent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>
                                                                        <span class="small text-muted"><?= $handledLeads; ?>/<?= $totalLeads; ?></span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-0">
                                                                    &nbsp;&nbsp;&nbsp;<span class="badge bg-primary-subtle text-primary"><?= (int) ($progress['policies_sold'] ?? 0); ?></span>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="3" class="text-muted text-center py-3">No telecaller progress data available.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Subscription Card -->
                            <div class="col-lg-4 ">
                                <div class="card shadow-none border">
                                    <div class="card-body">
                                        <h4 class="fw-semibold mb-3"><i
                                                class="ti ti-currency-dollar"></i>&nbsp;Subscription</h4>
                                        <div class="row">
                                            <?php foreach ($employees as $emp): ?>
                                            <?php
                                                $endDate = strtotime($emp['endDate']);
                                                $today   = strtotime(date('Y-m-d'));
                                                $daysRemaining = ceil(($endDate - $today) / (60 * 60 * 24));

                                                if ($daysRemaining < 0) {
                                                    $statusText  = "Expired";
                                                    $statusClass = "text-danger";
                                                } elseif ($daysRemaining >= 10) {
                                                    $statusText  = $daysRemaining . " days remaining";
                                                    $statusClass = "text-success";
                                                } elseif ($daysRemaining >= 0 && $daysRemaining <= 10) {
                                                    $statusText  = $daysRemaining . " days remaining";
                                                    $statusClass = "text-warning";
                                                } else {
                                                    $statusText  = $daysRemaining . " days remaining";
                                                    $statusClass = "text-danger";
                                                }

                                                $photo = $emp['profilePhoto'] ?? null;
                                                $gender = strtolower(trim($emp['gender'] ?? ''));
                                                if (empty($photo)) {
                                                    $photo = ($gender === 'male') ? 'user-1.jpg' : 'user-2.jpg';
                                                }
                                                ?>
                                            <div class="col-md-4 mb-4">
                                                <a href="<?= base_url('/admin/subscription') ?>">
                                                    <div class="position-relative">
                                                        <img src="<?= base_url('uploads/profile/' . $photo) ?>"
                                                            alt="employee-img" class="rounded-1 img-fluid mb-4">
                                                        <div class="position-absolute start-50 translate-middle-x bg-dark bg-opacity-75 px-3 py-1 rounded <?= $statusClass; ?>"
                                                            style="bottom: 1px;">
                                                            <?= $statusText; ?>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <?php endforeach; ?>
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

    <script>
    const todaysCount = <?= $todaysCount; ?>;
    const monthlyCount = <?= $monthlyCount; ?>;
    const allCount = <?= $totalPolicies; ?>;

    const options = ["All-time", "Monthly", "Today"];
    let currentIndex = 0;

    document.getElementById("nextOption").addEventListener("click", () => {
        currentIndex = (currentIndex + 1) % options.length;
        const option = options[currentIndex];

        document.getElementById("policyOption").textContent = option;
        document.getElementById("policyLabel").textContent =
            option === "Today" ? "Today's Policies" :
            option === "Monthly" ? "This Month's Policies" : "All Policies";

        document.getElementById("policyCount").textContent =
            option === "Today" ? todaysCount :
            option === "Monthly" ? monthlyCount : allCount;
    });
    </script>
    <script>
    const usedData = <?= $usedData; ?>;
    const unusedData = <?= $unusedData; ?>;

    const dataOptions = ["Unused", "Used"];
    let dataIndex = 0;

    document.getElementById("nextDataOption").addEventListener("click", () => {
        dataIndex = (dataIndex + 1) % dataOptions.length;
        const option = dataOptions[dataIndex];

        document.getElementById("dataOption").textContent = option;
        document.getElementById("dataLabel").textContent =
            option === "Used" ? "Used Data" : "Unused Data";

        document.getElementById("dataCount").textContent =
            option === "Used" ? usedData : unusedData;
    });
    </script>
</body>

</html>