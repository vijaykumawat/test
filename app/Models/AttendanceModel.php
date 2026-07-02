<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table      = 'attendance';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'employee_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'status',
        'remarks',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'employee_id' => 'required|alpha_numeric',
        'attendance_date' => 'required|valid_date',
        'check_in_time'   => 'permit_empty|regex_match[/^(?:[01]\d|2[0-3]):[0-5]\d$/]',
        'check_out_time'  => 'permit_empty|regex_match[/^(?:[01]\d|2[0-3]):[0-5]\d$/]',
        'status'          => 'required|in_list[Present,Absent,Half Day,Leave]',
        'remarks'         => 'permit_empty|string',
    ];

   
    protected $validationMessages = [
        'employee_id' => [
            'required'      => 'Employee is required',
            'integer'       => 'Invalid employee selected',
            'greater_than'  => 'Invalid employee selected',
        ],
        'attendance_date' => [
            'required'      => 'Attendance date is required',
            'valid_date'    => 'Invalid date format',
        ],
        'check_in_time' => [
            'regex_match'   => 'Invalid check-in time format',
        ],
        'check_out_time' => [
            'regex_match'   => 'Invalid check-out time format',
        ],
        'status' => [
            'required'      => 'Status is required',
            'in_list'       => 'Invalid status selected',
        ],
    ];

    /**
     * Check if attendance already exists for employee and date
     */
    public function attendanceExists($employeeId, $date)
    {
        return $this->where('employee_id', $employeeId)
                    ->where('attendance_date', $date)
                    ->first() !== null;
    }

    /**
     * Get attendance for a specific employee and date
     */
    public function getAttendance($employeeId, $date)
    {
        return $this->where('employee_id', $employeeId)
                    ->where('attendance_date', $date)
                    ->first();
    }

    /**
     * Get all attendance records for a specific employee
     */
    public function getEmployeeAttendance($employeeId)
    {
        return $this->where('employee_id', $employeeId)
                    ->orderBy('attendance_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get attendance for a date range
     */
    public function getAttendanceByDateRange($startDate, $endDate, $employeeId = null)
    {
        $builder = $this->select('attendance.*, employee.name as employee_name, employee.employeeId')
                        ->join('employee', 'employee.employeeId = attendance.employee_id', 'left')
                        ->where('attendance_date >=', $startDate)
                        ->where('attendance_date <=', $endDate);
        
        if ($employeeId) {
            $builder->where('attendance.employee_id', $employeeId);
        }

        return $builder->orderBy('attendance_date', 'DESC')
                      ->orderBy('employee.name', 'ASC')
                      ->findAll();
    }

    /**
     * Get attendance by status
     */
    public function getAttendanceByStatus($status, $startDate = null, $endDate = null)
    {
        $builder = $this->select('attendance.*, employee.name as employee_name')
                        ->join('employee', 'employee.employeeId = attendance.employee_id', 'left')
                        ->where('attendance.status', $status);
        
        if ($startDate && $endDate) {
            $builder->where('attendance_date >=', $startDate)
                    ->where('attendance_date <=', $endDate);
        }

        return $builder->orderBy('attendance_date', 'DESC')
                      ->findAll();
    }

    /**
     * Get monthly attendance summary for employee
     */
    public function getMonthlyAttendance($employeeId, $month, $year)
    {
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        return $this->where('employee_id', $employeeId)
                    ->where('attendance_date >=', $startDate)
                    ->where('attendance_date <=', $endDate)
                    ->orderBy('attendance_date', 'ASC')
                    ->findAll();
    }

    /**
     * Get monthly attendance counts for employee
     */
    public function getMonthlyAttendanceSummary($employeeId, $month, $year)
    {
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $result = $this->select('status, COUNT(*) as count')
                       ->where('employee_id', $employeeId)
                       ->where('attendance_date >=', $startDate)
                       ->where('attendance_date <=', $endDate)
                       ->groupBy('status')
                       ->findAll();

        $summary = [
            'Present'   => 0,
            'Absent'    => 0,
            'Half Day'  => 0,
            'Leave'     => 0,
        ];

        foreach ($result as $row) {
            $summary[$row['status']] = $row['count'];
        }

        return $summary;
    }

    /**
     * Get today's attendance statistics
     */
    public function getTodayAttendanceStats()
    {
        $today = date('Y-m-d');

        return [
            'present' => $this->where('attendance_date', $today)
                             ->where('status', 'Present')
                             ->countAllResults(),
            'absent'  => $this->where('attendance_date', $today)
                             ->where('status', 'Absent')
                             ->countAllResults(),
            'leave'   => $this->where('attendance_date', $today)
                             ->where('status', 'Leave')
                             ->countAllResults(),
            'half_day' => $this->where('attendance_date', $today)
                              ->where('status', 'Half Day')
                              ->countAllResults(),
        ];
    }

    /**
     * Bulk save attendance records
     */
    public function bulkInsertAttendance($attendanceRecords)
    {
        $savedCount = 0;
        $skippedCount = 0;

        foreach ($attendanceRecords as $record) {
            if (!$this->attendanceExists($record['employee_id'], $record['attendance_date'])) {
                if ($this->insert($record)) {
                    $savedCount++;
                }
            } else {
                $skippedCount++;
            }
        }

        return [
            'saved'   => $savedCount,
            'skipped' => $skippedCount,
        ];
    }

    /**
     * Validate check-in and check-out times
     */
    public function validateTimes($checkInTime, $checkOutTime = null)
    {
        if (!$checkInTime && !$checkOutTime) {
            return true; // Both can be empty for absent status
        }

        if ($checkInTime && $checkOutTime) {
            $checkIn = strtotime($checkInTime);
            $checkOut = strtotime($checkOutTime);

            if ($checkOut <= $checkIn) {
                return false; // Check-out must be after check-in
            }
        }

        return true;
    }
}
