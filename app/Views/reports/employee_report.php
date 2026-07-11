<!DOCTYPE html>
<html>
<head>
    <title>Employee Report</title>
    <style>
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:6px; text-align:center; }
        th { background:#f2f2f2; }
    </style>
</head>
<body>
    <h2>Employee Attendance + Salary Report</h2>
    <p>Period: <?= $period[0] ?> - <?= $period[1] ?></p>
    <p>Employee: <?= $employee['employeeId'] ?> - <?= $employee['name'] ?> (<?= $employee['jobTitle'] ?>)</p>
    <p>Salary: ₹<?= $employee['salary'] ?> / Month</p>

    <h3>Summary</h3>
    <ul>
        <li>Present Days: <?= $presentDays ?></li>
        <li>Absent Days: <?= $absentDays ?></li>
        <li>Half Days: <?= $halfDays ?></li>
        <li>Leave Days: <?= $leaveDays ?></li>
        <li>Salary Per Day: ₹<?= number_format($salaryPerDay,2) ?></li>
        <li>Salary Per Hour: ₹<?= number_format($salaryPerHour,2) ?></li>
        <li>Total Payable: ₹<?= number_format($totalPayable,2) ?></li>
    </ul>

    <h3>Detailed Attendance</h3>
    <table>
        <tr>
            <th>Date</th>
            <th>Status</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Remarks</th>
            <th>Payable</th>
        </tr>
        <?php foreach($attendance as $row): ?>
        <tr>
            <td><?= $row['attendance_date'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['check_in_time'] ?></td>
            <td><?= $row['check_out_time'] ?></td>
            <td><?= $row['remarks'] ?></td>
            <td><?= number_format($row['payable'],2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <h3>Salary Summary</h3>
<ul>
    <li>Present Days: <?= $presentDays ?></li>
    <li>Absent Days: <?= $absentDays ?></li>
    <li>Half Days: <?= $halfDays ?></li>
    <li>Leave Days: <?= $leaveDays ?></li>
    <li>Salary Per Day: ₹<?= number_format($salaryPerDay,2) ?></li>
    <li>Salary Per Hour: ₹<?= number_format($salaryPerHour,2) ?></li>
    <li>Payable Salary (Attendance): ₹<?= number_format($totalPayable,2) ?></li>
    <li>Bonus & Allowance: ₹<?= number_format($bonus,2) ?></li>
    <li>Deductions: ₹<?= number_format($deductions,2) ?></li>
    <li>Advance/Loan: ₹<?= number_format($advanceLoan,2) ?></li>
    <li>Previous Month Balance: ₹<?= number_format($previousSalary,2) ?></li>
    <li><strong>Final Salary to Pay: ₹<?= number_format($finalSalary,2) ?></strong></li>
</ul>

</body>
</html>
