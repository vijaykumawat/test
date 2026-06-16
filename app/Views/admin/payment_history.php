<!DOCTYPE html>
<html>
<head>
    <title>Payment History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; margin: 0; background-color: #f5f7fa; color: #333; }
        .wrapper { display: flex; flex-direction: column; min-height: 100vh; width: 100%; }
        .topbar { background: #ffffff; border-bottom: 1px solid #ddd; padding: 14px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .topbar-menu { display: flex; gap: 30px; align-items: center; }
        .topbar-menu a { text-decoration: none; color: #333; font-weight: 500; font-size: 14px; transition: color 0.3s; }
        .topbar-menu a:hover { color: #0069d9; }
        .container { display: flex; flex: 1; width: 100%; max-width: none; padding: 0; margin: 0; }
        .sidebar { width: 240px; background: #e8e8e8; color: #333; min-height: 100vh; padding-top: 20px; box-shadow: 2px 0 6px rgba(0,0,0,0.1); }
        .sidebar a { display: block; padding: 14px 22px; color: #333; text-decoration: none; font-weight: 500; transition: background 0.3s, color 0.3s; }
        .sidebar a:hover { background: #d0d0d0; color: #0069d9; }
        .sidebar a.active { background: #0069d9; color: #fff; }
        .main { flex: 1; padding: 30px; }
        h2 { font-size: 22px; margin-bottom: 20px; font-weight: 600; }
        table { margin: 20px 0; border-collapse: collapse; width: 100%; background-color: #ffffff; border: 1px solid #ddd; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; font-size: 14px; }
        th { background-color: #f0f2f5; font-weight: 600; }
        tr:nth-child(even) td { background-color: #fafafa; }
        .badge { font-size: 13px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="topbar">
        <div class="topbar-menu">
            <a href="<?= site_url('admin') ?>">🏠 Home</a>
            <a href="<?= site_url('admin/upload') ?>">📊 Dashboard</a>
            <a href="<?= site_url('admin/search') ?>">🔍 Search</a>
            <a href="<?= site_url('admin/payment-history') ?>" class="active">💳 Payment History</a>
        </div>
    </div>
    <div class="container">
        <?php include 'sidebar.php'; ?>
        <div class="main">
            <h2>Payment History</h2>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Receiver Name</th>
                            <th>Created Date</th>
                            <th>Screenshot</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($subscriptions)): ?>
                            <?php foreach ($subscriptions as $index => $sub): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($sub['receiver_name']) ?></td>
                                    <td><?= esc($sub['created_date']) ?></td>
                                    <td>
    <a href="<?= base_url($sub['screenshot']) ?>" target="_blank">⬇️ View</a>
</td>
                                    <td>
                                        <?php if ($sub['status'] == 1): ?>
                                            <span class="badge bg-success">Valid</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Invalid</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="loading">No payment records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
