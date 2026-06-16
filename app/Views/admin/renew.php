<!DOCTYPE html>
<html>
<head>
    <title>Renew Subscription</title>
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
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card h2 { font-size: 26px; margin-top: 0; color: #0069d9; }
        .upload-box { background: #fff9e6; border: 2px dashed #ffc107; border-radius: 10px; padding: 30px; text-align: center; transition: background 0.3s, border-color 0.3s; }
        .upload-box.dragover { background: #fff2cc; border-color: #e0a800; }
        .upload-btn { display: inline-block; background: #ffc107; color: #212529; padding: 14px 28px; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; border: none; }
        .upload-btn:hover { background: #e0a800; }
        .file-list { margin-top: 15px; text-align: left; display: inline-block; width: 100%; }
        .file-item { display: flex; align-items: center; margin-bottom: 8px; font-size: 14px; color: #333; }
        .file-item span { margin-left: 10px; }
        .file-icon { color: #ff9800; font-size: 18px; }
        .image-preview { margin-top: 20px; display: block; width: 100%; max-width: 540px; margin-left: auto; margin-right: auto; text-align: center; }
        .image-preview img { max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.12); display: block; margin: 0 auto; }
        .image-preview p { margin-top: 10px; color: #555; font-size: 14px; }
        .status-banner { padding: 15px 18px; border-radius: 8px; margin-bottom: 20px; }
        .status-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .result-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .result-table th, .result-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .result-table th { background: #f0f2f5; }
        textarea { width: 100%; min-height: 160px; padding: 14px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa; resize: vertical; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="topbar">
        <div class="topbar-menu">
            <a href="<?= site_url('admin') ?>">🏠 Home</a>
            <a href="<?= site_url('admin/search') ?>">🔍 Search</a>
            <a href="<?= site_url('admin/expired-current') ?>">⏳ Current Month</a>
        </div>
        <button class="topbar-btn" onclick="window.location.href='<?= site_url('admin/upload') ?>';">➕ Add Policy</button>
    </div>
    <div class="container">
        <?php include 'sidebar.php'; ?>
        <div class="main">
            <div class="card">
                <h2>Renew Subscription</h2>
                <p style="color: #666; margin-bottom: 24px;">Upload a payment screenshot. The system will verify that the receiver is <strong>Vijay Kailas kumawat</strong> and the payment date is <strong>today</strong>.</p>

                <?php if (session()->getFlashdata('renewError')): ?>
                    <div class="status-banner status-error"><?= esc(session()->getFlashdata('renewError')) ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('renewStatus')): ?>
                    <div class="status-banner status-success"><?= esc(session()->getFlashdata('renewStatus')) ?></div>
                <?php endif; ?>

                <form action="<?= site_url('admin/renew') ?>" method="post" enctype="multipart/form-data" class="upload-box" id="renewForm">
                    <?= csrf_field() ?>
                    <label for="renewImage" class="upload-btn">📸 Choose Payment Screenshot</label>
                    <input id="renewImage" type="file" name="renew_image" accept="image/*">
                    <p class="drag-text">Only image files are accepted. Upload a screenshot of the payment receipt.</p>
                    <div id="renewFileList" class="file-list"></div>
                    <div id="renewPreview" class="image-preview" style="display:none;">
                        <img id="renewPreviewImage" src="" alt="Payment screenshot preview">
                        <p id="renewPreviewText">Selected screenshot preview</p>
                    </div>
                    <button type="submit" style="background:#0069d9; color:#fff; border:none; border-radius:6px; padding:12px 24px; margin-top:16px; cursor:pointer;">Upload & Verify</button>
                </form>

                <?php if (session()->getFlashdata('renewText')): ?>
                    <div style="margin-top: 30px;">
                        <h4>Extracted Data</h4>
                        <table class="result-table">
                            <tr><th>Receiver Name</th><td><?= esc(session()->getFlashdata('renewReceiver')) ?></td></tr>
                            <tr><th>Detected Date</th><td><?= esc(session()->getFlashdata('renewDate')) ?></td></tr>
                        </table>
                        <details style="margin-top:16px;">
                            <summary style="cursor:pointer; font-weight:600; color:#0069d9;">View Raw OCR Text</summary>
                            <textarea readonly><?= esc(session()->getFlashdata('renewText')) ?></textarea>
                        </details>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 30px; background: #f0f2f5; padding: 15px; border-radius: 8px;">
                    <p style="margin:0;"><strong>Tip:</strong> Make sure the screenshot clearly shows the receiver name and the payment date.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('renewImage')?.addEventListener('change', function() {
        const fileList = document.getElementById('renewFileList');
        const preview = document.getElementById('renewPreview');
        const previewImage = document.getElementById('renewPreviewImage');
        const previewText = document.getElementById('renewPreviewText');

        fileList.innerHTML = '';
        preview.style.display = 'none';
        previewImage.src = '';

        if (!this.files.length) return;
        const file = this.files[0];
        const item = document.createElement('div');
        item.className = 'file-item';
        item.innerHTML = `<span class='file-icon'>🖼️</span><span>${file.name}</span>`;
        fileList.appendChild(item);

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImage.src = event.target.result;
                previewText.textContent = file.name;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewText.textContent = 'Preview unavailable for this file type.';
            preview.style.display = 'block';
        }
    });
</script>
</body>
</html>
