<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GB Insurance Registration</title>
    <link rel="shortcut icon" type="image/png" href="./assets/images/logos/reg_background.png" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <style>
    .bg-left {
        background: url('./assets/images/logos/reg_background.png') no-repeat center center;
        background-size: cover;
        width: 100vh;
        /* set desired width */
        height: 100vh;
        /* full viewport height */
    }

    .form-card {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 8px;
        padding: 2rem;
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row min-vh-100">

            <!-- Left side background image -->
            <div class="col-md-6 bg-left d-none d-md-block"></div>

            <!-- Right side form -->
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <div class="form-card shadow w-75">
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

                    <h2 class="text-center mb-4">Sign Up</h2>
                    <form action="<?= site_url('auth/employee-add') ?>" method="post" enctype="multipart/form-data"
                        class="needs-validation was-validated" id="employeeForm" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" name="name" placeholder="First name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="email">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" placeholder="Username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" name="dateOfBirth" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" name="phoneNumber" pattern="[0-9]{10}"
                                    placeholder="10-digit number" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender</label>
                                <select class="form-control" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Screenshot (JPG only)</label>
                                <input type="file" class="form-control" name="paymentScreenshot" accept=".jpg,.jpeg"
                                    required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">Sign Up</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>