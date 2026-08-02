<!doctype html>
<html lang="en">

<head>
    <?= $this->include('admin/link'); ?>
    <link rel="stylesheet" href="<?= base_url('/assets/css/common.css') ?>" />
    <style>
    .policy-card {
        border-radius: 10px;
        background: linear-gradient(135deg, #e8f0ff, #f8fbff);
        transition: all 0.3s ease;
    }

    .toggle-control span {
        font-size: 0.8rem;
        /* smaller font */
    }

    .toggle-control button i {
        font-size: 0.9rem;
    }

    .toggle-control button {
        text-decoration: none !important;
    }

    .toggle-control button:hover {
        text-decoration: none !important;
    }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <?= $this->include('superadmin/sidebar'); ?>

        <div class="body-wrapper">
            <?= $this->include('superadmin/header'); ?>

            <div class="body-wrapper-inner">
                <div class="container-fluid" style="padding-top:125px;">
                    <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-circle-check me-2"></i>
                        <?= session()->getFlashdata('success'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-alert-circle me-2"></i>
                        <?= session()->getFlashdata('error'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('warning')): ?>
                        <div class="alert alert-warning alert-dismissible fade show">
                            <?= session()->getFlashdata('warning'); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <div class="page-titles mb-7 mb-md-5">
                        <div class="row">
                            <div class="col-lg-4">

                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $this->include('superadmin/script'); ?>


</body>

</html>