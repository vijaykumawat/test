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

if (!function_exists('getCCRange')) {
    function getCCRange($cc)
    {
        if ($cc <= 1000) {
            $band = 'upto_1000';
        } elseif ($cc >= 1001 && $cc <= 1200) {
            $band = '1001-1200';
        } elseif ($cc >= 1201 && $cc <= 1500) {
            $band = '1201-1500';
        } else {
            $band = null; // outside defined range
        }
        return $band;
    }
}

if (!function_exists('getCCRangeForZeroDep')) {
    function getCCRangeForZeroDep($cc)
    {
        if ($cc <= 1000) {
            $band = 'upto_1000';
        } elseif ($cc >= 1001 && $cc <= 1500) {
            $band = '1001-1500';
        } elseif ($cc >= 1501 && $cc <= 2000) {
            $band = '1501-2000';
        } else {
            $band = null; // outside defined range
        }
        return $band;
    }
}

if (!function_exists('getAgeRange')) {
    function getAgeRange($age)
    {
        if ($age <= 1) {
            $ageBand = 'age_0_1';
        } elseif ($age <= 2) {
            $ageBand = 'age_1_2';
        } elseif ($age <= 3) {
            $ageBand = 'age_2_3';
        } elseif ($age <= 4) {
            $ageBand = 'age_3_4';
        } elseif ($age <= 5) {
            $ageBand = 'age_4_5';
        } else {
            $ageBand = null; // not eligible
        }
        return $ageBand;
    }
}