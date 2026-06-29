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
                                        <li class="breadcrumb-item" aria-current="page">
                                            <?= esc($employee['name'] ?? '') ?></li>
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
                        <div class="col-lg-4 d-flex align-items-stretch">
                            <div class="card w-100 border position-relative overflow-hidden">
                                <div class="card-body p-4">
                                    <h4 class="card-title">Change Profile</h4>
                                    <p class="card-subtitle mb-4">Change your profile picture from here</p>
                                    <div class="text-center">
                                        <?php
                                            // Check if profile photo exists
                                            $photo = $employee['profilePhoto'] ?? null;
                                            if (empty($photo)) {
                                                //$gender = $employee['gender'];
                                                $gender = strtolower(trim($employee['gender'] ?? ''));
                                                $photo = ($gender === 'male') ? 'user-1.jpg' : 'user-2.jpg';
                                            }
                                        ?>
                                        <!-- Profile picture (clickable) -->
                                        <img id="profileImage" src="<?= base_url('uploads/profile/' . $photo) ?>"
                                            alt="profile-img" class="img-fluid rounded-circle" width="120" height="120"
                                            style="cursor:pointer;" data-default="<?= base_url('uploads/profile/' . $photo) ?>">

                                        <!-- Hidden file input -->
                                        <form id="uploadForm" action="<?= base_url('/admin/uploadProfilePhoto') ?>"
                                            method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="employeeId" value="<?php echo $employee['employeeId']; ?>">
                                            <input type="file" id="profileInput" name="profilePhoto"
                                                accept=".jpg,.jpeg,.png,.gif" style="display:none;" required>
                                        </form>

                                        <div class="d-flex align-items-center justify-content-center my-4 gap-6">
                                            <button type="button" class="btn btn-primary" id="uploadBtn">Upload</button>
                                            <button type="button" class="btn bg-danger-subtle text-danger" id="resetBtn">Reset</button>
                                        </div>
                                        <p class="mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-8 ">
                            <!-- start Default Basic Forms -->
                            <form action="<?= site_url('admin/employee-update') ?>" method="post"
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
                                            <input type="hidden" name="employeeId" id="employeeId"
                                                value="<?= esc($employee['employeeId'] ?? '') ?>">
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
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Name</label>
                                            <div class="col-md-4 d-flex align-items-center d-flex justify-content-end">
                                                <span id="nameView"
                                                    class="flex-grow-1 "><?= esc($employee['name'] ?? '') ?></span>
                                                <input id="nameEdit" name="name" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['name'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('name')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Dob</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="dobView"
                                                    class="flex-grow-1"><?= esc($employee['dateOfBirth'] ?? '') ?></span>
                                                <input id="dobEdit" name="dob" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['dateOfBirth'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('dob')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>

                                        <!-- Dob -->
                                        <div class="mb-1 row">

                                        </div>

                                        <!-- Email -->
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Email</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="emailView"
                                                    class="flex-grow-1"><?= esc($employee['email'] ?? '') ?></span>
                                                <input id="emailEdit" name="email" type="email"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['email'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('email')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Status</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="statusView"
                                                    class="flex-grow-1"><?php if($employee['isActive']==1): ?>Active<?php else: ?>Inactive<?php endif; ?></span>
                                                <select class="form-control d-none flex-grow-1" id="statusEdit"
                                                    name="status">
                                                    <option>--Select Status--</option>
                                                    <option>Active</option>
                                                    <option>Inactive</option>
                                                </select>

                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('status')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>

                                        <!-- Contact No -->
                                        <div class="mb-1 row">
                                            <label class="col-md-2 col-form-label d-flex justify-content-end">Contact
                                                No</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="mobileView"
                                                    class="flex-grow-1"><?= esc($employee['phoneNumber'] ?? '') ?></span>
                                                <input id="mobileEdit" name="contactNo" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['phoneNumber'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('mobile')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Gender</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="genderView"
                                                    class="flex-grow-1"><?= esc($employee['gender'] ?? '') ?></span>
                                                <input id="genderEdit" name="gender" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['gender'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('gender')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>



                                        <!-- Address -->
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Address</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="addressView"
                                                    class="flex-grow-1"><?= esc($employee['address'] ?? '') ?></span>
                                                <input id="addressEdit" name="address" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['address'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('address')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Username</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="usernameView"
                                                    class="flex-grow-1"><?= esc($employee['username'] ?? '') ?></span>
                                                <input id="usernameEdit" name="username" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['username'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('username')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>

                                        <!-- Pincode -->
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Pincode</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="pincodeView"
                                                    class="flex-grow-1"><?= esc($employee['pincode'] ?? '') ?></span>
                                                <input id="pincodeEdit" name="pincode" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['pincode'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('pincode')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Password</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="passwordView"
                                                    class="flex-grow-1"><?= esc($employee['password'] ?? '') ?></span>
                                                <input id="passwordEdit" name="password" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['password'] ?? '') ?>">

                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('password')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Designation</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="jobTitleView"
                                                    class="flex-grow-1"><?= esc($employee['jobTitle'] ?? '') ?></span>
                                                <input id="jobTitleEdit" name="jobTitle" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['jobTitle'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('jobTitle')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                            <label class="col-md-2 col-form-label d-flex justify-content-end">Joining
                                                Date</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="hireDateView"
                                                    class="flex-grow-1"><?= esc($employee['hireDate'] ?? '') ?></span>
                                                <input id="hireDateEdit" name="hireDate" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['hireDate'] ?? '') ?>">

                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('hireDate')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Salarry</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="salaryView"
                                                    class="flex-grow-1"><?= esc($employee['salary'] ?? '') ?></span>
                                                <input id="salaryEdit" name="salary" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['salary'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('salary')"><i class="ti ti-pencil"></i></button>
                                            </div>
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Nationality</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="nationalityView"
                                                    class="flex-grow-1"><?= esc($employee['nationalId'] ?? '') ?></span>
                                                <input id="nationalityEdit" name="nationalId" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['nationalId'] ?? '') ?>">

                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('nationality')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <label class="col-md-2 col-form-label d-flex justify-content-end">Account
                                                No</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="bankAccountNumberView"
                                                    class="flex-grow-1"><?= esc($employee['bankAccountNumber'] ?? '') ?></span>
                                                <input id="bankAccountNumberEdit" name="bankAccountNumber" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['bankAccountNumber'] ?? '') ?>">
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('bankAccountNumber')"><i
                                                        class="ti ti-pencil"></i></button>
                                            </div>
                                            <label
                                                class="col-md-2 col-form-label d-flex justify-content-end">Location</label>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <span id="workLocationView"
                                                    class="flex-grow-1"><?= esc($employee['workLocation'] ?? '') ?></span>
                                                <input id="workLocationEdit" name="workLocation" type="text"
                                                    class="form-control d-none flex-grow-1"
                                                    value="<?= esc($employee['workLocation'] ?? '') ?>">

                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                    onclick="startEdit('workLocation')"><i
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