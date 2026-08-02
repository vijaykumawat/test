<!doctype html>
<html lang="en">

<head>
    <?= $this->include('admin/link'); ?>
    <link rel="stylesheet" href="<?= base_url('/assets/css/common.css') ?>" />
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
                                        <li class="breadcrumb-item" aria-current="page">Generic Data Loader</li>
                                    </ol>
                                </nav>
                                <h2 class="mb-0 fw-bolder fs-8">Generic Data Loader</h2>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <!-- start Event Registration -->
                            <div class="card">
                                <form action="<?= site_url('admin/generic-upload-data') ?>" method="post"
                                    enctype="multipart/form-data" class="upload-box" id="uploadForm">
                                    <div class="card-body">
                                        <h4 class="card-title">
                                            <i><img src="<?= base_url('assets/images/logos/1.png') ?>" width="30"
                                                    height="30" alt="Icon"></i>
                                            &nbsp; Data Loader
                                        </h4>
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
                                        <!-- Dataset Type -->
                                        <div class="mb-3">
                                            <label class="form-label">Dataset Type</label>
                                            <select name="dataset_type" class="form-select" required>
                                                <option value="health">Health</option>
                                                <option value="vehicle">Commercial Vehicle</option>
                                                <option value="life">Life Insurance</option>
                                                <option value="generic">Generic</option>
                                            </select>
                                        </div>

                                        <!-- File Upload -->
                                        <div class="mb-3">
                                            <label class="form-label">Select File</label>
                                            <div class="upload-box border border-dashed p-4 text-center"
                                                id="csvDropArea">
                                                <p class="mb-2">Drag & Drop CSV file here</p>
                                                <p class="small text-muted">or click below to select</p>
                                                <input id="csvUpload" type="file" class="form-control mt-2"
                                                    name="csv_file" accept=".csv,text/csv" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-3 border-top">
                                        <div class="d-flex flex-wrap gap-6 align-items-center">
                                            <div class="ms-auto">
                                                    <button type="button" onclick="removeAllData()"
                                                        class="btn btn-outline-danger btn-sm shadow-sm">
                                                        <i class="ti ti-trash me-2"></i>
                                                        Remove all Data
                                                    </button>
                                            </div>
                                            <div class="ms-auto d-flex flex-wrap gap-6 align-items-center">
                                                <button type="submit" id="uploadBtn"
                                                    class="btn btn-outline-success btn-sm shadow-sm" disabled>
                                                    <i class="ti ti-upload me-2"></i>
                                                    Upload
                                                </button>
                                            </div>
                                        </div>
                                        </div>
                                </form>

                                <script>
                                document.getElementById('csvUpload').addEventListener('change', function() {
                                    document.getElementById('uploadBtn').disabled = !this.files.length;
                                });
                                </script>

                            </div>
                            <!-- end Event Registration -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger-subtle">
                                <h5 class="modal-title text-danger" id="deleteConfirmLabel">
                                    <i class="ti ti-alert-triangle me-2"></i>Confirm Delete
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2"><strong>Warning!</strong> This action will permanently delete all records from the upload table.</p>
                                <p class="text-muted mb-0">This action cannot be undone. Are you sure you want to proceed?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="confirmDeleteBtn"
                                    class="btn btn-danger btn-sm shadow-sm">Delete All Data</button>
                            </div>
                        </div>
                    </div>
                </div>
    </div>
    <?= $this->include('admin/script'); ?>
    <script>
    document.getElementById('csvUpload').addEventListener('change', function() {
        document.getElementById('uploadBtn').disabled = !this.files.length;
    });
    document.addEventListener('DOMContentLoaded', () => {
        // Setup delete confirmation modal listener
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', async () => {
                    confirmDeleteBtn.disabled = true;
                    confirmDeleteBtn.textContent = 'Deleting...';

                    try {
                        const resp = await fetch('<?= site_url('admin/remove-generic-data') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        });
                        const json = await resp.json();
                        
                        if (json.success) {
                            const alertBox = document.getElementById('alertBox');
                            alertBox.innerHTML =
                                `<div class="alert bg-success-subtle text-success alert-dismissible fade show mb-3" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-circle-check me-2 fs-4"></i>
                                        ${json.message || 'All data deleted successfully!'}
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>`;
                            
                            // Close modal using backdrop click or hide
                            if (deleteModalInstance) {
                                deleteModalInstance.hide();
                            }
                            
                            // Refresh page after 2 seconds
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            const alertBox = document.getElementById('alertBox');
                            alertBox.innerHTML =
                                `<div class="alert bg-danger-subtle text-danger alert-dismissible fade show mb-3" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-alert-circle me-2 fs-4"></i>
                                        ${json.message || 'Failed to delete data.'}
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>`;
                            confirmDeleteBtn.disabled = false;
                            confirmDeleteBtn.textContent = 'Delete All Data';
                        }
                    } catch (err) {
                        const alertBox = document.getElementById('alertBox');
                        alertBox.innerHTML =
                            `<div class="alert bg-danger-subtle text-danger alert-dismissible fade show mb-3" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-alert-circle me-2 fs-4"></i>
                                    Delete error: ${err.message}
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`;
                        confirmDeleteBtn.disabled = false;
                        confirmDeleteBtn.textContent = 'Delete All Data';
                    }
                });
            }


    });                                        
            function removeAllData() {
                deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteConfirmModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                deleteModalInstance.show();
            }    
    </script>
</body>

</html>