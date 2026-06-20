<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flexy Free Bootstrap Admin Template by WrapPixel</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/toast.css') ?>" />
    <style>
    /* Ensure no top gap after removing app-topstrip */
    html,
    body {
        margin: 0;
        padding: 0;
    }

    #main-wrapper,
    .page-wrapper {
        padding-top: 0 !important;
    }

    .body-wrapper,
    .body-wrapper-inner {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .app-header,
    .navbar {
        top: 0 !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    /* fix left sidebar when layout is fixed */
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
        <?php include 'sidebar.php'; ?>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
             <?= $this->include('admin/header'); ?>
           <!--  Header End -->
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">



                        <div class="col-12">
                            <!-- start Event Registration -->
                            <div class="card">
                                <form action="<?= site_url('admin/upload') ?>" method="post"
                                    enctype="multipart/form-data" class="upload-box" id="uploadForm">
                                    <div class="card-body">
                                        <h4 class="card-title">Upload Policy PDFs</h4>
                                        <p class="card-subtitle mb-3">PDF files only • Drag & Drop here • Duplicate
                                            policies will be skipped</p>
                                        <?php if (!empty($results)): ?>
                                        <div class="alert bg-success-subtle text-info alert-dismissible fade show"
                                            role="alert">
                                            <div class="d-flex align-items-center text-primary ">
                                                <i class="ti ti-success-circle me-2 fs-4"></i>
                                                Success! Policy uploaded successfully!
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close" fdprocessedid="ed1z7v"></button>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (session()->has('error')): ?>
                                        <div class="alert bg-danger-subtle text-info alert-dismissible fade show"
                                            role="alert">
                                            <div class="d-flex align-items-center text-danger ">
                                                <i class="ti ti-info-circle me-2 fs-4"></i>
                                                <?= session('error') ?>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close" fdprocessedid="ed1z7v"></button>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (session()->has('warning')): ?>
                                        <div class="alert bg-warning-subtle text-info alert-dismissible fade show"
                                            role="alert">
                                            <div class="d-flex align-items-center text-warning ">
                                                <i class="ti ti-info-circle me-2 fs-4"></i>
                                                ⚠️ <?= session('warning') ?>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close" fdprocessedid="ed1z7v"></button>
                                        </div>
                                        <?php endif; ?>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Select File</label>
                                                    <div class="input-group flex-nowrap">

                                                        <div class="custom-file">
                                                            <!--<input type="file" class="form-control" id="inputGroupFile01">-->
                                                            <div class="upload-box border border-dashed p-4 text-center"
                                                                id="csvDropArea">
                                                                <p class="mb-2">Drag & Drop Pdfs file here</p>
                                                                <p class="small text-muted">or click below to select</p>
                                                                <input id="pdfUpload" type="file" class="form-control"
                                                                    name="pdfs[]" multiple
                                                                    accept=".pdf,application/pdf">
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


                                                <div class="btn-group">
                                                    <button type="submit"
                                                        class="btn btn-outline-success btn-sm shadow-sm"
                                                        aria-haspopup="true" aria-expanded="false"
                                                        fdprocessedid="sdx9js">
                                                        <i class="ti ti-upload me-1 fs-4"></i>
                                                        Extract & Upload
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
                    <div class="row">
                        <div class="col-lg-12">
                            <!--  start Info Table -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex mb-1 align-items-center">
                                        <div>
                                            <h4 class="card-title mb-0">Policy Information</h4>
                                        </div>

                                    </div>
                                    <br>
                                    <div class="table-responsive border rounded-2">
                                        <table class="table mb-0" id='resultsTable'>
                                            <thead class="table-info">
                                                <!-- start row -->
                                                <tr>
                                                    <th>Policy No.</th>
                                                    <th>Holder Name</th>
                                                    <th>Company</th>
                                                    <th>Vehicle No.</th>
                                                    <th>Insurance Type</th>
                                                    <th>Issue Date</th>
                                                    <th>Expiry</th>
                                                    <th>Download</th>
                                                </tr>
                                                <!-- end row -->
                                            </thead>
                                            <tbody>
                                                <?php foreach ($results as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['details']['policyNumber']) ?></td>
                                                    <td><?= htmlspecialchars($row['details']['holderName']) ?></td>
                                                    <td><?= htmlspecialchars($row['details']['companyName']) ?></td>
                                                    <td><?= htmlspecialchars($row['details']['vehicleNumber']) ?></td>
                                                    <td><?= htmlspecialchars($row['details']['insuranceType']) ?></td>
                                                    <td><?= htmlspecialchars($row['details']['policyStart']) ?></td>
                                                    <td><?= htmlspecialchars($row['details']['expiryDate']) ?></td>
                                                    <td><a class='download-icon'
                                                            href='<?= htmlspecialchars($row['path']) ?>' download>⬇️</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!--  end Info Table -->
                        </div>
                    </div>

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
    <script>
    function searchTable() {
        let input = document.getElementById("searchBox").value.toLowerCase();
        let rows = document.querySelectorAll("#resultsTable tbody tr");
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(input) ? "" : "none";
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        const fileInput = document.getElementById("pdfUpload");
        const fileList = document.getElementById("fileList");
        const uploadBox = document.querySelector(".upload-box");
        const uploadForm = document.getElementById("uploadForm");

        const validateAndDisplayFiles = () => {
            fileList.innerHTML = "";
            const validFiles = [];
            const invalidFiles = [];

            Array.from(fileInput.files).forEach(file => {
                if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                    invalidFiles.push(file.name);
                } else {
                    validFiles.push(file);
                    const item = document.createElement("div");
                    item.className = "file-item";
                    item.innerHTML = `<span class="file-icon">📄</span><span>${file.name}</span>`;
                    fileList.appendChild(item);
                }
            });

            if (invalidFiles.length > 0) {
                alert('❌ Invalid files detected (not PDF): ' + invalidFiles.join(', '));
            }

            // Update fileInput to only contain valid PDF files
            if (validFiles.length === 0 && Array.from(fileInput.files).length > 0) {
                fileInput.value = '';
            }
        };

        fileInput.addEventListener("change", validateAndDisplayFiles);

        uploadForm.addEventListener("submit", (e) => {
            if (fileInput.files.length === 0) {
                e.preventDefault();
                alert('⚠️ Please select at least one PDF file to upload.');
                return;
            }

            const invalidCount = Array.from(fileInput.files).filter(f =>
                f.type !== 'application/pdf' && !f.name.toLowerCase().endsWith('.pdf')
            ).length;

            if (invalidCount > 0) {
                e.preventDefault();
                alert('❌ Please remove non-PDF files before uploading.');
                return;
            }
        });

        uploadBox.addEventListener("dragover", (e) => {
            e.preventDefault();
            uploadBox.classList.add("dragover");
        });
        uploadBox.addEventListener("dragleave", () => {
            uploadBox.classList.remove("dragover");
        });
        uploadBox.addEventListener("drop", (e) => {
            e.preventDefault();
            uploadBox.classList.remove("dragover");
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event("change"));
        });
    });
    </script>

</body>

</html>