<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\DataModel;
use App\Models\EmployeeLoginHistoryModel;

class Auth extends BaseController
{
    public function loginForm()
    {
        return view('login'); // your login view
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->where('username', $username)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'User not found');
        }

        if ($employee['isActive'] == 0) {
            return redirect()->back()->with('error', 'Your account is inactive. Please contact admin.');
        }

        if ($employee && $password === $employee['password']) {
            $session = session();
            $session->set('employeeId', $employee['employeeId']);
            $session->set('employeeName', $employee['name']);
            $session->set('isLoggedIn', true);

           
            $historyModel = new EmployeeLoginHistoryModel();
            $historyModel->insert([
                'employeeId' => $employee['employeeId'],
                'status'     => 'LoggedIn'
            ]);

            if ($employee['jobTitle'] === 'Admin') {
                return redirect()->to('/admin');
            }

            $dataModel = new DataModel();
            $dataRecord = $dataModel->where([
                'telecaller' => $employee['employeeId'], // or nickName
                'actionTaken' => 0
            ])->first();



            if ($dataRecord) {
                return redirect()->to('/employee/dashboard/'.$dataRecord['recordId']);
            } else {
                return redirect()->to('/employee/dashboard');
            }
        }

        return redirect()->back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        $employeeId = session()->get('employeeId');
        //$historyModel = new EmployeeLoginHistoryModel();
        
         if ($employeeId) {
            $historyModel = new EmployeeLoginHistoryModel();

            // Get the latest login record with no logoutTime
            $lastLogin = $historyModel->where('employeeId', $employeeId)
                                    ->where('logoutTime', null)
                                    ->orderBy('loginTime', 'DESC')
                                    ->first();

            if ($lastLogin) {
                // Use raw CURRENT_TIMESTAMP so DB sets the time
                $db = \Config\Database::connect();
                $db->table('employeeloginhistory')
                ->where('id', $lastLogin['id'])
                ->set('logoutTime', 'CURRENT_TIMESTAMP', false) // false = don’t escape
                ->set('status', 'LoggedOut')
                ->update();
            }
        }

        session()->destroy();
        return redirect()->to('/employee/login')->with('success', 'Logged out successfully');
    }


}
