<?php
/**
 * PRODUCTION DIAGNOSTIC TOOL - DELETE THIS FILE AFTER USE!
 *
 * WHERE TO PLACE THIS FILE:
 *   Option A: project root  (folder containing "app/" and "system/")
 *   Option B: public/ folder (same folder as index.php)
 *
 * Then open in browser, e.g.:  https://gbinsurances.com/crm/check_production.php
 *
 * It shows the exact PHP error causing the blank page and verifies the
 * 4 edited files were uploaded completely.
 *
 * IMPORTANT: Delete this file from the server once done (security risk).
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// ---- Auto-detect project root (works from project root OR public folder) ----
$base = __DIR__;
if (! is_dir($base . '/app') && is_dir($base . '/../app')) {
    $base = dirname($base); // we are inside public/, go one level up
}

echo '<h2>Production Diagnostic</h2>';
echo '<p>PHP version: <strong>' . PHP_VERSION . '</strong> (CodeIgniter 4.7 needs PHP 8.1+)</p>';
echo '<p>Detected project root: <code>' . htmlspecialchars($base) . '</code></p>';

if (! is_dir($base . '/app')) {
    echo '<p style="color:red"><strong>Could not find the "app" folder near this file.
          Place this file in the project root or inside the public/ folder.</strong></p>';
    exit;
}

$files = [
    'app/Config/Routes.php',
    'app/Controllers/Admin.php',
    'app/Models/DataModel.php',
    'app/Views/admin/all_data.php',
];

$checks = [
    'app/Controllers/Admin.php' => ['updateRecord', 'deleteRecord', 'deleteRecords'],
    'app/Models/DataModel.php'  => ['telecallerId'],
    'app/Views/admin/all_data.php' => ['editRecordModal', 'deleteRecordModal', 'bulkDeleteModal', 'columnSettingsModal'],
    'app/Config/Routes.php'     => ['update-record', 'delete-record', 'delete-records'],
];

$allOk = true;

foreach ($files as $file) {
    $fullPath = $base . '/' . $file;
    echo '<hr><h3>' . htmlspecialchars($file) . '</h3>';

    if (! file_exists($fullPath)) {
        echo '<p style="color:red"><strong>MISSING FILE!</strong> Upload it.</p>';
        $allOk = false;
        continue;
    }

    $content = file_get_contents($fullPath);

    // 1) Feature checks — is the uploaded file the NEW version?
    if (isset($checks[$file])) {
        foreach ($checks[$file] as $needle) {
            $found = strpos($content, $needle) !== false;
            if (! $found) {
                $allOk = false;
            }
            echo ($found
                ? '<p style="color:green;margin:2px 0">OK: contains "' . htmlspecialchars($needle) . '"</p>'
                : '<p style="color:red;margin:2px 0"><strong>OUTDATED FILE: missing "' . htmlspecialchars($needle) . '" — re-upload this file!</strong></p>');
        }
    }

    // 2) Syntax check via php -l (works if PHP CLI is available on hosting)
    $tmp = tempnam(sys_get_temp_dir(), 'syn');
    file_put_contents($tmp, $content);
    $out = [];
    @exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($tmp) . ' 2>&1', $out, $code);
    unlink($tmp);
    if (! empty($out)) {
        $line = implode("\n", $out);
        echo '<pre style="margin:4px 0">' . htmlspecialchars($line) . '</pre>';
        if ($code !== 0) {
            $allOk = false;
        }
    }
}

// 3) Latest CodeIgniter log entry (often holds the real fatal error)
echo '<hr><h3>Latest log entries (writable/logs)</h3>';
$logDir = $base . '/writable/logs';
$latestLog = '';
if (is_dir($logDir)) {
    foreach (glob($logDir . '/log-*.php') ?: [] as $f) {
        if ($f !== $logDir . '/index.html' && (! $latestLog || filemtime($f) > filemtime($latestLog))) {
            $latestLog = $f;
        }
    }
}
if ($latestLog !== '') {
    echo '<p>Log file: <code>' . htmlspecialchars(basename($latestLog)) . '</code></p>';
    $lines = file($latestLog, FILE_IGNORE_NEW_LINES);
    $tail = array_slice($lines, -30);
    echo '<pre style="background:#f6f6f6;padding:8px;max-height:400px;overflow:auto">' 
       . htmlspecialchars(implode("\n", $tail)) . '</pre>';
} else {
    echo '<p>No log files found in writable/logs (logging may be disabled, or folder not writable).</p>';
}

echo '<hr>';
echo $allOk
    ? '<p style="color:green"><strong>All 4 files look correct and up to date.</strong> 
       If the page is still blank, check the log entries above — the fatal error will be there.
       Also try clearing OPcache (restart PHP / ask hosting) and clear writable/cache.</p>'
    : '<p style="color:red"><strong>Some files are missing/outdated — re-upload the flagged files and re-run this page.</strong></p>';

echo '<p style="color:red"><strong>DELETE THIS FILE NOW THAT YOU HAVE THE ANSWER.</strong></p>';