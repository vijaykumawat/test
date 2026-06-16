<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flexy Free Bootstrap Admin Template by WrapPixel</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <style>
    /* Ensure removing the topstrip leaves no empty gap */

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
                                        <li class="breadcrumb-item">
                                            <a class="text-muted text-decoration-none" href="<?= base_url('/admin/employees') ?>">
                                                Employees
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item" aria-current="page">New Employee</li>
                                    </ol>
                                </nav>
                                <h2 class="mb-0 fw-bolder fs-8">Employee Management</h2>
                            </div>
                            <div class="col-lg-4 col-md-6 d-none d-md-flex align-items-center justify-content-end">
                                
                                <a href="<?= base_url('/admin/employees/new') ?>" class="btn btn-primary d-flex align-items-center ms-2">
                                    <i class="ti ti-plus me-1"></i>
                                    Add New
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <!-- start Event Registration -->
                            <div class="card">
                                <form action="<?= site_url('admin/upload-data') ?>" method="post"
                                    enctype="multipart/form-data" class="upload-box" id="uploadForm">
                                    <div class="card-body">

                                        <h4 class="card-title"><i> <img
                                                    src="<?= base_url('assets/images/logos/1.png') ?>" width="30"
                                                    height="30" alt="Icon"> </i>&nbsp; Data Loader</h4>
                                        <p class="card-subtitle mb-2">CSV files only • Drag & Drop here • If data is
                                            available, it won't be overwritten</p>
                                        <?php if (session()->has('success')): ?>
                                        <div class="alert bg-success-subtle text-success alert-dismissible fade show mb-3"
                                            role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-circle-check me-2 fs-4"></i>
                                                <?= session('success') ?>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (session()->has('error')): ?>
                                        <div class="alert bg-danger-subtle text-danger alert-dismissible fade show mb-3"
                                            role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-alert-circle me-2 fs-4"></i>
                                                <?= session('error') ?>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (session()->has('warning')): ?>
                                        <div class="alert bg-warning-subtle text-warning alert-dismissible fade show mb-3"
                                            role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-alert-triangle me-2 fs-4"></i>
                                                <?= session('warning') ?>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>
                                        <div id="alertBox"></div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Select File</label>
                                                    <div class="input-group flex-nowrap">
                                                        <div class="custom-file">
                                                            <div class="upload-box border border-dashed p-4 text-center"
                                                                id="csvDropArea">
                                                                <p class="mb-2">Drag & Drop CSV file here</p>
                                                                <p class="small text-muted">or click below to select</p>
                                                                <input id="csvUpload" type="file"
                                                                    class="form-control mt-2" name="csv_file"
                                                                    accept=".csv,text/csv" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-3 border-top">
                                        <div class="d-flex flex-wrap gap-6 align-items-center">
                                            <div>

                                            </div>
                                            <div class="ms-auto d-flex flex-wrap gap-6 align-items-center">
                                                <div class="ms-auto">
                                                    <button type="button" onclick=""
                                                        class="btn btn-outline-danger btn-sm shadow-sm">
                                                        <i class="ti ti-trash me-2"></i>
                                                        Remove all Data
                                                    </button>
                                                </div>
                                                <div class="ms-auto">
                                                    <button type="button" id="openMappingBtn"
                                                        class="btn btn-outline-secondary btn-sm shadow-sm me-2">Toggle
                                                        Mapping</button>
                                                    <button type="button" id="uploadBtn"
                                                        class="btn btn-outline-success btn-sm shadow-sm" disabled>
                                                        <i class="ti ti-upload me-2"></i>
                                                        Upload
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- end Event Registration -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->include('admin/script'); ?>
</body>

</html>