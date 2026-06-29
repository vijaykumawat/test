<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flexy Free Bootstrap Admin Template by WrapPixel</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
          <link rel="stylesheet" href="<?= base_url('/assets/css/common.css') ?>" />
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

    .mb-md-5 {
        margin-bottom: 0 !important;
    }

    .mb-7 {
        margin-bottom: 0 !important;
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
                            <div class="col-lg-12 col-md-12 col-12 align-self-center">
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
                                                Policy
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item" aria-current="page">
                                            <?= esc($policy['policy_number'] ?? '') ?></li>
                                    </ol>
                                </nav>
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

                        <div class="col-12 ">
                            <!-- start Default Basic Forms -->
                            <form action="<?= site_url('admin/policy-update') ?>" method="post"
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
                                            <input type="hidden" name="policy_id" id="policy_id"
                                                value="<?= esc($policy['policy_id'] ?? '') ?>">
                                            <button type="submit" id="saveBtn"
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
                                            <label class="col-md-2 col-form-label d-flex justify-content-end">Holder
                                                Name</label>
                                            <div class="col-md-4 d-flex align-items-center d-flex justify-content-end">
                                                <span id="holderNameView"
                                                    class="flex-grow-1 "><?= esc($policy['holder_name'] ?? '') ?></span>
                                                <input id="holderNameEdit" name="holderName" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($policy['holder_name'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('holderName')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                            <label class="col-md-2 col-form-label d-flex justify-content-end">Policy
                                                Number</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="policyNumberView"
                                                    class="flex-grow-1"><?= esc($policy['policy_number'] ?? '') ?></span>
                                                <input id="policyNumberEdit" name="policyNumber" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($policy['policy_number'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('policyNumber')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>

                                        <!-- Dob -->
                                        <div class="mb-1 row">

                                        </div>

                                        <!-- Email -->
                                        <div class="mb-1 row">
                                            <label class="col-md-2 col-form-label d-flex justify-content-end">Company
                                                Name</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="companyNameView"
                                                    class="flex-grow-1"><?= esc($policy['company_name'] ?? '') ?></span>
                                                <input id="companyNameEdit" name="companyName" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($policy['company_name'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('companyName')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                            <label class="col-md-2 col-form-label d-flex justify-content-end">Vehicle
                                                Number</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="vehicleNumberView"
                                                    class="flex-grow-1"><?= esc($policy['vehicle_number'] ?? '') ?></span>
                                                <input id="vehicleNumberEdit" name="vehicleNumber" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($policy['vehicle_number'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('vehicleNumber')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>

                                        <!-- Contact No -->
                                        <div class="mb-1 row">
                                            <label class="col-md-2 col-form-label d-flex justify-content-end">Mobile
                                                No</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="mobileNoView"
                                                    class="flex-grow-1"><?= esc($policy['mobileNo'] ?? '') ?></span>
                                                <input id="mobileNoEdit" name="mobileNo" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($policy['mobileNo'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('mobileNo')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Telecaller</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="telecallerView" class="flex-grow-1">
                                                    <?= esc($telecaller) ?>
                                                </span>
                                                <select class="form-control d-none flex-grow-1" id="telecallerEdit" name="telecaller">
                                                    <option value="">--Select Employee--</option>
                                                    <?php foreach ($employees as $emp): ?>
                                                        <option value="<?= esc($emp['employeeId']) ?>"
                                                            <?= ($policy['telecaller'] == $emp['employeeId']) ? 'selected' : '' ?>>
                                                            <?= esc($emp['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>


                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('telecaller')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <label class="col-md-2 col-form-label d-flex justify-content-end">Issue Date
                                                No</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="issueDateView"
                                                    class="flex-grow-1"><?= esc($policy['issue_date'] ?? '') ?></span>
                                                <input id="issueDateEdit" name="issueDate" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($policy['issue_date'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('issueDate')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                            <label class="col-md-2 col-form-label d-flex justify-content-end">Expiry
                                                Date</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="expiryDateView"
                                                    class="flex-grow-1"><?= esc($policy['expiry_date'] ?? '') ?></span>
                                                <input id="expiryDateEdit" name="expiryDate" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($policy['expiry_date'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('expiryDate')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>





                                    </div>
                                </div>
                            </form>
                            <!-- end Default Basic Forms -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->include('admin/script'); ?>
    <script>
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

    const profileImage = document.getElementById('profileImage');
    const profileInput = document.getElementById('profileInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadForm = document.getElementById('uploadForm');

    // When user clicks on image, open file selector
    profileImage.addEventListener('click', () => {
        profileInput.click();
    });

    // Preview selected image immediately
    profileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                profileImage.src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Upload button submits the form
    uploadBtn.addEventListener('click', () => {
        if (profileInput.files.length === 0) {
            alert("Please select an image by clicking on the profile picture.");
        } else {
            uploadForm.submit();
        }
    });
    resetBtn.addEventListener('click', () => {
        profileImage.src = profileImage.getAttribute('data-default');
        profileInput.value = ""; // clear file input
    });
    </script>
    <script>
    const baseUrl = "<?= base_url() ?>";
    const searchCustomerUrl = "<?= site_url('admin/searchCustomerAjax') ?>";
</script>

<script src="<?= base_url('assets/js/customer-search.js') ?>"></script>
</body>

</html>