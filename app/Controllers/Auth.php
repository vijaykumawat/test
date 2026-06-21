<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\DataModel;
use App\Models\EmployeeLoginHistoryModel;
use App\Models\EmployeeSubscriptionModel;
use CodeIgniter\I18n\Time;
use DateTime;

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
               $session->set([
                'employeeId'   => $employee['employeeId'],
                'employeeName' => $employee['name'],
                'jobTitle'     => $employee['jobTitle'], // <-- add this
                'gender'             => $employee['gender'],
                'profilePhoto'             => $employee['profilePhoto'],
                'isLoggedIn'   => true
            ]);
            $session->setTempdata('isLoggedIn', true, 3600);

            if ($employee['jobTitle'] === 'Admin') {
                
                return redirect()->to('/admin');
            }
            return redirect()->to('/employee/dashboard');

            /*
           
            $historyModel = new EmployeeLoginHistoryModel();
            $historyModel->insert([
                'employeeId' => $employee['employeeId'],
                'status'     => 'LoggedIn'
            ]);


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
                */
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

    public function register(){
        return view('registration');
    }


    public function employeeAdd()
    {
        $employeeModel = new \App\Models\EmployeeModel();
        $db = \Config\Database::connect();

        // Handle file upload
        $file = $this->request->getFile('paymentScreenshot');
        $photoName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $photoName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/profile', $photoName);
        }

        $empid = substr(bin2hex(random_bytes(8)), 0, 16);

        $data = [
            'employeeId'       => $empid,
            'name'             => $this->request->getPost('name'),
            'dateOfBirth'      => $this->request->getPost('dateOfBirth'),
            'gender'           => $this->request->getPost('gender'),
            'phoneNumber'      => $this->request->getPost('phoneNumber'),
            'email'            => $this->request->getPost('email'),
            'username'         => $this->request->getPost('username'),
            'password'         => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'profilePhoto'     => $photoName,
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

        $db->transStart();

        if ($employeeModel->insert($data) === false) {
            $db->transRollback();
            return redirect()->to(site_url('register'))
                            ->with('error', 'Failed to add employee.');
        }

        $res = $this->purchaseSubscription($file, $data);
        if (!$res['success']) {


            $db->transRollback();
return redirect()->to(base_url('register'))
                 ->with('error', 'Subscription verification failed: ' . $res['message']);
        }

        $db->transComplete();

        return redirect()->to(site_url('register'))
                        ->with('success', 'Employee added successfully');
    }


    private function purchaseSubscription($img, array $employeeData)
    {
        $image = $img;
        if (! $image || ! $image->isValid()) {
            return ['success' => false, 'message' => 'Please upload a valid Payment screenshot file.'];
        }

        $extension = strtolower($image->getClientExtension() ?: pathinfo($image->getClientName(), PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg','jpeg','png','webp','bmp','gif'];
        if (! in_array($extension, $allowedExtensions)) {
            return ['success' => false, 'message' => 'Only image files are allowed (jpg, png, webp, bmp, gif).'];
        }

        $uploadPath = FCPATH . 'uploads/receipts/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = $image->getRandomName();
        if (! $image->move($uploadPath, $newName)) {
            return ['success' => false, 'message' => 'Failed to save uploaded image.'];
        }

        $imagePath = $uploadPath . $newName;
        $ocrResult = $this->runOcrPython($imagePath);
        if (! empty($ocrResult['error'])) {
            return ['success' => false, 'message' => 'OCR failed: ' . $ocrResult['error']];
        }

        $validNames = [
            "Vijay Kailas Kumawat",
            "Vijey Kumawatt",
            "Vijay Kailash Kumawat",
            "Vijay Kumawat"
        ];

        $text          = trim($ocrResult['text'] ?? '');
        $receiverValid = $this->containsReceiverName($text, $validNames);
        $dateText      = $this->extractDateFromText($text);
        $dateValid     = $this->isTodayDate($dateText);

        // 🚫 Stop here if validation fails — no DB insert
        if (!$receiverValid || !$dateValid) {
            return [
                'success' => false,
                'message' => 'Wrong screenshot attached.',
            ];
        }

        // ✅ Only insert if validation passed
        $subscriptionModel = new EmployeeSubscriptionModel();
        $insertData = [
            'employeeId' => $employeeData['employeeId'],
            'startDate'  => date('Y-m-d'),
            'endDate'    => date('Y-m-d', strtotime('+1 month')),
            'status'     => 'Active',
            'amount'     => 100.00
        ];

        $subscriptionId = $subscriptionModel->insert($insertData);

        if ($subscriptionId === false) {
            return [
                'success' => false,
                'message' => 'Failed to insert subscription',
                'errors'  => $subscriptionModel->errors()
            ];
        }

        return [
            'success'        => true,
            'message'        => 'Payment screenshot verified successfully.',
            'receiver'       => 'Vijay Kailas kumawat',
            'date'           => $dateText ?: date('Y-m-d'),
            'subscriptionId' => $subscriptionId
        ];
    }


}
