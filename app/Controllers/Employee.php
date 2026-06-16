<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\EmployeeLoginHistoryModel;

class Employee extends BaseController
{
    public function dashboard()
    {
        // Check if employee is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/employee/login')->with('error', 'Please log in to access the dashboard');
        }

        $employeeId = session()->get('employeeId');
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->find($employeeId);

        return view('employee/dashboard', ['employee' => $employee]);
    }

}