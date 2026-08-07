<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background: #f2f2f2; }
    </style>
    <style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #333;
    font-size: 12px;
}

.header {
    background-color: #003366;
    color: #fff;
    padding: 10px 15px;
    text-align: center;
    font-size: 16px;
    font-weight: bold;
}

.summary {
    background-color: #f2f6fa;
    border: 1px solid #d0d7de;
    padding: 8px;
    margin-bottom: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th {
    background-color: #e6f0ff;
    color: #003366;
    font-weight: 600;
    border: 1px solid #ccc;
    padding: 6px;
    text-align: center;
}

td {
    border: 1px solid #ccc;
    padding: 6px;
    text-align: center;
}

tr:nth-child(even) {
    background-color: #f9fbfd;
}

.status-absent {
    color: red;
    font-weight: 600;
}

.status-weeklyoff {
    color: #007bff;
    font-weight: 600;
}

.status-holiday {
    color: #28a745;
    font-weight: 600;
}
</style>
<style>
.report-header-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
    font-family: Arial, sans-serif;
    font-size: 12px;
}

.report-header-table th {
    background-color: #003366;
    color: #fff;
    padding: 8px;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
}

.report-header-table td {
    border: 1px solid #ccc;
    padding: 6px;
    text-align: center;
}
</style>
</head>
<body>
<table class="report-header-table">
    <tr>
        <th colspan="6">Employee Attendance + Salary Detail Report</th>
    </tr>
    <tr>
        <td colspan="6" style="text-align: left; font-size: 14px;"> 
            <?php 
                $gender = strtolower(trim($report['employee']['gender'] ?? ''));
                $salutation = ($gender === 'male') ? 'MR.' : 'MS.';
                                                
            ?>
            <?= $salutation ?> <?= $report['employee']['name'] ?>
        </td>
    </tr>
    <tr>
        <td><strong>Emp No.</strong></td>
        <td><?= $report['employee']['employeeId'] ?></td>
        <td><strong>Designation</strong></td>
        <td><?= $report['employee']['jobTitle'] ?></td>
        <td><strong>Period</strong></td>
        <td><?= $period[0] ?> - <?= $period[1] ?></td>
        
    </tr>
    <tr>
        <td><strong>Present</strong></td>
        <td style="color:green;"><?= $report['presentDays'] ?></td>
        <td><strong>Absent</strong></td>
        <td style="color:red;"><?= $report['absentDays'] ?></td>
        <td><strong>Weekly Off</strong></td>
        <td><?= $report['weeklyOffDays'] ?></td>
    </tr>
    
<tr>
    <td><strong>Payable</strong></td>
    <td> <?= number_format($report['totalPayable'], 2) ?></td>
    <td><strong>Bonus</strong></td>
    <td> <?= number_format($report['bonus'], 2) ?></td>
    <td><strong>Deductions</strong></td>
    <td> <?= number_format($report['deductions'], 2) ?></td>
</tr>
    <!--
   
    <tr>
        <td><strong>Previous Balance</strong></td>
        <td> &#8377;<?= number_format($report['previousSalary'], 2) ?></td>
        <td colspan="2"><strong>Final Salary</strong></td>
        <td colspan="2"><strong>₹<?= number_format($report['finalSalary'], 2) ?></strong></td>
    </tr>
-->
</table>
<br>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Date</th>
            <th>Status</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Duration</th>
            <th>Remarks</th>
            <th>Payable</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($report['attendance'] as $record): ?>
            <?php
                $style = '';
                if ($record['status'] === 'Absent') {
                    $style = 'color:red;';
                } elseif ($record['status'] === 'Weekly Off') {
                    $style = 'color:blue;';
                }

                $duration = $record['duration'] ?? '-';
            ?>
            <tr class="status-<?= strtolower(str_replace(' ', '', $record['status'])) ?>">
                <td><?= $record['attendance_date'] ?></td>
                <td><?= $record['status'] ?></td>
                <td><?= $record['check_in_time'] ?></td>
                <td><?= $record['check_out_time'] ?></td>
                <td><?= $duration ?></td>
                <td><?= $record['remarks'] ?></td>
                <td><?= number_format($record['payable'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
