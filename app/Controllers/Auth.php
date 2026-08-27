<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\DataModel;
//use App\Models\EmployeeLoginHistoryModel;
use App\Models\EmployeeSubscriptionModel;
use CodeIgniter\I18n\Time;
use DateTime;
use Config\SuperUser;
use App\Services\SubscriptionService;

class Auth extends BaseController
{
    protected $dataModel;
    protected $employeeModel;
    //protected $employeeLoginHistoryModel;
    protected $subscriptionModel;
    protected $subscriptionService;
    public function __construct()
    {
        $this->dataModel = new DataModel();
        $this->employeeModel = new EmployeeModel();
        //$this->employeeLoginHistoryModel = new EmployeeLoginHistoryModel();
        $this->subscriptionModel = new EmployeeSubscriptionModel();
        $this->subscriptionService = new SubscriptionService();
    }

    public function loginForm()
    {
        return view('login'); // your login view
    }

    public function login()
    {
        $session = session();
        helper('common_helper'); 
        // 🚫 Prevent re-login if already logged in
        if ($session->get('isLoggedIn')) {
            if ($session->get('jobTitle') === 'SuperAdmin') {
                return redirect()->to('/superadmin/dashboard')->with('info', 'You are already logged in.');
            }
            if ($session->get('jobTitle') === 'Admin') {
                return redirect()->to('/admin')->with('info', 'You are already logged in.');
            }
            return redirect()->to('/employee/dashboard')->with('info', 'You are already logged in.');
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

         // ✅ Step 1: Check SuperUser credentials from config
        $superUser = new SuperUser();
        if ($username === $superUser->username && $password === $superUser->password) {
            $session->set([
                'employeeId'   => 0,
                'employeeName' => 'Super User',
                'mobile'       => '',
                'jobTitle'     => 'SuperAdmin',
                'gender'       => 'Male',
                'profilePhoto' => '',
                'isLoggedIn'   => true
            ]);
            $session->setTempdata('isLoggedIn', true, 36000);

            return redirect()->to('/superadmin/dashboard');
        }


        //$employeeModel = new EmployeeModel();
        $employee = $this->employeeModel->where('username', $username)->first();

        if (!$employee) {
            
            return redirect()->back()->with('error', 'User not found');
        }

        if ($employee['isActive'] == 0) {
            return redirect()->back()->with('error', 'Your account is inactive. Please contact admin.');
        }

        if ($employee && $password === $employee['password']) {
            
            // ✅ Check subscription
            $subscription = $this->subscriptionModel->where('employeeId', $employee['employeeId'])
                                            ->orderBy('endDate', 'DESC')
                                            ->first();

            date_default_timezone_set('Asia/Kolkata');
            $today = date('Y-m-d');

            if (!$subscription) {
                return redirect()->back()->with('error', 'No subscription found. Please contact admin.');
            }
            
            if ($subscription['endDate'] < $today ) {
                 $this->subscriptionModel->update($subscription['id'], [
                    'status' => 'Expired'
                ]);
                return redirect()->back()->with('error', 'Your subscription has expired.');
            }


            $session->set([
                'employeeId'   => $employee['employeeId'],
                'employeeName' => $employee['name'],
                'mobile'      =>  $employee['phoneNumber'],
                'jobTitle'     => $employee['jobTitle'], // <-- add this
                'gender'             => $employee['gender'],
                'profilePhoto'             => $employee['profilePhoto'],
                'loginTime'    => date('Y-m-d H:i:s'),
                'isLoggedIn'   => true
            ]);
            $session->setTempdata('isLoggedIn', true, 36000);
            /*
            $existingLogin = $this->employeeLoginHistoryModel
                ->where('employeeId', $employee['employeeId'])
                ->where('DATE(dateCreated)', $today)
                ->first();

            if (!$existingLogin) {
                $this->employeeLoginHistoryModel->insert([
                    'employeeId' => $employee['employeeId'],
                    'status'     => 'LoggedIn',
                    'dateCreated'=> date('Y-m-d H:i:s')
                ]);
            }
            */
            markAttendance($employee['employeeId']);

            // Check days left for subscription and set a one-time flash for modal display
            try {
                $endDateStr = $subscription['endDate'];
                $todayStr = date('Y-m-d');
                $daysLeft = (int) floor((strtotime($endDateStr) - strtotime($todayStr)) / 86400);

                if ($daysLeft <= 3) {
                    $modalType = ($daysLeft === 0) ? 'danger' : 'warning';
                    $session->setFlashdata('subscription_modal', [
                        'type' => $modalType,
                        'days' => $daysLeft
                    ]);
                }
            } catch (\Exception $e) {
                // swallow errors here; modal is non-critical
            }

            if ($employee['jobTitle'] === 'Admin') {
                return redirect()->to('/admin');
            }
            return redirect()->to('/employee/dashboard');

        }

        return redirect()->back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        helper('common_helper');
        $employeeId = session()->get('employeeId');
        
        if ($employeeId) {
            // Get the latest login record with no logoutTime
            /*
            $lastLogin = $this->employeeLoginHistoryModel
                ->where('employeeId', $employeeId)
                ->where('logoutTime IS NULL')   // ✅ cleaner raw condition
                ->orderBy('loginTime', 'DESC')
                ->first();

            if ($lastLogin) {
                // Update using model instead of raw DB connection
                $this->employeeLoginHistoryModel->update($lastLogin['id'], [
                    'logoutTime' => date('Y-m-d H:i:s'), // ✅ use PHP timestamp
                    'status'     => 'LoggedOut'
                ]);
            }
                */

            // ✅ mark checkout for attendance
            markCheckout($employeeId);
        }

        session()->destroy();
        return redirect()->to('/employee/login')->with('success', 'Logged out successfully');
    }


    public function register(){
        return view('registration');
    }


    public function employeeAdd()
    {
        //$employeeModel = new \App\Models\EmployeeModel();
        $db = \Config\Database::connect();

        $empid = $this->generateRecordId();

        $data = [
            'employeeId'       => $empid,
            'name'             => $this->request->getPost('name'),
            'dateOfBirth'      => $this->request->getPost('dateOfBirth'),
            'gender'           => $this->request->getPost('gender'),
            'phoneNumber'      => $this->request->getPost('phoneNumber'),
            'email'            => $this->request->getPost('email'),
            'username'         => $this->request->getPost('username'),
            'password'         => $this->request->getPost('password'),
            'profilePhoto'     => '',
            'hireDate'         => date('Y-m-d'),
            'jobTitle'         => 'Admin',
            'employmentStatus' => 'Active',
            'isActive'         => 1,
            'bonusEligible'    => 0,
            'bankAccountNumber'=> '',
            'workLocation'     => ''
        ];

        // Normalize DOB
        if (!empty($data['dateOfBirth'])) {
            $date = DateTime::createFromFormat('d/m/Y', $data['dateOfBirth']);
            $data['dateOfBirth'] = $date ? $date->format('Y-m-d') : $data['dateOfBirth'];
        }

        /* Handle profile image upload
        $file = $this->request->getFile('profile_img');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $targetPath = FCPATH . 'uploads/profile/';
            $file->move($targetPath, $newName);
            $employeeData['profilePhoto'] = $newName;
        } else {
            $employeeData['profilePhoto'] = null; // no file selected
        }
        */
        
        $db->transStart();

        if ($this->employeeModel->insert($data) === false) {
            $db->transRollback();
            return redirect()->to(site_url('register'))
                            ->with('error', 'Failed to add employee.');
        }

        $res = $this->subscriptionService->purchaseSubscription($this->request->getFile('paymentScreenshot'), $data);
        if (!$res['success']) {
            $db->transRollback();
            return redirect()->to(base_url('register'))
                 ->with('error', 'Subscription verification failed: ' . $res['message']);
        }
        
        $db->transComplete();
        

        return redirect()->to(site_url('employee/login'))
                        ->with('success', 'Employee added successfully');
    }

    
    /**
 * Generate a unique alphanumeric recordId (15–16 characters).
 */
    private function generateRecordId(): string
    {
        do {
            // Generate 12 hex characters (from random bytes) + 4 digits
            $randomHex = substr(bin2hex(random_bytes(8)), 0, 12); // already lowercase
            $randomNum = str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT); // 4 digits
            $id = substr($randomHex . $randomNum, 0, 16); // total length 15–16 chars
        } while ($this->employeeModel->where('employeeId', $id)->countAllResults() > 0);

        return $id;
    }



}
