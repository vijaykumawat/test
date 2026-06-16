<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Flexy Free Bootstrap Admin Template by WrapPixel</title>
  <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
      <style>
    /* Ensure no top gap after removing app-topstrip */
    html, body { margin: 0; padding: 0; }
    #main-wrapper, .page-wrapper { padding-top: 0 !important; }
    .body-wrapper, .body-wrapper-inner { margin-top: 0 !important; padding-top: 0 !important; }
    .app-header, .navbar { top: 0 !important; margin-top: 0 !important; padding-top: 0 !important; }

    /* fix left sidebar when layout is fixed */
    #main-wrapper[data-layout="vertical"][data-sidebar-position="fixed"] .left-sidebar { top: 0 !important; }
      </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar Start -->
    <?php include 'sidebar.php'; ?><!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <?php include 'header.php'; ?>
      <!--  Header End -->
      <div class="body-wrapper-inner">
        <div class="container-fluid">
        


        </div>
      </div>
    </div>
  </div>
  <script src="<?= base_url('/assets/libs/jquery/dist/jquery.min.js') ?>"></script>
  <script src="<?= base_url('/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('/assets/js/sidebarmenu.js') ?>"></script>
  <script src="<?= base_url('/assets/js/app.min.js') ?>"></script>
  <script src="<?= base_url('/assets/libs/apexcharts/dist/apexcharts.min.js') ?>"></script>
  <script src="<?= base_url('/assets/libs/simplebar/dist/simplebar.js') ?>"></script>
  <script src="<?= base_url('/assets/js/dashboard.js') ?>"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>