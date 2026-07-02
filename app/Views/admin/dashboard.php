<!doctype html>
<html lang="en">

<head>
    <?= $this->include('admin/link'); ?>
    <link rel="stylesheet" href="<?= base_url('/assets/css/common.css') ?>" />
    
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
            <?php include 'header.php'; ?>
            <!--  Header End -->
            <div class="body-wrapper-inner">
                <div class="container-fluid" style="padding-top:125px;">
                    <div class="page-titles mb-7 mb-md-5">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="card welcome-card2 overflow-hidden bg-primary-subtle border-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start position-relative">
                                            <div>
                                                <h4 class="fw-bolder fs-5">Policies</h4>
                                                <h2 class="text-primary fs-7"><?= $totalPolicies; ?></h2>
                                            </div>
                                            <div class="ms-auto">
                                                <span
                                                    class="btn round-48 fs-7 rounded-circle btn-primary d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-notes"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <a href="<?= site_url('admin/search-policy') ?>"
                                            class="btn btn-primary position-relative mt-2">Open</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card welcome-card2 overflow-hidden bg-success-subtle border-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start position-relative">
                                            <div>
                                                <h4 class="fw-bolder fs-5">Calling Data</h4>
                                                <h2 class="text-primary fs-7"><?= $totalData; ?></h2>
                                            </div>
                                            <div class="ms-auto">
                                                <span
                                                    class="btn round-48 fs-7 rounded-circle btn-success d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-database"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <a href="<?= base_url('/admin/all-data') ?>"
                                            class="btn btn-success position-relative mt-2">Open</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 align-self-center">
                                <div class="card shadow-none border">
                                    <div class="card-body">
                                        <h4 class="fw-semibold mb-3"><i class="ti ti-currency-dollar"></i>
                                            &nbsp;Subscription</h4>
                                        <div class="row">
                                            <?php foreach ($employees as $emp): ?>
                                            <?php
                                              // Calculate remaining days
                                              $endDate = strtotime($emp['endDate']);
                                              $today   = strtotime(date('Y-m-d'));
                                              $daysRemaining = ceil(($endDate - $today) / (60 * 60 * 24));

                                              // Decide text and color
                                              if ($daysRemaining < 0) {
                                                  $statusText  = "Expired";
                                                  $statusClass = "text-danger";
                                              } elseif ($daysRemaining >= 10) {
                                                  $statusText  = $daysRemaining . " days remaining";
                                                  $statusClass = "text-success";
                                              } elseif ($daysRemaining >= 0 && $daysRemaining <= 10) {
                                                  $statusText  = $daysRemaining . " days remaining";
                                                  $statusClass = "text-warning";
                                              } else { // 1 to 4 days
                                                  $statusText  = $daysRemaining . " days remaining";
                                                  $statusClass = "text-danger";
                                              }
                                              $photo = $emp['profilePhoto'] ?? null;

                                                if (empty($photo)) {
                                                    // Fallback based on session gender
                                                    //$gender = $emp['gender'];
                                                    $gender = strtolower(trim($emp['gender'] ?? ''));
                                                    $photo = ($gender === 'male') ? 'user-1.jpg' : 'user-2.jpg';
                                                }
                                            ?>
                                            <div class="col-md-4 mb-4">
                                                <a href="<?= base_url('/admin/subscription') ?>">

                                                    <div class="position-relative">
                                                        <img src="<?= base_url('uploads/profile/' . $photo) ?>"
                                                            alt="employee-img" class="rounded-1 img-fluid mb-4">
                                                        <!--
                                                        <div class="position-absolute start-50 translate-middle-x bg-opacity-75 px-3 py-1 rounded <?= $statusClass; ?>"
                                                        style="bottom: 100px; ">
                                                            <?= esc(explode(' ', $emp['name'])[0]) ?>
                                                        </div> -->
                                                        <!-- Overlay lifted above bottom with semi-transparent dark background -->
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

</body>

</html>