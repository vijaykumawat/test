<?php

namespace App\Controllers;
use App\Models\EmployeeModel;
use App\Models\AttendanceModel;

class EmployeeReportController extends BaseController
{
    public function generate($employeeId, $startDate, $endDate)
    {
        $employeeModel   = new EmployeeModel();
        $attendanceModel = new AttendanceModel();

        // Employee details
        $employee = $employeeModel->find($employeeId);

        // Attendance records
        $attendanceRecords = $attendanceModel
            ->where('employee_id', $employeeId)
            ->where('attendance_date >=', $startDate)
            ->where('attendance_date <=', $endDate)
            ->orderBy('attendance_date', 'ASC')
            ->findAll();

        // Salary calculations
        $monthlySalary = $employee['salary'];
        $daysInMonth   = date('t', strtotime($startDate));
        $salaryPerDay  = $monthlySalary / $daysInMonth;
        $salaryPerHour = $salaryPerDay / 8;

        $presentDays = $absentDays = $halfDays = $leaveDays = 0;
        $totalPayable = 0;

        foreach ($attendanceRecords as &$record) {
            switch ($record['status']) {
                case 'Present':
                    $presentDays++;
                    $record['payable'] = $salaryPerDay;
                    break;
                case 'Absent':
                    $absentDays++;
                    $record['payable'] = 0;
                    break;
                case 'Half Day':
                    $halfDays++;
                    $record['payable'] = $salaryPerDay / 2;
                    break;
                case 'Leave':
                    $leaveDays++;
                    $record['payable'] = $salaryPerDay; // paid leave
                    break;
            }
            $totalPayable += $record['payable'];
        }

        // Extra components (example values, can be fetched from DB)
        $bonus          = 0;      // allowances/bonus
        $deductions     = 1000;   // deductions (PF, TDS, etc.)
        $previousSalary = 8673;   // opening balance from last month
        $advanceLoan    = 0;      // loan/advance adjustments

        // Final salary calculation
        $finalSalary = ($totalPayable + $bonus + $previousSalary) - ($deductions + $advanceLoan);

        $data = [
            'employee'       => $employee,
            'attendance'     => $attendanceRecords,
            'period'         => [$startDate, $endDate],
            'salaryPerDay'   => $salaryPerDay,
            'salaryPerHour'  => $salaryPerHour,
            'presentDays'    => $presentDays,
            'absentDays'     => $absentDays,
            'halfDays'       => $halfDays,
            'leaveDays'      => $leaveDays,
            'totalPayable'   => $totalPayable,
            'bonus'          => $bonus,
            'deductions'     => $deductions,
            'previousSalary' => $previousSalary,
            'advanceLoan'    => $advanceLoan,
            'finalSalary'    => $finalSalary,
        ];

        return view('reports/employee_report', $data);
    }
}
