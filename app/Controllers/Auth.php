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
    protected $dataModel;
    protected $employeeModel;
    protected $employeeLoginHistoryModel;
    protected $subscriptionModel;
    public function __construct()
    {
        $this->dataModel = new DataModel();
        $this->employeeModel = new EmployeeModel();
        $this->employeeLoginHistoryModel = new EmployeeLoginHistoryModel();
        $this->subscriptionModel = new EmployeeSubscriptionModel();
    }

    public function loginForm()
    {
        return view('login'); // your login view
    }

    public function login()
    {
        $session = session();
        // 🚫 Prevent re-login if already logged in
        if ($session->get('isLoggedIn')) {
            if ($session->get('jobTitle') === 'Admin') {
                return redirect()->to('/admin')->with('info', 'You are already logged in.');
            }
            return redirect()->to('/employee/dashboard')->with('info', 'You are already logged in.');
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        //$employeeModel = new EmployeeModel();
        $employee = $this->employeeModel->where('username', $username)->first();

        if (!$employee) {
            
            return redirect()->back()->with('error', 'User not found');
        }

        if ($employee['isActive'] == 0) {
            return redirect()->back()->with('error', 'Your account is inactive. Please contact admin.');
        }

        if ($employee && $password === $employee['password']) {
                $session->set([
                'employeeId'   => $employee['employeeId'],
                'employeeName' => $employee['name'],
                'jobTitle'     => $employee['jobTitle'], // <-- add this
                'gender'             => $employee['gender'],
                'profilePhoto'             => $employee['profilePhoto'],
                'isLoggedIn'   => true
            ]);
            $session->setTempdata('isLoggedIn', true, 3600);
            
            $this->employeeLoginHistoryModel->insert([
                'employeeId' => $employee['employeeId'],
                'status'     => 'LoggedIn'
            ]);

            if ($employee['jobTitle'] === 'Admin') {
                return redirect()->to('/admin');
            }
            return redirect()->to('/employee/dashboard');

        }

        return redirect()->back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        $employeeId = session()->get('employeeId');
        
         if ($employeeId) {
           
            // Get the latest login record with no logoutTime
            $lastLogin = $this->employeeLoginHistoryModel->where('employeeId', $employeeId)
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

        $res = $this->purchaseSubscription($this->request->getFile('paymentScreenshot'), $data);
        if (!$res['success']) {
            $db->transRollback();
            return redirect()->to(base_url('register'))
                 ->with('error', 'Subscription verification failed: ' . $res['message']);
        }
        
        $db->transComplete();
        

        return redirect()->to(site_url('employee/login'))
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
            "Vijay Kumawat",
            "Vijay",
            "MR VIJAY KAILAS KUMAWAT",
            "KUMAWAT",
            "Kumawat"
        ];

        $text          = trim($ocrResult['text'] ?? '');
        print_r("text::".$text);
        log_message('debug', 'Image extract data: ' . $text);
        $receiverValid = $this->containsReceiverName($text, $validNames);
        $dateText      = $this->extractDateFromText($text);
        $dateValid     = $this->isTodayDate($dateText);

        print_r("receiver valid::".$receiverValid);
        print_r("DateText::".$dateText);
        print_r("is date vlaid::".$dateValid);

        // 🚫 Stop here if validation fails — no DB insert
        if (!$receiverValid || !$dateValid) {
            echo "something not valid";
            exit;
            return [
                'success' => false,
                'message' => 'Wrong screenshot attached.',
            ];
        }

        // ✅ Only insert if validation passed
        
        $insertData = [
            'employeeId' => $employeeData['employeeId'],
            'startDate'  => date('Y-m-d'),
            'endDate'    => date('Y-m-d', strtotime('+1 month')),
            'status'     => 'Active',
            'amount'     => 100.00
        ];

        $subscriptionId = $this->subscriptionModel->insert($insertData);

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

    private function runOcrPython(string $imagePath): array
    {
        $rootPath = defined('ROOTPATH') ? ROOTPATH : realpath(APPPATH . '../') . DIRECTORY_SEPARATOR;
        $scriptPath = $rootPath . 'ocr.py';

        if (! file_exists($scriptPath)) {
            return ['error' => 'OCR script not found'];
        }

        $commands = ['python', 'python3', 'py -3'];
        foreach ($commands as $command) {
            $cmd = $command . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($imagePath) . ' 2>&1';
            $output = shell_exec($cmd);
            $result = json_decode($output, true);
            if (is_array($result)) {
                return $result;
            }
        }

        return ['error' => 'OCR execution failed or returned invalid response.'];
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

private function containsReceiverName(string $text, array $expectedNames): bool
{
    if (preg_match_all('/(?:To|Paid To)\s*:?\s*([^\n]+)/i', $text, $matches)) {
        foreach ($matches[1] as $name) {
            // Normalize whitespace and case
            $cleanName = strtolower(trim(preg_replace('/\s+/', ' ', $name)));

            // Remove common prefixes
            $cleanName = preg_replace('/^(mr|mrs|dr)\s+/i', '', $cleanName);

            foreach ($expectedNames as $expected) {
                $expectedClean = strtolower(trim(preg_replace('/\s+/', ' ', $expected)));

                // ✅ Use "contains" instead of strict equality
                if (strpos($cleanName, $expectedClean) !== false) {
                    log_message('debug', 'Name matched: ' . $expectedClean);
                    return true;
                }
            }
        }
    }

    // Extra handling for next-line case
    $lines = preg_split('/\r?\n/', $text);
    for ($i = 0; $i < count($lines) - 1; $i++) {
        if (preg_match('/^(?:To|Paid To)\s*:?\s*$/i', trim($lines[$i]))) {
            $nextLine = strtolower(trim(preg_replace('/\s+/', ' ', $lines[$i+1])));
            $nextLine = preg_replace('/^(mr|mrs|dr)\s+/i', '', $nextLine);

            foreach ($expectedNames as $expected) {
                $expectedClean = strtolower(trim(preg_replace('/\s+/', ' ', $expected)));
                if (strpos($nextLine, $expectedClean) !== false) {
                    log_message('debug', 'Name matched: ' . $expectedClean);
                    return true;
                }
            }
        }
    }

    return false;
}

    private function extractDateFromText(string $text): ?string
    {
        $patterns = [
            '/\b(\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4})\b/',
            '/\b(\d{4}[\/-]\d{1,2}[\/-]\d{1,2})\b/',
            '/\b([A-Za-z]{3,9}\s+\d{1,2},?\s+\d{4})\b/',
            '/\b(\d{1,2}\s+[A-Za-z]{3,9}\s+\d{4})\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[1] as $candidate) {
                    $normalized = $this->normalizeDateString($candidate);
                    if ($normalized !== null) {
                        return $normalized;
                    }
                }
            }
        }

        return null;
    }

    private function normalizeDateString(string $dateStr): ?string
    {
        $formats = ['d/m/Y', 'd-m-Y', 'd/m/y', 'd-m-y', 'Y-m-d', 'Y/m/d', 'M d, Y', 'F d, Y', 'd M Y', 'd F Y'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, trim($dateStr));
            if ($dt !== false) {
                return $dt->format('Y-m-d');
            }
        }

        return null;
    }

    private function isTodayDate(?string $dateStr): bool
    {
        if (empty($dateStr)) {
            return false;
        }

        $res =  $dateStr === date('Y-m-d');
        log_message('debug', 'isDatevalid: ' . $res);
        print_r("res::".$res);
                    
        return $res;
    }


}
