<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; margin: 0; background-color: #f5f7fa; color: #333; }
        .wrapper { display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { background: #ffffff; border-bottom: 1px solid #ddd; padding: 14px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .topbar-menu { display: flex; gap: 30px; }
        .topbar-menu a { text-decoration: none; color: #333; font-weight: 500; font-size: 14px; transition: color 0.3s; }
        .topbar-menu a:hover { color: #0069d9; }
        .topbar-btn { background: #0069d9; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .container { display: flex; flex: 1; }
        .sidebar { width: 240px; background: #e8e8e8; padding-top: 20px; box-shadow: 2px 0 6px rgba(0,0,0,0.1); }
        .sidebar h2 { text-align: center; font-size: 18px; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 14px 22px; color: #333; text-decoration: none; font-weight: 500; transition: background 0.3s; }
        .sidebar a:hover { background: #d0d0d0; color: #0069d9; }
        .sidebar a.active { background: #0069d9; color: #fff; }
        .main { flex: 1; padding: 40px; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h3 { margin-top: 0; color: #0069d9; }
        .btn { padding: 8px 16px; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 10px; }
        .btn-primary { background: #0069d9; color: #fff; }
        .btn-primary:hover { background: #0053aa; }
        .btn-info { background: #17a2b8; color: #fff; }
        .btn-info:hover { background: #138496; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="topbar">
        <div class="topbar-menu">
            <a href="<?= site_url('admin') ?>">🏠 Home</a>
            <a href="<?= site_url('admin/search') ?>">🔍 Search</a>
        </div>
        <button class="topbar-btn" onclick="window.location.href='<?= site_url('admin/upload') ?>';">➕ Add Policy</button>
    </div>
    <div class="container">
       <?php include 'sidebar.php'; ?>
        <div class="main">
            <h1>Welcome to Admin Dashboard</h1>
            <p>Manage insurance policies and subscriptions.</p>
            
            <div class="card">
                <h3>Quick Actions</h3>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="<?= site_url('admin/upload') ?>" class="btn btn-primary">📤 Upload New Policy</a>
                    <a href="<?= site_url('admin/search') ?>" class="btn btn-info">🔍 Search Policies</a>
                    <a href="<?= site_url('admin/expired-current') ?>" class="btn btn-warning">⏳ View Expiring</a>
                </div>
            </div>

            <div class="card">
                <h3>Features</h3>
                <ul>
                    <li><strong>Upload Policy PDFs</strong> - Extract policy details automatically from PDF files</li>
                    <li><strong>Search Policies</strong> - Search across all policies with pagination support</li>
                    <li><strong>Expiry Tracking</strong> - View policies expiring this month and next month</li>
                    <li><strong>Excel Export</strong> - Download policy lists as Excel files</li>
                    <li><strong>OCR Support</strong> - Extract text from transaction receipts and documents</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</body>
</html>
