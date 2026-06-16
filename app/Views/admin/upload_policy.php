<!DOCTYPE html>
<html>
<head>
    <title>Upload Policy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; margin: 0; background-color: #f5f7fa; color: #333; }
        .wrapper { display: flex; flex-direction: column; min-height: 100vh; width: 100%; }
        .topbar { background: #ffffff; border-bottom: 1px solid #ddd; padding: 14px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .topbar-menu { display: flex; gap: 30px; align-items: center; }
        .topbar-menu a { text-decoration: none; color: #333; font-weight: 500; font-size: 14px; transition: color 0.3s; }
        .topbar-menu a:hover { color: #0069d9; }
        .topbar-btn { background: #0069d9; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer; }
        .container { display: flex; flex: 1; width: 100%; max-width: none; padding: 0; margin: 0; }
        .sidebar { width: 240px; background: #e8e8e8; color: #333; min-height: 100vh; padding-top: 20px; box-shadow: 2px 0 6px rgba(0,0,0,0.1); }
        .sidebar h2 { text-align: center; font-size: 18px; margin-bottom: 30px; color: #333; }
        .sidebar a { display: block; padding: 14px 22px; color: #333; text-decoration: none; font-weight: 500; transition: background 0.3s, color 0.3s; }
        .sidebar a:hover { background: #d0d0d0; color: #0069d9; }
        .sidebar a.active { background: #0069d9; color: #fff; }
        .main { flex: 1; padding: 30px; }
        h2 { font-size: 22px; margin-bottom: 20px; font-weight: 600; }
        .upload-box { background: #e9f2fb; border: 2px dashed #0069d9; border-radius: 10px; padding: 40px; text-align: center; margin-top: 20px; transition: background 0.3s, border-color 0.3s; cursor: pointer; }
        .upload-box.dragover { background: #d0ebff; border-color: #0053aa; }
        .upload-btn { display: inline-block; background: #0069d9; color: #fff; padding: 14px 28px; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; }
        .upload-box input[type="file"] { display: none; }
        .drag-text { color: #666; margin-top: 12px; font-size: 14px; }
        .file-list { margin-top: 15px; text-align: left; display: inline-block; }
        .file-item { display: flex; align-items: center; margin-bottom: 6px; font-size: 14px; color: #333; }
        .file-item span { margin-left: 8px; }
        .file-icon { color: #0069d9; font-size: 18px; }
        button[type="submit"] { background-color: #28a745; color: white; font-size: 14px; padding: 10px 20px; border: none; border-radius: 4px; margin-top: 20px; cursor: pointer; }
        button[type="submit"]:hover { background-color: #218838; }
        table { margin: 20px 0; border-collapse: collapse; width: 100%; background-color: #ffffff; border: 1px solid #ddd; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; font-size: 14px; }
        th { background-color: #f0f2f5; font-weight: 600; }
        tr:nth-child(even) td { background-color: #fafafa; }
        .download-icon { text-decoration: none; color: #0069d9; font-size: 16px; }
        .success-banner { background: #d4edda; color: #155724; padding: 12px 40px 12px 12px; border-radius: 6px; margin: 40px 0 20px 0; border: 1px solid #c3e6cb; position: relative; }
        .success-banner .close-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 18px; font-weight: bold; color: #155724; cursor: pointer; }
        .search-wrapper { position: relative; }
        .search-wrapper input { padding: 12px 40px 12px 16px; font-size: 14px; border: 2px solid #0069d9; border-radius: 8px; background-color: #ffffff; box-shadow: 0 2px 4px rgba(0, 105, 217, 0.1); }
        .search-wrapper input:focus { outline: none; border-color: #0053aa; box-shadow: 0 4px 12px rgba(0, 105, 217, 0.25); }
        .search-wrapper input::placeholder { color: #999; }
        .search-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #0069d9; font-size: 18px; pointer-events: none; }
        .error-banner { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin: 20px 0; border: 1px solid #f5c6cb; }
        .warning-banner { background: #fff3cd; color: #856404; padding: 12px; border-radius: 6px; margin: 20px 0; border: 1px solid #ffeeba; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="topbar">
        <div class="topbar-menu">
            <a href="<?= site_url('admin') ?>">🏠 Home</a>
            <a href="<?= site_url('admin/upload') ?>">📊 Dashboard</a>
            <a href="<?= site_url('admin/search') ?>">🔍 Search</a>
        </div>
        <button class="topbar-btn" onclick="document.getElementById('pdfUpload').click();">➕ Add Policy</button>
    </div>
    <div class="container">
        <?php include 'sidebar.php'; ?>
        <div class="main">
            <h2>Upload Policy PDFs</h2>
            
            <?php if (session()->has('error')): ?>
                <div class="error-banner">
                    <?= session('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->has('warning')): ?>
                <div class="warning-banner">
                    ⚠️ <?= session('warning') ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('admin/upload') ?>" method="post" enctype="multipart/form-data" class="upload-box" id="uploadForm">
                <?= csrf_field() ?>
                <label for="pdfUpload" class="upload-btn">� Select PDF Policy Files</label>
                <input id="pdfUpload" type="file" name="pdfs[]" multiple accept=".pdf,application/pdf">
                <p class="drag-text">PDF files only • Drag & Drop here • Duplicate policies will be skipped</p>
                <div id="fileList" class="file-list"></div>
                <button type="submit">Upload & Extract</button>
            </form>

            <?php if (!empty($results)): ?>
                <div class="success-banner" id="successBanner">
                    ✅ Upload complete. Extracted details below.
                    <button class="close-btn" onclick="document.getElementById('successBanner').style.display='none'">&times;</button>
                </div>
                <h2>Extracted Policy Details</h2>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div></div>
                    <div class="search-wrapper">
                        <input type='text' id='searchBox' onkeyup='searchTable()' placeholder='Search policies...'>
                        <span class="search-icon">🔍</span>
                    </div>
                </div>
                <table id='resultsTable'>
                    <thead>
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
                            <td><a class='download-icon' href='<?= htmlspecialchars($row['path']) ?>' download>⬇️</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

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
