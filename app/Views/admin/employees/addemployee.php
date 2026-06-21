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
                                            <a class="text-muted text-decoration-none"
                                                href="<?= base_url('/admin/employees') ?>">
                                                Employees
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item" aria-current="page">New Employee</li>
                                    </ol>
                                </nav>
                                <h3 class="mb-0 fw-bolder fs-8">Add New Employee</h3>
                            </div>
                        </div>
                    </div>
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

                    <div class="row">
                        <div class="col-6">
                            <!-- start Default Basic Forms -->
                            <form action="<?= site_url('admin/employee-add') ?>" method="post"
                                enctype="multipart/form-data" id="employeeForm">
                                <div class="card">
                                    <div class="form-actions">
                                        <div class="card-body border-top">
                                            <!--<button type="submit" id="saveBtn"
                                                class="btn btn-outline-success btn-sm shadow-sm" disabled>
                                                Save
                                            </button>
                                            <button type="button" class="btn btn-outline-dark btn-sm shadow-sm "
                                                fdprocessedid="ik40r9">
                                                Cancel
                                            </button>-->
                                            <!--<button type="submit" id="saveBtn" class="btn btn-outline-success btn-sm shadow-sm" disabled>
                                                Save
                                            </button>-->
                                            <button type="button" id="saveBtn"
                                                class="btn btn-outline-success btn-sm shadow-sm" disabled>
                                                Save
                                            </button>
                                            <button type="button" id="cancelBtn"
                                                class="btn btn-outline-dark btn-sm shadow-sm">
                                                Cancel
                                            </button>

                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!--<h4 class="card-title">Default Basic Forms</h4>
                                    <p class="card-subtitle mb-3">
                                        All bootstrap element classies
                                    </p>-->
                                        <!-- Name -->
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-4 col-form-label d-flex justify-content-end">Name</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <span id="nameView"
                                                    class="flex-grow-1"><?= esc($uploadResults['name'] ?? '') ?></span>
                                                <input id="nameEdit" name="name" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($uploadResults['name'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('name')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>

                                        <!-- Dob -->
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-4 col-form-label d-flex justify-content-end">Dob</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <span id="dobView"
                                                    class="flex-grow-1"><?= esc($uploadResults['dob'] ?? '') ?></span>
                                                <input id="dobEdit" name="dob" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($uploadResults['dob'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('dob')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-4 col-form-label d-flex justify-content-end">Email</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <span id="emailView"
                                                    class="flex-grow-1"><?= esc($uploadResults['email'] ?? '') ?></span>
                                                <input id="emailEdit" name="email" type="email"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($uploadResults['email'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('email')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>

                                        <!-- Contact No -->
                                        <div class="mb-1 row">
                                            <label class="col-md-4 col-form-label d-flex justify-content-end ">Contact
                                                No</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <span id="mobileView"
                                                    class="flex-grow-1"><?= esc($uploadResults['mobile'] ?? '') ?></span>
                                                <input id="mobileEdit" name="contactNo" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($uploadResults['mobile'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('mobile')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>

                                        <!-- Gender -->
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-4 col-form-label d-flex justify-content-end">Gender</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <span id="genderView"
                                                    class="flex-grow-1"><?= esc($uploadResults['gender'] ?? '') ?></span>
                                                <input id="genderEdit" name="gender" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($uploadResults['gender'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('gender')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-4 col-form-label d-flex justify-content-end">Address</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <span id="addressView"
                                                    class="flex-grow-1"><?= esc($uploadResults['address'] ?? '') ?></span>
                                                <input id="addressEdit" name="address" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($uploadResults['address'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('address')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>

                                        <!-- Pincode -->
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-4 col-form-label d-flex justify-content-end">Pincode</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <span id="pincodeView"
                                                    class="flex-grow-1"><?= esc($uploadResults['pincode'] ?? '') ?></span>
                                                <input id="pincodeEdit" name="pincode" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($uploadResults['pincode'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('pincode')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>
                                        <!-- Username -->
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-4 col-form-label d-flex justify-content-end">Username</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <span id="usernameView"
                                                    class="flex-grow-1"><?= esc($uploadResults['username'] ?? '') ?></span>
                                                <input id="usernameEdit" name="username" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($uploadResults['username'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('username')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                        </div><!-- Password -->
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-4 col-form-label d-flex justify-content-end">Password</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <span id="passwordView"
                                                    class="flex-grow-1"><?= esc($uploadResults['mobile'] ?? '') ?></span>
                                                <input id="passwordEdit" name="password" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($uploadResults['mobile'] ?? '') ?>">

                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('password')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <label class="col-md-4 col-form-label d-flex justify-content-end">Profile
                                                Img</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <input id="profile_img" type="file" class="form-control mt-2"
                                                    name="profile_img" accept=".jpg,.jpeg">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header text-danger">
                                                <h5 class="modal-title bg-danger-subtle">Purchase Subscription</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>You must purchase a subscription before adding a new employee.</p>
                                                <div class="mb-3">
                                                    <label for="paymentScreenshot" class="form-label">Upload Payment
                                                        Screenshot</label>
                                                    <input type="file" class="form-control" id="paymentScreenshot"
                                                        name="paymentScreenshot" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" form="employeeForm" id="finalSubmitBtn"
                                                    class="btn btn-danger" disabled>
                                                    Submit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- end Default Basic Forms -->
                        </div>
                        <div class="col-6">
                            <!-- start Event Registration -->
                            <div class="card">
                                <form action="<?= site_url('admin/extract-data') ?>" method="post"
                                    enctype="multipart/form-data" class="upload-box" id="uploadForm">
                                    <div class="card-body">

                                        <h4 class="card-title"><i> <img
                                                    src="<?= base_url('assets/images/logos/12.png') ?>" width="40"
                                                    height="40" alt="Icon"> </i>&nbsp; ID Proof</h4>
                                        <p class="card-subtitle mb-2">Pdf, Jpg files only • Drag & Drop here • If data
                                            is
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

                                                            <p class="mb-2">Drag & Drop Pdf, Jpg file here</p>
                                                            <p class="small text-muted">or click below to select</p>
                                                            <input id="idproof" type="file" class="form-control mt-2"
                                                                name="idproof" accept=".pdf,.jpg,.jpeg" required>
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
                                                    <button type="submit" id="uploadBtn"
                                                        class="btn btn-outline-success btn-sm shadow-sm">
                                                        <i class="ti ti-upload me-2"></i>
                                                        Extract Data
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
    <script>
    const screenshotInput = document.getElementById('paymentScreenshot');
    const finalSubmitBtn = document.getElementById('finalSubmitBtn');

    screenshotInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            finalSubmitBtn.disabled = false; // enable submit
        } else {
            finalSubmitBtn.disabled = true; // keep disabled if no file
        }
    });

    function toggleEdit(field) {
        const view = document.getElementById(field + 'View');
        const edit = document.getElementById(field + 'Edit');

        if (edit.classList.contains('d-none')) {
            // Switch to edit mode
            view.classList.add('d-none');
            edit.classList.remove('d-none');
            edit.focus();
        } else {
            // Switch back to view mode with updated text
            view.textContent = edit.value;
            view.classList.remove('d-none');
            edit.classList.add('d-none');
        }
    }

    // Store original values at page load
    const originalValues = {};
    ['name', 'dob', 'email', 'mobile', 'gender', 'address', 'pincode'].forEach(field => {
        originalValues[field] = document.getElementById(field + 'View').textContent;
    });

    function startEdit(field) {
        const view = document.getElementById(field + 'View');
        const edit = document.getElementById(field + 'Edit');
        const saveBtn = document.getElementById('saveBtn');

        // Show input, hide text
        view.classList.add('d-none');
        edit.classList.remove('d-none');
        edit.focus();

        // Enable Save button once editing starts
        saveBtn.disabled = false;

        // On blur, update and hide input
        edit.onblur = function() {
            view.textContent = edit.value;
            view.classList.remove('d-none');
            edit.classList.add('d-none');
        };

        // Allow pressing Enter to save
        edit.onkeydown = function(e) {
            if (e.key === 'Enter') {
                edit.blur(); // triggers blur handler
            }
        };
    }

    // Cancel button logic
    document.getElementById('cancelBtn').addEventListener('click', function() {
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true; // disable Save again

        // Restore all original values
        Object.keys(originalValues).forEach(field => {
            const view = document.getElementById(field + 'View');
            const edit = document.getElementById(field + 'Edit');
            view.textContent = originalValues[field];
            view.classList.remove('d-none');
            edit.classList.add('d-none');
            edit.value = originalValues[field]; // reset input value too
        });
    });

    document.getElementById('saveBtn').addEventListener('click', function() {
        var modal = new bootstrap.Modal(document.getElementById('subscriptionModal'));
        modal.show();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const saveBtn = document.getElementById('saveBtn');

        // Check if uploadResults has values in the UI
        const hasValues = ['name', 'dob', 'email', 'mobile', 'gender', 'address', 'pincode', 'username',
                'password'
            ]
            .some(field => {
                const view = document.getElementById(field + 'View');
                return view && view.textContent.trim() !== '';
            });

        if (hasValues) {
            saveBtn.disabled = false; // enable Save button
        }
    });
    </script>
</body>

</html>