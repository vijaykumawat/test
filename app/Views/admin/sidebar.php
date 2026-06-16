<?php
$currentRoute = service('router')->getMatchedRoute()[0]; 
// e.g. "admin/upload"
?>


<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="<?= base_url('/admin') ?>" class="text-nowrap logo-img" style="margin-top:15px;">
                <img src="<?= base_url('/assets/images/logos/admin.png') ?>" alt="" width=60px; height=60px; />
            <strong class="ms-2" style="font-size:30px; position:relative; top:10px;"> &nbsp;&nbsp;Admin</strong>

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
                    <a class="sidebar-link" href="<?= base_url('/admin') ?>" aria-expanded="false">
                        <i class="ti ti-atom"></i>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('/admin/data-loader') ?>" aria-expanded="false">
                        <i class="ti ti-cloud-upload"></i>
                        <span class="hide-menu">Data Loader</span>
                    </a>
                </li>
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
                
                <li class="sidebar-item">
                    <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)"
                        aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex">
                                <i class="ti ti-user-circle"></i>
                            </span>
                            <span class="hide-menu">Employee</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" href="<?= base_url('/admin/employees') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">All Employee</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between"
                                href="<?= site_url('/admin/employees/new') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Add New Employee</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)"
                                aria-expanded="false">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Attendance</span>
                                </div>
                            </a>
                            <ul aria-expanded="false" class="collapse second-level">
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="<?= site_url('/admin/attendance/mark') ?>">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="round-16 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-point-filled"></i>
                                            </div>
                                            <span class="hide-menu">Mark Attendance</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="<?= site_url('/admin/attendance/report') ?>">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="round-16 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-point-filled"></i>
                                            </div>
                                            <span class="hide-menu">Attendance Report</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="<?= site_url('/admin/attendance/monthly') ?>">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="round-16 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-point-filled"></i>
                                            </div>
                                            <span class="hide-menu">Monthly Attendance</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="<?= site_url('/admin/attendance/history') ?>">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="round-16 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-point-filled"></i>
                                            </div>
                                            <span class="hide-menu">Attendance History</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                       
                    </ul>
                </li>


            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>