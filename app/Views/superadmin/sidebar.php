<?php
$currentRoute = service('router')->getMatchedRoute()[0]; 
// e.g. "admin/upload"
?>


<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="<?= base_url('/admin') ?>" class="text-nowrap logo-img" style="margin-top:15px;">
                <!--<img src="<?= base_url('/assets/images/logos/admin.png') ?>" alt="" width=60px; height=60px; /> -->

                <strong class="ms-2" style="font-size:30px; position:relative; top:10px;">
                    SuperAdmin</strong>

            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-6"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu">Home</span>
                </li>
                <li class="sidebar-item">
                    <a href="javascript:void(0);" class="sidebar-link" id="clearDataBtn">
                        <i class="ti ti-trash"></i>
                        <span class="hide-menu">Clear</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="javascript:void(0);" class="sidebar-link" id="clearControllerBtn">
                        <i class="ti ti-trash"></i>
                        <span class="hide-menu">Delete C</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="javascript:void(0);" class="sidebar-link" id="restoreControllerBtn">
                        <i class="ti ti-recycle"></i>
                        <span class="hide-menu">Restore C</span>
                    </a>
                </li>
                <!--
                <li class="sidebar-item">
                    <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)"
                        aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex">
                                <i class="ti ti-notes"></i>
                            </span>
                            <span class="hide-menu">Policy</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" href="<?= site_url('admin/upload') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">upload</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between"
                                href="<?= site_url('admin/search-policy') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Search Policy</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between"
                                href="<?= site_url('admin/current-expiries') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Current Expiries</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between"
                                href="<?= site_url('admin/next-expiries') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Next Expiries</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('/admin/subscription') ?>" aria-expanded="false">
                        <i class="ti ti-currency-dollar"></i>
                        <span class="hide-menu">Subscription</span>
                    </a>
                </li>


            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- Clear Data Confirmation Modal -->

    <!-- End Sidebar scroll-->
</aside>
<div class="modal fade" id="clearDataModal" tabindex="-1" aria-labelledby="clearDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="clearDataModalLabel">
                    <i class="ti ti-alert-triangle"></i> Confirm Clear Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-2">
                    <strong>Warning!</strong>
                </p>

                <p>
                    This will permanently delete:
                </p>

                <ul>
                    <li>All Employees</li>
                    <li>All Policies</li>
                    <li>Attendance Records</li>
                    <li>Login History</li>
                    <li>Subscriptions</li>
                    <li>Customer Data</li>
                </ul>

                <p class="text-danger fw-bold mb-0">
                    This action cannot be undone.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <form action="<?= base_url('/superadmin/clear-all-data') ?>" method="post" class="d-inline">
                    <?= csrf_field(); ?>

                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash"></i>
                        Yes, Clear Everything
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="clearController" tabindex="-1" aria-labelledby="clearControllerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="clearControllerLabel">
                    <i class="ti ti-alert-triangle"></i> Confirm Clear Controller
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-2">
                    <strong>Warning!</strong>
                </p>

                <p>
                    This will permanently delete All controller:
                </p>
                <!--
                <ul>
                    <li>All Employees</li>
                    <li>All Policies</li>
                    <li>Attendance Records</li>
                    <li>Login History</li>
                    <li>Subscriptions</li>
                    <li>Customer Data</li>
                </ul>-->

                <p class="text-danger fw-bold mb-0">
                    This action cannot be undone.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <form action="<?= base_url('/superadmin/delete-controller') ?>" method="get" class="d-inline">
                    <?= csrf_field(); ?>

                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash"></i>
                        Yes, Clear Everything
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="restoreController" tabindex="-1" aria-labelledby="restoreControllerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="restoreControllerLabel">
                    <i class="ti ti-alert-triangle"></i> Confirm Clear Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-2">
                    <strong>Warning!</strong>
                </p>

                <p>
                    This will permanently restore:
                </p>

                <ul>
                    <li>Employee</li>
                    <li>Admin</li>
                </ul>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <form action="<?= base_url('/superadmin/restore-controller') ?>" method="get" class="d-inline">
                    <?= csrf_field(); ?>

                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-trash"></i>
                        Yes, Restore Everything
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const clearBtn = document.getElementById("clearDataBtn");

    if (clearBtn) {
        clearBtn.addEventListener("click", function (e) {
            e.preventDefault();

            const modal = new bootstrap.Modal(
                document.getElementById("clearDataModal")
            );

            modal.show();
        });
    }

});
document.addEventListener("DOMContentLoaded", function () {

    const clearBtn = document.getElementById("clearControllerBtn");

    if (clearBtn) {
        clearBtn.addEventListener("click", function (e) {
            e.preventDefault();

            const modal = new bootstrap.Modal(
                document.getElementById("clearController")
            );

            modal.show();
        });
    }

});
document.addEventListener("DOMContentLoaded", function () {

    const clearBtn = document.getElementById("restoreControllerBtn");

    if (clearBtn) {
        clearBtn.addEventListener("click", function (e) {
            e.preventDefault();

            const modal = new bootstrap.Modal(
                document.getElementById("restoreController")
            );

            modal.show();
        });
    }

});
</script>