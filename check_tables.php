<?php
require 'vendor/autoload.php';

// Bootstrap CodeIgniter
require 'system/bootstrap.php';

$db = \Config\Database::connect();

// Show all tables
$result = $db->query('SHOW TABLES');
echo "Tables:\n";
foreach ($result->getResult() as $row) {
    echo "  - " . implode(', ', (array)$row) . "\n";
}

// Check employee table structure
echo "\nEmployee table structure:\n";
$result = $db->query('DESC employee');
foreach ($result->getResult() as $row) {
    echo $row->Field . " | " . $row->Type . "\n";
}
