<?php

use App\Models\AttendanceModel;

if (!function_exists('markAttendance')) {
    function markAttendance($employeeId)
    {
         date_default_timezone_set('Asia/Kolkata'); 
        $attendanceModel = new AttendanceModel();
        $today = date('Y-m-d');

        // Check if already marked today
        $existing = $attendanceModel
            ->where('employee_id', $employeeId)
            ->where('attendance_date', $today)
            ->first();
        

        if (!$existing) {

            $attendanceModel->insert([
                'employee_id'     => $employeeId,
                'attendance_date' => $today,
                'check_in_time'   => date('H:i'),
                'status'          => 'Present',
                'remarks'         => 'Auto check-in on login',
            ]);
        }

    }
}

if (!function_exists('markCheckout')) {
    function markCheckout($employeeId)
    {

        date_default_timezone_set('Asia/Kolkata'); // ensure IST

        $attendanceModel = new AttendanceModel();
        $today = date('Y-m-d');

        // Find today's record
        $record = $attendanceModel
            ->where('employee_id', $employeeId)
            ->where('attendance_date', $today)
            ->first();

        if ($record) {
            $attendanceModel->update($record['id'], [
                'check_out_time' => date('H:i'),   // ✅ matches regex
                'remarks'        => 'Auto check-out on logout',
            ]);
        }
        
    }
}

