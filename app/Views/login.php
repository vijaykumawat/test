<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Flexy Free Bootstrap Admin Template by WrapPixel</title>
  <link rel="shortcut icon" type="image/png" href="<?php echo base_url(); ?>/assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="<?php echo base_url(); ?>/assets/css/styles.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
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

                <!--<a href="<?php echo base_url(); ?>/index.html" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="<?php echo base_url(); ?>/assets/images/logos/logo.svg" alt="">
                </a>-->
                <p class="text-center">Login to Your Account</p>
                <form action="<?php echo base_url(); ?>employee/login" method="post">
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Username</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" name="username" aria-describedby="emailHelp">
                  </div>
                  <div class="mb-4">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" name="password">
                  </div>
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                      <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                      <label class="form-check-label text-dark" for="flexCheckChecked">
                        Remeber this Device
                      </label>
                    </div>
                    <a class="text-primary fw-bold" href="<?php echo base_url(); ?>/index.html">Forgot Password ?</a>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign In </button>  
                  <!--<a href="<?php echo base_url(); ?>/employee/login" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign In</a>-->
                  <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">New to Gb ?</p>
                    <a class="text-primary fw-bold ms-2" href="<?php echo base_url(); ?>/authentication-register.html">Create an account</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="<?php echo base_url(); ?>/assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>