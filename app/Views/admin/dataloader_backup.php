<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Flexy Free Bootstrap Admin Template by WrapPixel</title>
  <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('/assets/css/toast.css') ?>" />
  <style>  .btn-excel { background-color: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .btn-excel:hover { background-color: #218838; }
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
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ti ti-bell"></i>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
              <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1">
                <div class="message-body">
                  <a href="javascript:void(0)" class="dropdown-item">
                    Item 1
                  </a>
                  <a href="javascript:void(0)" class="dropdown-item">
                    Item 2
                  </a>
                </div>
              </div>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              
              <li class="nav-item dropdown">
                <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <div class="d-flex align-items-center gap-2 border-start ps-3">
                      
                  <div class="user-profile-img">
                          <img src="<?= base_url('/assets/images/profile/user-1.jpg') ?>" class="rounded-circle" width="35" height="35" alt="flexy-img">
                  </div>
                  <div class="d-none d-md-flex align-items-center">
                          <h5 class="mb-0 fs-4">Hi,</h5>
                          <h5 class="mb-0 fs-4 fw-semibold ms-1">Johnathan</h5>
                          <i class="ti ti-chevron-down"></i>
                  </div>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-mail fs-6"></i>
                      <p class="mb-0 fs-3">My Account</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-list-check fs-6"></i>
                      <p class="mb-0 fs-3">My Task</p>
                    </a>
                    <a href="./authentication-login.html" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                  </div>
                </div>
              </li> 

              
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->
      <div class="body-wrapper-inner">
        <div class="container-fluid">
          <div class="row">
            
            
           
            <div class="col-12">
              <!-- start Event Registration -->
              <div class="card">
                <form action="<?= site_url('admin/upload-data') ?>" method="post" enctype="multipart/form-data" class="upload-box" id="uploadForm">
                  <div class="card-body">
                    <h4 class="card-title">Data Loader</h4>
                    <p class="card-subtitle mb-3">CSV files only • Drag & Drop here • Duplicate policies will be skipped</p>
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
                        <div id="alertBox"></div>    
                    <div class="row">
                      <div class="col-12">
                        <div class="mb-3">
                          <label class="form-label">Select File</label>
                          <div class="input-group flex-nowrap">
                            
                            <div class="custom-file">
                              <!--<input type="file" class="form-control" id="inputGroupFile01">-->
                                <input id="csvUpload" type="file" class="form-control" name="csvFile" accept=".csv,text/csv" required>    
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
                            <button type="button" onclick="removeAllData()" class="btn btn-outline-danger btn-sm shadow-sm">
                                <i class="ti ti-trash me-2"></i>
                                Remove all Data
                            </button>
                        </div>        
                        <div class="ms-auto">
                            <button type="submit"  class="btn btn-outline-success btn-sm shadow-sm">
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
        const fileInput = document.getElementById("csvUpload");
        const fileList = document.getElementById("fileList");
        const uploadBox = document.querySelector(".upload-box");
        const uploadForm = document.getElementById("uploadForm");

        const validateAndDisplayFiles = () => {
            fileList.innerHTML = "";
            const validFiles = [];
            const invalidFiles = [];

            Array.from(fileInput.files).forEach(file => {
                if (file.type !== 'text/csv' && !file.name.toLowerCase().endsWith('.csv')) {
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
                alert('❌ Invalid files detected (not CSV): ' + invalidFiles.join(', '));
            }

            // Update fileInput to only contain valid CSV files
            if (validFiles.length === 0 && Array.from(fileInput.files).length > 0) {
                fileInput.value = '';
            }
        };

        fileInput.addEventListener("change", validateAndDisplayFiles);

        uploadForm.addEventListener("submit", (e) => {
            if (fileInput.files.length === 0) {
                e.preventDefault();
                alert('⚠️ Please select at least one CSV file to upload.');
                return;
            }

            const invalidCount = Array.from(fileInput.files).filter(f => 
                f.type !== 'text/csv' && !f.name.toLowerCase().endsWith('.csv')
            ).length;

            if (invalidCount > 0) {
                e.preventDefault();
                alert('❌ Please remove non-CSV files before uploading.');
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

    function removeAllData() {
    if (confirm('Are you sure you want to remove all data? This action cannot be undone.')) {
        fetch('<?= site_url('admin/remove-all-data') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const alertBox = document.querySelector('#alertBox');
            if (data.success) {
                alertBox.innerHTML = `
                    <div class="alert bg-success-subtle text-success alert-dismissible fade show mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-circle-check me-2 fs-4"></i>
                        ${data.message}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                     // Auto-dismiss after 4 seconds
                    setTimeout(() => {
                        const alertEl = alertBox.querySelector('.alert');
                        if (alertEl) {
                            alertEl.classList.remove('show'); // Bootstrap fade out
                        }
                    }, 5000);
            } else {
                alertBox.innerHTML = `
                    <div class="alert bg-warning-subtle text-warning alert-dismissible fade show mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-triangle me-2 fs-4"></i>
                        ${data.message}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                             // Auto-dismiss after 4 seconds
                    setTimeout(() => {
                        const alertEl = alertBox.querySelector('.alert');
                        if (alertEl) {
                            alertEl.classList.remove('show'); // Bootstrap fade out
                        }
                    }, 5000);
            }
            
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector('#alertBox').innerHTML = `
                <div class="alert bg-danger-subtle text-danger alert-dismissible fade show mb-3" role="alert">
                  <div class="d-flex align-items-center">
                    <i class="ti ti-alert-circle me-2 fs-4"></i>
                    ❌ An error occurred while removing data.
                  </div>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
        });
    }
}


</script>

</body>

</html>