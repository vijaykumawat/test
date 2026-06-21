<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\EmployeeLoginHistoryModel;

class SessionExpireFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // If user is not logged in, check if we need to auto-log logout
        if (! session()->get('isLoggedIn')) {
            $employeeId = session()->get('employeeId');

            if ($employeeId) {
                $historyModel = new EmployeeLoginHistoryModel();

                // Find last login record without logoutTime
                $lastLogin = $historyModel->where('employeeId', $employeeId)
                                          ->where('logoutTime', null)
                                          ->orderBy('loginTime', 'DESC')
                                          ->first();

                if ($lastLogin) {
                    $historyModel->update($lastLogin['id'], [
                        'logoutTime' => date('Y-m-d H:i:s'),
                        'status'     => 'AutoLoggedOut'
                    ]);
                }
            }

            // Redirect to login page with flash message
            return redirect()->to('/employee/login')
                             ->with('warning', 'Session expired, please log in again.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed after response
    }
}
