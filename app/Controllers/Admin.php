<?php

namespace App\Controllers;

use App\Models\PolicyModel;
use App\Models\DataModel;
use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Libraries\PolicyExtractor;
use App\Libraries\OCRProcessor;
use App\Models\SubscriptionModel;
use CodeIgniter\Email\Email;
use CodeIgniter\I18n\Time;
use App\Models\EmployeeSubscriptionModel;
use App\Models\HistoryModel;
use App\Models\PaymentModel;
use App\Models\ExpiryDataModel;
use App\Services\SubscriptionService;
//require_once APPPATH . '../public/dompdf/autoload.inc.php';
require_once FCPATH . 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

use DateTime;

class Admin extends BaseController
{
    protected $policyModel;
    protected $policyExtractor;
    protected $ocrProcessor;
    protected $dataModel;
    protected $attendanceModel;
    protected $historyModel;
    protected $employeeModel;
    protected $paymentModel;
    protected $empSubscriptionModel;
    protected $subscriptionService;
    protected $expiryDataModel;
    public function __construct()
    {
        $this->policyModel = new PolicyModel();
        $this->policyExtractor = new PolicyExtractor();
        $this->ocrProcessor = new OCRProcessor();
        $this->dataModel = new DataModel();
        $this->attendanceModel = new AttendanceModel();     
        $this->historyModel = new HistoryModel();
        $this->employeeModel = new EmployeeModel();
        $this->paymentModel = new PaymentModel();
        $this->empSubscriptionModel = new EmployeeSubscriptionModel();
        $this->subscriptionService = new SubscriptionService();      
        $this->expiryDataModel = new ExpiryDataModel();
    }

    /**
     * Display admin dashboard
     */
public function index()
{
    $db = \Config\Database::connect();

    // Employees + subscriptions
    $builder = $db->table('employee');
    $builder->select('employee.employeeId, employee.profilePhoto, employee.name, employee.gender, subscriptions.endDate, subscriptions.status');
    $builder->join('subscriptions', 'subscriptions.employeeId = employee.employeeId AND subscriptions.status = "active"', 'left');
    $employees = $builder->get()->getResultArray();

    foreach ($employees as &$emp) {
        if (!empty($emp['endDate'])) {
            $endDate = strtotime($emp['endDate']);
            $today   = strtotime(date('Y-m-d'));
            $emp['daysRemaining'] = ceil(($endDate - $today) / (60 * 60 * 24));
        } else {
            $emp['daysRemaining'] = null;
        }
    }

    // Dashboard metrics
    $data['employees']     = $employees;
    $data['allCount']      = $this->policyModel->countAll();
    $data['monthlyCount']  = $this->policyModel->where('MONTH(created_at)', date('m'))->countAllResults();
    $data['todaysCount']   = $this->policyModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults();
    $data['totalData']     = $this->dataModel->countAll();
    $data['totalPolicies'] = $this->policyModel->countAll();
    
    $data['usedData']   = $this->dataModel->where('actionTaken', 1)->countAllResults();
    $data['unusedData'] = $this->dataModel->where('actionTaken', 0)->countAllResults();
    $topPerformers = $db->query("
    SELECT e.name, e.profilePhoto, e.gender, COUNT(p.policy_id) AS total, MIN(p.issue_date) AS first_issue, MAX(p.issue_date) AS last_issue
    FROM policies p
    JOIN employee e ON e.employeeId = p.telecaller
    WHERE p.issue_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
    AND p.issue_date < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
    GROUP BY e.employeeId, e.name, e.profilePhoto
    ORDER BY total DESC;
    ")->getResultArray();

    $data['topPerformers'] = $topPerformers;

    $telecallerProgressQuery = $db->query("SELECT 
    e.employeeId, 
    e.name, 
    e.profilePhoto, 
    e.gender,
    COUNT(DISTINCT d.recordId) AS total_leads,
    COUNT(DISTINCT CASE WHEN d.actionTaken = 1 THEN d.recordId END) AS handled_leads,
    COUNT(DISTINCT p.policy_id) AS policies_sold
FROM employee e
LEFT JOIN data d 
    ON d.telecaller = e.employeeId
LEFT JOIN policies p 
    ON p.telecaller = e.employeeId
WHERE e.jobTitle = 'telecaller'
GROUP BY 
    e.employeeId, 
    e.name, 
    e.profilePhoto, 
    e.gender
ORDER BY 
    policies_sold DESC, 
    handled_leads DESC, 
    total_leads DESC;");
    $data['telecallerProgress'] = $telecallerProgressQuery->getResultArray();

    // Chart data
    $chartQuery = $db->query("
        SELECT DATE_FORMAT(issue_date, '%b') AS month, COUNT(*) AS total
        FROM policies
        WHERE issue_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY YEAR(issue_date), MONTH(issue_date)
        ORDER BY YEAR(issue_date), MONTH(issue_date)
    ");
    $data['chartData'] = $chartQuery->getResultArray();

    // Policy type distribution
    $typeQuery = $db->query("
        SELECT insurance_type, COUNT(*) AS total
        FROM policies
        GROUP BY insurance_type
        ORDER BY total DESC
    ");
    $data['policyTypes'] = $typeQuery->getResultArray();

$records = $this->dataModel
    ->select('data.recordId, data.expiryDate, data.telecaller, employee.name as employeeName, employee.profilePhoto, employee.gender')
    ->join('employee', 'employee.employeeId = data.telecaller')
    ->where('data.actionTaken', 0)
    ->orderBy('data.recordId', 'ASC')
    ->groupBy('data.telecaller')
    ->findAll();

$data['unusedDataRecords'] = $records;

    return view('admin/dashboard', $data);
}



    /**
     * Display upload policy form
     */
    public function uploadPolicy()
    {   
        $data = [
            'results' => session()->getFlashdata('uploadResults') ?? []
        ];
        return view('admin/uploadpolicy', $data);
    }

    /**
     * Handle PDF upload and extraction
     */
    public function uploadPolicyPost()
    {
        if (! $this->request->is('post')) {
            return redirect()->to('/admin/upload')->with('error', 'Invalid request method');
        }

        $files = $this->request->getFiles();
        $results = [];
        $errors = [];
        $warnings = [];

        if (empty($files['pdfs'])) {
            return redirect()->to('/admin/upload')->with('error', 'No files selected');
        }

        

        $uploadPath = WRITEPATH . 'uploads/policies/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        foreach ($files['pdfs'] as $file) {
            // Validate file upload
            if (! $file instanceof \CodeIgniter\HTTP\Files\UploadedFile || ! $file->isValid()) {
                $errors[] = $file->getClientName() . ' - File upload failed.';
                continue;
            }

            // Check file extension
            $fileExtension = strtolower($file->getClientExtension());
            if ($fileExtension !== 'pdf') {
                $errors[] = $file->getClientName() . ' - Invalid file type. Only PDF files are allowed.';
                continue;
            }

            // Check MIME type
            $mimeType = $file->getClientMimeType() ?: $file->getMimeType();
            if ($mimeType !== 'application/pdf') {
                $errors[] = $file->getClientName() . ' - Invalid PDF format (MIME: ' . $mimeType . ').';
                continue;
            }

            try {
                $details = $this->policyExtractor->extractPolicyDetails($file->getTempName());

                // Validate extracted policy number
                if (empty($details['policyNumber'])) {
                    $errors[] = $file->getClientName() . ' - Could not extract policy number. PDF may be invalid or secured.';
                    continue;
                }

                // Check for duplicate policy number
                /*
                $existingPolicy = $this->policyModel->getPolicyByNumber($details['policyNumber']);
                if ($existingPolicy) {
                    $warnings[] = $file->getClientName() . ' - Policy #' . $details['policyNumber'] . ' already exists in database. Skipped.';
                    continue;
                } */

                $existingPolicy = $this->policyModel->getPolicyByDetails(
                    $details['policyNumber'],
                    $details['policyStart'],   // use same key as insert
                    $details['expiryDate']     // use same key as insert
                );

                if ($existingPolicy) {
                    $warnings[] = $file->getClientName()  
                                . ' already exists in database. Skipped.';
                    continue;
                }

                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);

                $insertData = [
                    'policy_number' => $details['policyNumber'],
                    'holder_name' => $details['holderName'],
                    'company_name' => $details['companyName'],
                    'vehicle_number' => $details['vehicleNumber'],
                    'insurance_type' => $details['insuranceType'],
                    'mobileNo'       => '',
                    'telecaller'     => '',
                    'cashback'        => '',
                    'premium'        => '',  
                    'policyType'     => '',
                    'issue_date' => $details['policyStart'],
                    'expiry_date' => $details['expiryDate'],
                    'file_path' => 'writable/uploads/policies/' . $newName,
                ];

                $insertId = $this->policyModel->insert($insertData);
                if ($insertId === false) {
                    $dbErrors = $this->policyModel->errors();
                    $errors[] = $file->getClientName() . ' - Database error: ' . implode(' ', $dbErrors);
                    // Clean up uploaded file
                    if (file_exists($uploadPath . $newName)) {
                        unlink($uploadPath . $newName);
                    }
                    continue;
                }

                $results[] = [
                    'fileName' => $file->getClientName(),
                    'details' => $details,
                    'path' => 'writable/uploads/policies/' . $newName,
                ];
            } catch (\Exception $e) {
                $errors[] = $file->getClientName() . ' - ' . $e->getMessage();
            }
        }

        if (empty($results) && ! empty($errors)) {
            return redirect()->to('/admin/upload')->with('error', implode(' | ', $errors));
        }

        if (!empty($results)) {
            $cache = \Config\Services::cache();
            $cache->delete('all_policies_count');
            $cache->delete('expired_current_month_count');
            $cache->delete('expired_next_month_count');
        }

        $redirect = redirect()->to('/admin/upload')->with('uploadResults', $results);
        if (! empty($errors)) {
            $redirect = $redirect->with('error', implode(' | ', $errors));
        }
        if (! empty($warnings)) {
            $redirect = $redirect->with('warning', implode(' | ', $warnings));
        }

        return $redirect;
    }

    /**
     * Display search policy page
     */
    public function searchPolicy()
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'all_policies_count';
        
        $totalPolicies = $cache->get($cacheKey);
        if ($totalPolicies === null) {
            $totalPolicies = $this->policyModel->countAllResults();
            $cache->save($cacheKey, $totalPolicies, 0);
        }

        $data = [
            'totalPolicies' => $totalPolicies
        ];
        return view('admin/searchpolicy', $data);
    }

    public function currentMonthPolicy()
    {
        $data = [
            'month' => date('F Y')
        ];
        return view('admin/policy/currentmonthpolicy', $data);
    }

    protected function normalizePolicyPerPage($perPage): int
    {
        $perPage = (int) $perPage;

        if ($perPage <= 0) {
            return 10000;
        }

        if ($perPage > 10000) {
            return 10000;
        }

        return $perPage;
    }

    /**
     * API endpoint for search with pagination
     */
    public function searchPolicyApi()
    {
        $search = trim((string) ($this->request->getVar('q') ?? ''));
        $page   = (int) ($this->request->getVar('page') ?? 1);
        $perPage = $this->normalizePolicyPerPage($this->request->getVar('per_page'));

        $hasExplicitPagination = $this->request->getVar('page') !== null || $this->request->getVar('per_page') !== null;
        if (! $hasExplicitPagination) {
            $page = 1;
            $perPage = 10000;
        }

        $offset = ($page - 1) * $perPage;

        if (!empty($search)) {
            $policies = $this->policyModel->searchPoliciesWithTelecaller($search, $perPage, $offset);
            $total    = $this->policyModel->countSearch($search);
        } else {
            $cache    = \Config\Services::cache();
            $cacheKey = 'all_policies_count';
            $total    = $cache->get($cacheKey);

            if ($total === null) {
                $total = $this->policyModel->countAllResults();
                $cache->save($cacheKey, $total, 0);
            }

            $policies = $this->policyModel->getAllPoliciesWithTelecaller($perPage, $offset);
        }

        $totalPages = $perPage ? ceil($total / $perPage) : 1;

        return $this->response->setJSON([
            'success'     => true,
            'data'        => $policies,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages
        ]);
    }

    /**
     * Display expired current month policies
     */
    public function expiredCurrentMonth()
    {
        $data = [
            'month' => date('F Y')
        ];
        return view('admin/currentexpiries', $data);
    }

    /**
     * API endpoint for current month expired policies
     */
    public function expiredCurrentMonthApi()
    {
        $search = $this->request->getVar('q') ?? '';
        $page = (int)($this->request->getVar('page') ?? 1);
        $perPage = (int)($this->request->getVar('per_page') ?? 25);

        if ($perPage === 0 || $perPage > 200) {
            $perPage = 25;
        }

        $offset = ($page - 1) * $perPage;
        $cache = \Config\Services::cache();
        $countCacheKey = 'expired_current_month_count';

        $total = $cache->get($countCacheKey);
        if ($total === null) {
            $total = $this->policyModel->countExpiredCurrentMonth();
            $cache->save($countCacheKey, $total, 0);
        }

        $policies = $this->policyModel->getExpiredCurrentMonth($perPage, $offset);

        if (!empty($search)) {
            $policies = array_filter($policies, function ($policy) use ($search) {
                $searchLower = strtolower($search);
                return stripos($policy['policy_number'], $searchLower) !== false ||
                       stripos($policy['holder_name'], $searchLower) !== false ||
                       stripos($policy['vehicle_number'], $searchLower) !== false;
            });
        }

        $totalPages = $perPage ? ceil($total / $perPage) : 1;

        return $this->response->setJSON([
            'success' => true,
            'data' => array_values($policies),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages
        ]);
    }
    public function currentMonthApi()
    {
        $perPage = $this->request->getVar('per_page') ?? 0;
        $perPage = (int) $perPage;
        if ($perPage <= 0) {
            // return all for this view by default
            $perPage = 999999;
        }

        $page = (int) ($this->request->getVar('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        try {
            $policies = $this->policyModel->getCurrentMonthPoliciesWithTelecaller($perPage, $offset);

            return $this->response->setJSON([
                'success' => true,
                'data'    => array_values($policies)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'currentMonthApi error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load policies'
            ]);
        }
    }
    /**
     * Display expired next month policies
     */
    public function expiredNextMonth()
    {
        $nextMonth = date('F Y', strtotime('+1 month'));
        $data = [
            'month' => $nextMonth
        ];
        return view('admin/nextexpiries', $data);
    }

    /**
     * API endpoint for next month expired policies
     */
 public function expiredNextMonthApi()
{
    $request = service('request');

    $draw   = (int) $request->getPost('draw');
    $start  = (int) $request->getPost('start');
    $length = (int) $request->getPost('length');
    $search = $request->getPost('search')['value'] ?? '';
    $order  = $request->getPost('order');

    $columns = ['holder_name','vehicle_number','insurance_type','mobileNo','issue_date','expiry_date','policy_id'];
    $orderColumn = 'expiry_date';
    $orderDir    = 'ASC';

    if (!empty($order)) {
        $colIndex = (int) $order[0]['column'];
        $orderDir = $order[0]['dir'];
        $orderColumn = $columns[$colIndex] ?? 'expiry_date';
    }

    // Get filtered data
    $data = $this->policyModel->getExpiredNextMonth($length, $start, $search, $orderColumn, $orderDir);

    // Total count (without search)
    $nextMonth = date('m') + 1;
    $nextYear = date('Y');
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }

    $totalRecords = $this->policyModel
        ->where("YEAR(expiry_date) = {$nextYear}", null, false)
        ->where("MONTH(expiry_date) = {$nextMonth}", null, false)
        ->countAllResults();

    // Filtered count (with search)
    $filteredRecords = $this->policyModel
        ->where("YEAR(expiry_date) = {$nextYear}", null, false)
        ->where("MONTH(expiry_date) = {$nextMonth}", null, false);

    if (!empty($search)) {
        $filteredRecords->groupStart()
            ->like('holder_name', $search)
            ->orLike('vehicle_number', $search)
            ->orLike('insurance_type', $search)
            ->orLike('mobileNo', $search)
        ->groupEnd();
    }

    $filteredCount = $filteredRecords->countAllResults();

    return $this->response->setJSON([
        "draw"            => $draw,
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $filteredCount,
        "data"            => $data
    ]);
}   
    /*
    public function extractImageText()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $image = $this->request->getFile('image');

        if (!$image || !$image->isValid()) {
            return $this->response->setJSON(['error' => 'Invalid image file']);
        }

        // Save temporary image
        $uploadPath = WRITEPATH . 'uploads/images/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = $image->getRandomName();
        $image->move($uploadPath, $newName);
        $imagePath = $uploadPath . $newName;

        // Extract text using OCR
        $result = $this->ocrProcessor->extractTextFromImage($imagePath);

        // Clean up temp image
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        return $this->response->setJSON($result);
    }
    */
    /**
     * Export expired policies to Excel
     */
    public function exportExpiredExcel()
    {
        
        helper('excel');
        
        $policies = $this->policyModel->getExpiredCurrentMonth(999999);
        $filename = 'expired-policies-' . date('Y-m-d_His');
        
        policyTableToExcel($policies, $filename);
    }
    

    /**
     * Export next month's expiries to Excel
     */
    public function exportNextExpiriesExcel()
    {
        helper('excel');
        
        $policies = $this->policyModel->getExpiredNextMonth(999999);
        
        $filename = 'next-expiries-' . date('Y-m-d_His');
        
        policyTableToExcel($policies, $filename);
    }

    /**
     * Export current month's policies to Excel
     */
    public function exportCurrentMonthExcel()
    {
        helper('excel');
        
        $policies = $this->policyModel->getCurrentMonthPoliciesWithTelecaller(999999);
        
        $filename = 'current-month-policies-' . date('Y-m-d_His');
        
        policyTableToExcel($policies, $filename);
    }

    /**
     * Download policy PDF
     */
    public function downloadPolicy($policyId)
    {
        $policy = $this->policyModel->find($policyId);

        if (! $policy) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Policy not found');
        }

        $relativePath = preg_replace('#^writable[\\/]+#i', '', $policy['file_path']);
        $filePath = WRITEPATH . $relativePath;

        if (! file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        return $this->response->download($filePath, null)->setFileName(basename($filePath));
    }

    /**
     * Renew subscription
     */
    public function renewSubscription()
    {
        $data = [
            'renewStatus' => session()->getFlashdata('renewStatus'),
            'renewError' => session()->getFlashdata('renewError'),
            'renewText' => session()->getFlashdata('renewText'),
            'renewReceiver' => session()->getFlashdata('renewReceiver'),
            'renewDate' => session()->getFlashdata('renewDate'),
        ];

        return view('admin/renew', $data);
    }

    /**
     * Handle renew subscription image upload and OCR validation
     */


    public function renewSubscriptionPost($img)
    {
        /*
        if (! $this->request->is('post')) {
            return redirect()->to('/admin/renew')->with('renewError', 'Invalid request method.');
        }*/

        //$image = $this->request->getFile('renew_image');

        $response;
        $image = $img;

        if (! $image || ! $image->isValid()) {
            //return redirect()->to('/admin/renew')->with('renewError', 'Please upload a valid image file.');
            $response = [
                'success' => false,
                'message' => 'Please upload a valid image file.'
            ];
            return $response;
        }

        $extension = strtolower($image->getClientExtension() ?: pathinfo($image->getClientName(), PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'gif'];
        if (! in_array($extension, $allowedExtensions)) {
            //return redirect()->to('/admin/renew')->with('renewError', 'Only image files are allowed (jpg, png, webp, bmp, gif).');
            $response = [
                'success' => false,
                'message' => 'Only image files are allowed (jpg, png, webp, bmp, gif).'
            ];
            return $response;
        }
        $uploadPath = FCPATH . 'uploads/receipts/'; // FCPATH points to /public
        //$uploadPath = WRITEPATH . 'uploads/receipts/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = $image->getRandomName();
        if (! $image->move($uploadPath, $newName)) {
            //return redirect()->to('/admin/renew')->with('renewError', 'Failed to save uploaded image.');
            $response = [
                'success' => false,
                'message' => 'Failed to save uploaded image.'
            ];
            return $response;
        }

        $imagePath = $uploadPath . $newName;
        $ocrResult = $this->runOcr($imagePath);

        if (! empty($ocrResult['error'])) {
            //return redirect()->to('/admin/renew')->with('renewError', 'OCR failed: ' . $ocrResult['error']);
            $response = [
                'success' => false,
                'message' => 'OCR failed: ' . $ocrResult['error']
            ];
            return $response;
        }

        $text = trim($ocrResult['text'] ?? '');
        $validNames = [
            "Vijay Kailas Kumawat",
            "Vijey Kumawatt",
            "Vijay Kailash Kumawat",
            "Vijay Kumawat"
        ];
        $receiverValid = $this->containsReceiverName($text, $validNames);
        $dateText = $this->extractDateFromText($text);
        $dateValid = $this->isTodayDate($dateText);

        // ✅ Insert into subscription table regardless of validity
        $subscriptionModel = new SubscriptionModel();

        $insertData = [
        'receiver_name' => $receiverValid ? 'Vijay Kailas kumawat' : 'Not found',
        'screenshot'    => 'uploads/receipts/' . $newName,
        'status'        => ($receiverValid && $dateValid) ? 1 : 0
        ];

        if (! $subscriptionModel->insert($insertData)) {
            print_r($subscriptionModel->errors()); // show why it failed
            exit;
        }
        /*
        // Prepare email
        $email = \Config\Services::email();

        $email->setFrom('vijaykmwt49@gmail.com', 'Subscription System');
        $email->setTo('vijay.kumawat.mca16@gmail.com');
        $email->setSubject('New Payment Screenshot Uploaded');
        $email->setMessage(
            "Hello Vijay,\n\n" .
            "A new payment screenshot has been uploaded.\n\n" .
            "Receiver: " . ($receiverValid ? 'Vijay Kailas kumawat' : 'Not found') . "\n" .
            "Date: " . ($dateText ?: date('Y-m-d')) . "\n" .
            "Status: " . (($receiverValid && $dateValid) ? 'Valid' : 'Invalid') . "\n\n" .
            "Regards,\nSubscription System"
        );

        // Attach the screenshot file
        $email->attach($imagePath);

        if (! $email->send()) {
            log_message('error', 'Email failed: ' . $email->printDebugger(['headers']));
        } */

        if (! $receiverValid || ! $dateValid) {
            $reason = [];
            if (! $receiverValid) {
                $response = [
                    'success' => false,
                    'message' => 'Receiver name is not "Vijay Kailas kumawat".'
                ];
                //$reason[] = 'Receiver name is not "Vijay Kailas kumawat".';
            }
            if (! $dateValid) {
                $response = [
                    'success' => false,
                    'message' => 'Screenshot is old one.'
                ];
                //$reason[] = 'Screenshot date is not today.';
            }

            
        }
        return $response;
        return redirect()->to('/admin/renew')
            ->with('renewStatus', 'Payment screenshot verified successfully.')
            ->with('renewText', $text)
            ->with('renewReceiver', 'Vijay Kailas kumawat')
            ->with('renewDate', $dateText ?: date('Y-m-d'));
    }




    private function runOcr(string $imagePath): array
    {
        $apiKey = 'K89821879188957'; // Better: store this in .env

        if (!file_exists($imagePath)) {
            return ['error' => 'Image not found.'];
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.ocr.space/parse/image',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_POSTFIELDS => [
                'apikey' => $apiKey,
                'language' => 'eng',
                'file' => new \CURLFile($imagePath),
            ],
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);

            return ['error' => $error];
        }

        curl_close($curl);

        $json = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid response from OCR API'];
        }

        if (!empty($json['ParsedResults'][0]['ParsedText'])) {
            return [
                'text' => trim($json['ParsedResults'][0]['ParsedText'])
            ];
        }

        return [
            'error' => $json['ErrorMessage'] ?? 'No text detected.'
        ];
    }

    private function containsReceiverName(string $text, array $expectedNames): bool
    {
        // Normalize the input text
        $normalizedText = strtolower(preg_replace('/\s+/', ' ', $text));

        foreach ($expectedNames as $expected) {
            // Normalize expected name
            $expectedClean = strtolower(trim(preg_replace('/\s+/', ' ', $expected)));

            // Simple substring check
            if (strpos($normalizedText, $expectedClean) !== false) {
                return true;
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

        return $dateStr === date('Y-m-d');
    }

    function extractTransactionId($input) {
        // Regex looks for "Transaction ID" followed by a space and an alphanumeric string
        $pattern = '/Transaction ID\s+([A-Za-z0-9]+)/i';
        if (preg_match($pattern, $input, $matches)) {
            return $matches[1]; // Transaction ID
        }
        return null; // Not found
    }
    function extractAmount($input) {
        // Regex looks for ? or ₹ or Rs followed by digits
        $pattern = '/[₹?Rs]\s?(\d+(?:\.\d{1,2})?)/i';
        if (preg_match($pattern, $input, $matches)) {
            return $matches[1]; // Amount as string
        }
        return null; // Not found
    }
    function extractUTR($input) {
        // Regex looks for "UTR:" followed by digits
        $pattern = '/UTR:\s*([0-9]+)/i';
        if (preg_match($pattern, $input, $matches)) {
            return $matches[1]; // UTR number
        }
        return null; // Not found
    }

    /**
     * Delete a policy and invalidate cache
     */
    public function deletePolicy($policyId)
    {
        $policy = $this->policyModel->find($policyId);

        if (! $policy) {
            return $this->response->setJSON(['success' => false, 'message' => 'Policy not found']);
        }

        $deleted = $this->policyModel->delete($policyId);

        if ($deleted) {
            $cache = \Config\Services::cache();
            $cache->delete('all_policies_count');
            $cache->delete('expired_current_month_count');
            $cache->delete('expired_next_month_count');

            return $this->response->setJSON(['success' => true, 'message' => 'Policy deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete policy']);
    }

    /**
     * Update a policy and invalidate cache
     */
    /*
    public function updatePolicy($policyId)
    {
        if (! $this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $policy = $this->policyModel->find($policyId);

        if (! $policy) {
            return $this->response->setJSON(['success' => false, 'message' => 'Policy not found']);
        }

        $updateData = $this->request->getJSON(true);

        $updated = $this->policyModel->update($policyId, $updateData);

        if ($updated !== false) {
            $cache = \Config\Services::cache();
            $cache->delete('all_policies_count');
            $cache->delete('expired_current_month_count');
            $cache->delete('expired_next_month_count');

            return $this->response->setJSON(['success' => true, 'message' => 'Policy updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update policy']);
    } */
    public function paymentHistory()
    {
        $subscriptionModel = new \App\Models\SubscriptionModel();
    $subscriptions = $subscriptionModel->orderBy('created_date', 'DESC')->findAll();

    return view('admin/payment_history', ['subscriptions' => $subscriptions]);
    }

    public function dataLoader(){
        return view('admin/dataloader');
    }

public function uploadDataPost()
{
    // Accept either 'csv_file' or 'csvFile' as the input name
    $file = $this->request->getFile('csv_file') ?: $this->request->getFile('csvFile');

    if (! $file || ! $file->isValid()) {
        return $this->response->setStatusCode(400)
            ->setJSON(['success' => false, 'message' => 'No valid CSV uploaded']);
    }

    // Route based on selected table: 'data' -> existing flow, 'expiry' -> expiry data flow
    $tableType = $this->request->getPost('table');

    if ($tableType === 'expiry') {
        return $this->processExpiryCsv($file);
    }

    // Block upload if table already has data
    /*
    $count = $this->dataModel->countAll();
    if ($count >= 1) {
        return $this->response->setStatusCode(400)
            ->setJSON(['success' => false, 'message' => 'Data table already has records. Please clear before uploading.']);
    } */

    // DB fields that the user can map (exclude recordId from mapping)
    $dbFields = [
        'regDate','regDateMonth','regNumber','ownerName',
        'address','vehicleMaker','vehicleModel','fuelType','saleAmt',
        'seatCapacity','cubicCapacity','mobile','expiryDate','prevInsuCompany','finance','telecaller'
    ];

    // Read CSV header (first line)
    $stream = fopen($file->getTempName(), 'r');
    if ($stream === false) {
        return $this->response->setStatusCode(500)->setJSON(['success'=>false,'message'=>'Unable to read uploaded file']);
    }

    $rawHeaders = fgetcsv($stream);
    if ($rawHeaders === false) {
        fclose($stream);
        return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'CSV appears empty or invalid']);
    }

    $headers = array_map(function($h){ return preg_replace('/^\x{FEFF}/u', '', trim((string)$h)); }, $rawHeaders);

    // mapping JSON from the POST body
    $mappingJson = $this->request->getPost('mapping');
    $mapping = $mappingJson ? json_decode($mappingJson, true) : null;

    // If no mapping provided, try exact auto-match (case-insensitive)
    if (! $mapping) {
        $lowerHeaders = array_map('mb_strtolower', $headers);
        $allFound = true;
        $mapping = [];
        foreach ($dbFields as $f) {
            $pos = array_search(mb_strtolower($f), $lowerHeaders);
            if ($pos === false) { $allFound = false; break; }
            $mapping[$f] = $headers[$pos];
        }
        if (! $allFound) {
            fclose($stream);
            return $this->response->setStatusCode(400)
                ->setJSON(['success'=>false,'message'=>'Mapping required: CSV headers do not match DB fields and no mapping was submitted']);
        }
    } else {
        // ensure mapping covers required dbFields
        foreach ($dbFields as $f) {
            if (!isset($mapping[$f]) || $mapping[$f] === '') {
                fclose($stream);
                return $this->response->setStatusCode(400)
                    ->setJSON(['success'=>false,'message'=>"Mapping incomplete: missing mapping for {$f}"]);
            }
        }
    }

    // Build header index lookup
    $headerIndex = [];
    foreach ($headers as $i => $h) { $headerIndex[mb_strtolower($h)] = $i; }

    $rows = [];
    while (($csvRow = fgetcsv($stream)) !== false) {
        // skip empty rows
        $nonEmpty = false;
        foreach ($csvRow as $c) { if (trim((string)$c) !== '') { $nonEmpty = true; break; } }
        if (! $nonEmpty) continue;

        $rowData = [];

        // Generate system recordId (alphanumeric, 15–16 chars)
        //$rowData['recordId'] = $this->generateRecordId();

        foreach ($dbFields as $field) {
            $csvHeader = $mapping[$field];
            $idx = array_key_exists(mb_strtolower($csvHeader), $headerIndex) ? $headerIndex[mb_strtolower($csvHeader)] : null;
            $value = ($idx !== null && array_key_exists($idx, $csvRow)) ? trim($csvRow[$idx]) : null;
            $rowData[$field] = $value;
        }

        // auto-fill required fields
        $rowData['dataUploadDate'] = date('Y-m-d H:i:s');
        $rowData['actionTaken']    = 0;
        $rowData['isImportant']    = 0;
        $rowData['alreadySale']    = 0;
        $rowData['modifiyDate']    = date('Y-m-d H:i:s');
        $rowData['isIntrested']    = 0;
        $rowData['saleInGb']       = 0;

        $rows[] = $rowData;
    }
    fclose($stream);

    if (empty($rows)) {
        return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'No data rows found in CSV']);
    }

    $currentEmployeeName = session()->get('employeeName');
    $currentEmployeeId = session()->get('employeeId');
    $isRestrictedUser = (strtolower((string) $currentEmployeeName) === 'testuser') || (string) $currentEmployeeId === 'cef99519ba925515';
    if ($isRestrictedUser && count($rows) > 20) {
        return $this->response->setStatusCode(400)->setJSON([
            'success' => false,
            'message' => 'Upload blocked: this account is restricted to 20 rows per upload. This file contains ' . count($rows) . ' rows.'
        ]);
    }

    try {
        $builder = $this->dataModel->db->table('data');
        $builder->insertBatch($rows);
    } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON(['success'=>false,'message'=>'DB insert failed: '.$e->getMessage()]);
    }

    return $this->response->setJSON(['success'=>true,'message'=>'Data uploaded successfully']);
}

/**
 * Handle CSV upload for the "expiry" table (Expiry Data).
 * Expiry Data has only two mapped columns: regNumber and employeeId.
 */
    private function processExpiryCsv($file)
    {
        // DB fields that the user can map for expiry data
        $dbFields = ['regNumber', 'employeeId'];

        // Read CSV header (first line)
        $stream = fopen($file->getTempName(), 'r');
        if ($stream === false) {
            return $this->response->setStatusCode(500)->setJSON(['success'=>false,'message'=>'Unable to read uploaded file']);
        }

        $rawHeaders = fgetcsv($stream);
        if ($rawHeaders === false) {
            fclose($stream);
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'CSV appears empty or invalid']);
        }

        $headers = array_map(function($h){ return preg_replace('/^\x{FEFF}/u', '', trim((string)$h)); }, $rawHeaders);

        // mapping JSON from the POST body
        $mappingJson = $this->request->getPost('mapping');
        $mapping = $mappingJson ? json_decode($mappingJson, true) : null;

        // If no mapping provided, try exact auto-match (case-insensitive)
        if (! $mapping) {
            $lowerHeaders = array_map('mb_strtolower', $headers);
            $allFound = true;
            $mapping = [];
            foreach ($dbFields as $f) {
                $pos = array_search(mb_strtolower($f), $lowerHeaders);
                if ($pos === false) { $allFound = false; break; }
                $mapping[$f] = $headers[$pos];
            }
            if (! $allFound) {
                fclose($stream);
                return $this->response->setStatusCode(400)
                    ->setJSON(['success'=>false,'message'=>'Mapping required: CSV headers do not match DB fields and no mapping was submitted']);
            }
        } else {
            // ensure mapping covers required dbFields
            foreach ($dbFields as $f) {
                if (!isset($mapping[$f]) || $mapping[$f] === '') {
                    fclose($stream);
                    return $this->response->setStatusCode(400)
                        ->setJSON(['success'=>false,'message'=>"Mapping incomplete: missing mapping for {$f}"]);
                }
            }
        }

        // Build header index lookup
        $headerIndex = [];
        foreach ($headers as $i => $h) { $headerIndex[mb_strtolower($h)] = $i; }

        $rows = [];
        while (($csvRow = fgetcsv($stream)) !== false) {
            // skip empty rows
            $nonEmpty = false;
            foreach ($csvRow as $c) { if (trim((string)$c) !== '') { $nonEmpty = true; break; } }
            if (! $nonEmpty) continue;

            $rowData = [];

            foreach ($dbFields as $field) {
                $csvHeader = $mapping[$field];
                $idx = array_key_exists(mb_strtolower($csvHeader), $headerIndex) ? $headerIndex[mb_strtolower($csvHeader)] : null;
                $value = ($idx !== null && array_key_exists($idx, $csvRow)) ? trim($csvRow[$idx]) : null;
                $rowData[$field] = $value;
            }

            // expiryDate stays blank (NULL) on CSV upload — it is filled later
            // by the user from the Expiry Data page; status starts at 0
            $rowData['expiryDate'] = null;
            $rowData['status']     = 0;

            $rows[] = $rowData;
        }
        fclose($stream);

        if (empty($rows)) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'No data rows found in CSV']);
        }

        $currentEmployeeName = session()->get('employeeName');
        $currentEmployeeId = session()->get('employeeId');
        $isRestrictedUser = (strtolower((string) $currentEmployeeName) === 'testuser') || (string) $currentEmployeeId === 'cef99519ba925515';
        if ($isRestrictedUser && count($rows) > 20) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Upload blocked: this account is restricted to 20 rows per upload. This file contains ' . count($rows) . ' rows.'
            ]);
        }

        try {
            $builder = $this->expiryDataModel->db->table('expirydata');
            $builder->insertBatch($rows);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['success'=>false,'message'=>'DB insert failed: '.$e->getMessage()]);
        }

        return $this->response->setJSON(['success'=>true,'message'=>'Expiry data uploaded successfully']);
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
        } while ($this->dataModel->where('recordId', $id)->countAllResults() > 0);

        return $id;
    }

    /*
    public function removeAllData(){
        
        try {
         
            if ($this->dataModel->countAll() < 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data table is already empty.'
                ]);
            }
            $this->dataModel->db->table('data')->truncate();
            //$this->historyModel->db->table('history')->truncate();
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All data removed successfully!'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    */
    public function removeAllData(){
        try {
            if ($this->dataModel->countAll() < 1 && $this->historyModel->countAll() < 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Both tables are already empty.'
                ]);
            }

            // Truncate both tables
            $this->dataModel->db->table('data')->truncate();
            $this->historyModel->db->table('history')->truncate();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'All records removed successfully from data and history tables!'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    public function removePreviousData(){
        try {
            // Current month start
            $currentMonthStart = date('Y-m-01 00:00:00');
            // Next month start
            $nextMonthStart = date('Y-m-01 00:00:00', strtotime('first day of next month'));
            // Previous month start
            $prevMonthStart = date('Y-m-01 00:00:00', strtotime('first day of last month'));

            // Step 1: Get all recordIds that will be deleted from data table
            $recordsToDelete = $this->dataModel->db->table('data')
                ->select('recordId')
                ->where("NOT (
                    (dataUploadDate >= '{$prevMonthStart}' AND dataUploadDate < '{$currentMonthStart}')
                    OR
                    (dataUploadDate >= '{$currentMonthStart}' AND dataUploadDate < '{$nextMonthStart}')
                )")
                ->get()
                ->getResultArray();

            // Extract recordIds into array
            $recordIds = array_column($recordsToDelete, 'recordId');

            if (!empty($recordIds)) {
                // Step 2: Delete related history rows
                $this->historyModel->db->table('history')
                    ->whereIn('recordId', $recordIds)
                    ->delete();

                // Step 3: Delete from data table
                $this->dataModel->db->table('data')
                    ->where("NOT (
                        (dataUploadDate >= '{$prevMonthStart}' AND dataUploadDate < '{$currentMonthStart}')
                        OR
                        (dataUploadDate >= '{$currentMonthStart}' AND dataUploadDate < '{$nextMonthStart}')
                    )")
                    ->delete();
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Records outside current and previous month removed successfully, including history!'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    //Employee management methods
    public function listEmployees()
    {   
        $employeeModel = new EmployeeModel();

        // Fetch all employees
        $employees = $employeeModel->findAll();

        // Pass to view
        return view('admin/employees/list', [
            'employees' => $employees
        ]);
    }

    public function newEmployee()
    {
        // Pull any flash data from session
        $data = [
            'uploadResults' => session('uploadResults'),
            'error'         => session('error'),
            'warning'       => session('warning'),
        ];

        return view('admin/employees/addemployee', $data);
    }
    
    public function extractData()
    {
        $file = $this->request->getFile('idproof');

        if (! $file || ! $file->isValid()) {
            return redirect()->to('/admin/employees/new')
                            ->with('error', 'No valid file uploaded');
        }

        try {
            $details = $this->policyExtractor->idExtract($file->getTempName());
            // Assume $details['name'] contains something like "Vijay Kailas Kumawat"
            if (!empty($details['name']) && !empty($details['dob'])) {
                // Extract first name (before space)
                $parts = explode(' ', trim($details['name']));
                $firstName = strtolower($parts[0]);

                // Convert DOB (e.g. 14/05/1992) into Ymd format
                $dobObj = \DateTime::createFromFormat('d/m/Y', $details['dob']);
                $dobFormatted = $dobObj ? $dobObj->format('Ymd') : '';

                // Build username: firstName + dob
                $username = $firstName . $dobFormatted;

                // Generate random password (plain + hashed)

                // Add to details array
                $details['username']      = $username;
                //$details['plainPassword'] = $details['mobile']; // optional, for showing to user
            }

        } catch (\Exception $e) {
            return redirect()->to('/admin/employees/new')
                            ->with('error', 'Error processing file: ' . $e->getMessage());
        }

        // You can also build warnings/errors arrays if your extractor provides them
        return redirect()->to('/admin/employees/new')
                        ->with('uploadResults', $details);
    }

    public function addEmployee()
    {
        $employeeModel = new EmployeeModel();
        $db = \Config\Database::connect();

        // Generate employeeId first
        //$empid = substr(bin2hex(random_bytes(8)), 0, 16);
        $empid = $this->generateRecordId();

        // Collect employee data
        $employeeData = [
            'employeeId'       => $empid,
            'name'             => $this->request->getPost('name'),
            'dateOfBirth'      => $this->request->getPost('dob'),
            'gender'           => $this->request->getPost('gender'),
            'email'            => $this->request->getPost('email'),
            'phoneNumber'      => $this->request->getPost('contactNo'),
            'address'          => $this->request->getPost('address'),
            'pincode'          => $this->request->getPost('pincode'),
            'username'         => $this->request->getPost('username'),
            'password'         => $this->request->getPost('password'),
            'hireDate'         => date('Y-m-d'),
            'jobTitle'         => 'telecaller',
            'employmentStatus' => 'Active',
            'bonusEligible'    => 0,
            'isActive'         => 1,
            'salary'           => 0,
            'bankAccountNumber'=> '',
            'workLocation'     => '',
            'profilePhoto'     => null // default if no image
        ];

        // Normalize DOB if provided
        if (!empty($employeeData['dateOfBirth'])) {
            $date = DateTime::createFromFormat('d/m/Y', $employeeData['dateOfBirth']);
            $employeeData['dateOfBirth'] = $date ? $date->format('Y-m-d') : null;
        }

        // ✅ Duplicate check: same name + DOB
        $existing = $employeeModel->where('name', $employeeData['name'])
                                ->where('dateOfBirth', $employeeData['dateOfBirth'])
                                ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Employee with same name and DOB already exists.');
        }

        // Handle profile image upload
        $file = $this->request->getFile('profile_img');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $targetPath = FCPATH . 'uploads/profile/';
            $file->move($targetPath, $newName);
            $employeeData['profilePhoto'] = $newName;
        } else {
            $employeeData['profilePhoto'] = null; // no file selected
        }

        // Start transaction
        $db->transStart();

        // Step 1: Insert employee
        if ($employeeModel->insert($employeeData) === false) {
            log_message('error', 'Employee insert failed: ' . json_encode($employeeModel->errors()));
            $db->transRollback();
            return redirect()->back()->with('error', 'Failed to add employee.');
        }

        // Step 2: Purchase subscription (employeeId now exists)
        
        $res = $this->subscriptionService->purchaseSubscription(
            $this->request->getFile('paymentScreenshot'),
            $employeeData
        );

        if (!$res['success']) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Subscription verification failed: ' . $res['message']);
        } 

        // Commit transaction
        $db->transComplete();

        return redirect()->to('/admin/employees/new')
                        ->with('success', 'Employee and subscription added successfully!')
                        ->with('employeeId', $employeeData['employeeId'])
                        ->with('subscriptionId', $res['subscriptionId']);
    }


    public function viewEmployee($id)
    {
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->find($id);

        if (! $employee) {
            return redirect()->to('/admin/employees')->with('error', 'Employee not found');
        }

        return view('admin/employees/viewemployee', ['employee' => $employee]);
    }
    public function updateEmployee()
        {

           
            $employeeModel = new EmployeeModel();

            // Grab employeeId from hidden field
            $employeeId = $this->request->getPost('employeeId');
            $statusInput = $this->request->getPost('status');
            $statusValue = ($statusInput === 'Active') ? 1 : 0;
            // Map UI fields to DB columns
               // Map UI fields to DB columns
 $data = [
    'name'             => $this->request->getPost('name'),
    'dateOfBirth'      => $this->request->getPost('dob'),
    'gender'           => $this->request->getPost('gender'),
    'email'            => $this->request->getPost('email'),
    'employmentStatus' => $statusInput,   // keep text if you want
    'isActive'         => $statusValue,   // numeric flag
    'phoneNumber'      => $this->request->getPost('contactNo'),
    'address'          => $this->request->getPost('address'),
    'pincode'          => $this->request->getPost('pincode'),
    'username'         => $this->request->getPost('username'),
    'password'         => $this->request->getPost('password'),
    'jobTitle'         => $this->request->getPost('jobTitle'),
    'hireDate'         => $this->request->getPost('hireDate'),
    'salary'           => $this->request->getPost('salary'),
    'nationalId'       => $this->request->getPost('nationalId'),
    'bankAccountNumber'=> $this->request->getPost('bankAccountNumber'),
    'workLocation'     => $this->request->getPost('workLocation'),
    'updatedAt'        => date('Y-m-d H:i:s')
];

            // Perform update
            $employeeModel->update($employeeId, $data);
            $path = '/admin/employee/' . $employeeId;
            return redirect()->to($path)->with('success', 'Employee updated successfully');
    }

    // ========================= ATTENDANCE MANAGEMENT =========================

    /**
     * Display mark attendance page
     */
    public function markAttendancePage()
    {
        $employeeModel = new EmployeeModel();
        $employees = $employeeModel->where('isActive', 1)->orderBy('name', 'ASC')->findAll();
        
        return view('admin/attendance/mark', [
            'employees' => $employees,
            'today'     => date('Y-m-d'),
        ]);
    }

    /**
     * Save attendance for single or multiple employees
     */
    private function isValidTime(?string $time): bool
{
    if (empty($time)) {
        return true; // allow null/empty
    }
    // Match HH:MM 24-hour format
   return (bool) preg_match('/^(?:[0-9]|[01]\d|2[0-3]):[0-5]\d$/', $time);
}

    public function saveAttendance()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        $attendanceDate = $this->request->getPost('attendance_date');
        $employees = $this->request->getPost('employees') ?? [];

        // Validate date
        if (!$attendanceDate || strtotime($attendanceDate) > strtotime(date('Y-m-d'))) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid or future date selected'
            ]);
        }

        if (empty($employees)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select at least one employee'
            ]);
        }

        $savedCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($employees as $employeeId) {
            if ($this->attendanceModel->attendanceExists($employeeId, $attendanceDate)) {
                $skippedCount++;
                continue;
            }

            $checkIn  = $this->request->getPost("check_in_$employeeId") ?: null;
            $checkOut = $this->request->getPost("check_out_$employeeId") ?: null;

            // Validate times
            if (!$this->isValidTime($checkIn) || !$this->isValidTime($checkOut)) {
                $errors[] = "Invalid time format for employee $employeeId";
                continue;
            }

            $attendanceData = [
                'employee_id'     => $employeeId,
                'attendance_date' => $attendanceDate,
                'status'          => $this->request->getPost("status_$employeeId") ?? 'Present',
                'check_in_time'   => $checkIn,
                'check_out_time'  => $checkOut,
                'remarks'         => $this->request->getPost("remarks_$employeeId") ?: null,
            ];

            if (!$this->attendanceModel->insert($attendanceData)) {
                $errors[] = "Failed to save attendance for employee $employeeId: " 
                        . implode(', ', $this->attendanceModel->errors());
            } else {
                $savedCount++;
            }
        }

        $message = "Attendance saved: $savedCount records. Skipped: $skippedCount (already marked).";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(", ", $errors);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'saved'   => $savedCount,
            'skipped' => $skippedCount,
        ]);
    }


    /**
     * Display attendance report page
     */
    public function attendanceReportPage()
    {
        $employeeModel = new EmployeeModel();
        $employees = $employeeModel->where('isActive', 1)->orderBy('name', 'ASC')->findAll();

        return view('admin/attendance/report', [
            'employees' => $employees,
            'statuses'  => ['Present', 'Absent', 'Half Day', 'Leave'],
        ]);
    }

    /**
     * Get attendance report data
     */
    public function getAttendanceReport()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $startDate  = $this->request->getPost('start_date');
        $endDate    = $this->request->getPost('end_date');
        $employeeId = $this->request->getPost('employee_id');
        $status     = $this->request->getPost('status');

        if (!$startDate || !$endDate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select date range'
            ]);
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Start date must be before end date'
            ]);
        }

        $query = $this->attendanceModel
                    ->select('attendance.*, employee.name as employee_name, employee.jobTitle, employee.salary')
                    ->join('employee', 'employee.employeeId = attendance.employee_id', 'left')
                    ->where('attendance_date >=', $startDate)
                    ->where('attendance_date <=', $endDate);

        if ($employeeId) {
            $query->where('attendance.employee_id', $employeeId);
        }

        if ($status) {
            $query->where('attendance.status', $status);
        }

        $records = $query->orderBy('attendance.attendance_date', 'DESC')
                        ->orderBy('employee.name', 'ASC')
                        ->findAll();

        // --- Salary summary calculation ---
        $presentDays = $absentDays = $halfDays = $leaveDays = 0;
        $totalPayable = 0;
        $salaryPerDay = $salaryPerHour = 0;
        $bonus = 0;
        $deductions = 1000;
        $previousSalary = 8673;
        $advanceLoan = 0;

        if ($employeeId && !empty($records)) {
            $monthlySalary = $records[0]['salary'] ?? 0;
            $daysInMonth   = date('t', strtotime($startDate));
            $salaryPerDay  = $monthlySalary / $daysInMonth;
            $salaryPerHour = $salaryPerDay / 8;

            foreach ($records as &$record) {
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
                        $record['payable'] = $salaryPerDay;
                        break;
                }
                $totalPayable += $record['payable'];
            }
        }

        $finalSalary = ($totalPayable + $bonus + $previousSalary) - ($deductions + $advanceLoan);

        return $this->response->setJSON([
            'success' => true,
            'data'    => [
                'attendance' => $records,
                'summary' => [
                    'presentDays'    => $presentDays,
                    'absentDays'     => $absentDays,
                    'halfDays'       => $halfDays,
                    'leaveDays'      => $leaveDays,
                    'salaryPerDay'   => $salaryPerDay,
                    'salaryPerHour'  => $salaryPerHour,
                    'totalPayable'   => $totalPayable,
                    'bonus'          => $bonus,
                    'deductions'     => $deductions,
                    'advanceLoan'    => $advanceLoan,
                    'previousSalary' => $previousSalary,
                    'finalSalary'    => $finalSalary,
                ]
            ],
            'count'   => count($records),
        ]);
    }

    /**
     * Export attendance report to CSV
     */
    public function exportAttendanceReport()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $employeeId = $this->request->getGet('employee_id');

        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Invalid date range');
        }

        $records = $this->attendanceModel
                       ->getAttendanceByDateRange($startDate, $endDate, $employeeId);

        // Prepare CSV
        $filename = 'attendance_report_' . date('Y-m-d_H-i-s') . '.csv';
        $csv = "Employee Name,Date,Check In,Check Out,Status,Remarks\n";

        foreach ($records as $record) {
            $csv .= '"' . $record['employee_name'] . '",'
                  . '"' . $record['attendance_date'] . '",'
                  . '"' . ($record['check_in_time'] ?? '') . '",'
                  . '"' . ($record['check_out_time'] ?? '') . '",'
                  . '"' . $record['status'] . '",'
                  . '"' . str_replace('"', '""', $record['remarks'] ?? '') . '"' . "\n";
        }

        // Send as download
        return $this->response
                    ->setHeader('Content-Type', 'text/csv')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setBody($csv);
    }

    /**
     * Display monthly attendance page
     */
    public function monthlyAttendancePage()
    {
        $employeeModel = new EmployeeModel();
        $employees = $employeeModel->where('isActive', 1)->orderBy('name', 'ASC')->findAll();

        $currentMonth = date('m');
        $currentYear = date('Y');

        return view('admin/attendance/monthly', [
            'employees' => $employees,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * Get monthly attendance data for employee
     */
    public function getMonthlyAttendance()
    {
        //if (!$this->request->is('ajax')) {
        //    return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        //}

        $employeeId = $this->request->getPost('employee_id');
        $month = $this->request->getPost('month');
        $year = $this->request->getPost('year');

        if (!$employeeId || !$month || !$year) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select employee, month and year'
            ]);
        }

        $records = $this->attendanceModel->getMonthlyAttendance($employeeId, str_pad($month, 2, '0', STR_PAD_LEFT), $year);
        $summary = $this->attendanceModel->getMonthlyAttendanceSummary($employeeId, str_pad($month, 2, '0', STR_PAD_LEFT), $year);

        return $this->response->setJSON([
            'success' => true,
            'records' => $records,
            'summary' => $summary,
        ]);
    }

    /**
     * Display attendance history for an employee
     */
    public function employeeAttendanceHistory($employeeId = null)
    {
        if (!$employeeId) {
            $employeeId = session()->get('employeeId'); // fallback to logged-in user
            //return redirect()->back()->with('error', 'Invalid employee');
        }

        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->find($employeeId);

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found');
        }

        $page = $this->request->getGet('page') ?? 1;
        $perPage = 15;

        $records = $this->attendanceModel
                       ->where('employee_id', $employeeId)
                       ->orderBy('attendance_date', 'DESC')
                       ->paginate($perPage);

        $pager = $this->attendanceModel->pager;

        return view('admin/attendance/history', [
            'employee' => $employee,
            'records'  => $records,
            'pager'    => $pager,
        ]);
    }

    /**
     * Get today's attendance statistics for dashboard
     */
    public function getTodayStats()
    {
        if (!$this->request->is('ajax')) {
            return $this->response->setJSON(['success' => false]);
        }

        $employeeModel = new EmployeeModel();
        $totalEmployees = $employeeModel->where('isActive', 1)->countAllResults();
        $todayStats = $this->attendanceModel->getTodayAttendanceStats();

        return $this->response->setJSON([
            'success' => true,
            'total_employees' => $totalEmployees,
            'present_today'   => $todayStats['present'],
            'absent_today'    => $todayStats['absent'],
            'leave_today'     => $todayStats['leave'],
            'half_day_today'  => $todayStats['half_day'],
        ]);
    }

    /**
     * Update attendance record
     */
    public function updateAttendance()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        $id = $this->request->getPost('id');
        $attendanceData = [
            'status' => $this->request->getPost('status'),
            'check_in_time' => $this->request->getPost('check_in_time') ?: null,
            'check_out_time' => $this->request->getPost('check_out_time') ?: null,
            'remarks' => $this->request->getPost('remarks') ?: null,
        ];

        if (!$id || !$this->attendanceModel->update($id, $attendanceData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update attendance'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Attendance updated successfully'
        ]);
    }

    /**
     * Delete attendance record
     */
    public function deleteAttendance()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        $id = $this->request->getPost('id');

        if (!$id || !$this->attendanceModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete attendance'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Attendance deleted successfully'
        ]);
    }

    public function allData()
    {
        $rows = $this->dataModel->findAllWithTelecaller();
        return view('admin/all_data', ['rows' => $rows]);
    }

    /**
     * Display expiry data page (records are loaded in chunks via AJAX)
     */
    public function expiryData()
    {
        return view('admin/expiry_data');
    }

    /**
     * Server-side API for the expiry data DataTable.
     * Returns only the requested chunk (page) of records so the page
     * loads fast even with 10,000+ rows.
     */
    public function expiryDataApi()
    {
        // DataTables server-side parameters
        $draw      = (int) ($this->request->getVar('draw') ?? 1);
        $start     = max(0, (int) ($this->request->getVar('start') ?? 0));
        $length    = (int) ($this->request->getVar('length') ?? 25);
        $searchRaw = trim((string) ($this->request->getVar('search')['value'] ?? ''));

        if ($length < 1 || $length > 500) {
            $length = 25;
        }

        // Map the clicked column index to a real DB column
        $columns = [
            0 => 'expirydata.id',
            1 => 'expirydata.regNumber',
            2 => 'expirydata.expiryDate',
            3 => 'employee.name',
            4 => 'expirydata.status',
        ];
        $orderColIndex = (int) ($this->request->getVar('order')[0]['column'] ?? 0);
        $orderColumn   = $columns[$orderColIndex] ?? 'expirydata.id';
        $orderDir      = strtolower((string) ($this->request->getVar('order')[0]['dir'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';

        try {
            $rows = $this->expiryDataModel->getPaginatedWithEmployee($length, $start, $searchRaw, $orderColumn, $orderDir);

            $totalFiltered = $searchRaw !== ''
                ? $this->expiryDataModel->countFilteredWithEmployee($searchRaw)
                : $this->expiryDataModel->countAllWithEmployee();

            $data = [];
            foreach ($rows as $row) {
                $rowStatus = (int) ($row['status'] ?? 0);
                if ($rowStatus === 1) {
                    $badge = '<span class="badge bg-success">Completed</span>';
                } elseif ($rowStatus === 2) {
                    $badge = '<span class="badge bg-secondary">Skipped</span>';
                } else {
                    $badge = '<span class="badge bg-warning text-dark">Pending</span>';
                }

                $employeeCell = esc($row['employeeName'] ?? '');
                if (empty($row['employeeName'])) {
                    $employeeCell .= ' <span class="text-muted">' . esc($row['employeeId']) . '</span>';
                }

                $data[] = [
                    'id'         => esc($row['id']),
                    'regNumber'  => esc($row['regNumber']),
                    'expiryDate' => esc($row['expiryDate']),
                    'employee'   => $employeeCell,
                    'status'     => $badge,
                ];
            }

            return $this->response->setJSON([
                'draw'            => $draw,
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'expiryDataApi error: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw'            => $draw,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => 'Failed to load expiry data',
            ]);
        }
    }

    /**
     * Export all expiry data records to a CSV file (Excel compatible)
     */
    public function exportExpiryData()
    {
        $rows = $this->expiryDataModel->findAllWithEmployee();

        $filename = 'expiry_data_' . date('Y-m-d_H-i-s') . '.csv';

        // UTF-8 BOM so Excel opens the file with correct encoding
        $csv = "\xEF\xBB\xBF";
        $csv .= "ID,Reg Number,Expiry Date,Employee ID,Employee Name,Status
";

        foreach ($rows as $row) {
            $rowStatus = (int) ($row['status'] ?? 0);
            if ($rowStatus === 1) {
                $status = 'Completed';
            } elseif ($rowStatus === 2) {
                $status = 'Skipped';
            } else {
                $status = 'Pending';
            }

            $csv .= '"' . str_replace('"', '""', (string) ($row['id'] ?? '')) . '",'
                  . '"' . str_replace('"', '""', (string) ($row['regNumber'] ?? '')) . '",'
                  . '"' . str_replace('"', '""', (string) ($row['expiryDate'] ?? '')) . '",'
                  . '"' . str_replace('"', '""', (string) ($row['employeeId'] ?? '')) . '",'
                  . '"' . str_replace('"', '""', (string) ($row['employeeName'] ?? '')) . '",'
                  . '"' . $status . '"' . "
";
        }

        return $this->response
                    ->setHeader('Content-Type', 'text/csv; charset=utf-8')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setBody($csv);
    }
    public function exportCallingData(){
        helper('excel');

        // Fetch ALL records from the "data" table with telecaller names
        $rows = $this->dataModel
            ->select('data.*, employee.name as telecallerName')
            ->join('employee', 'employee.employeeId = data.telecaller', 'left')
            ->orderBy('data.recordId', 'ASC')
            ->findAll();

        $headers = [
            'Record ID',
            'Reg Date',
            'Reg Month',
            'Reg Number',
            'Owner Name',
            'Address',
            'Vehicle Maker',
            'Vehicle Model',
            'Fuel Type',
            'Sale Amount',
            'Seat Capacity',
            'Cubic Capacity',
            'Mobile',
            'Expiry Date',
            'Previous Insurance Company',
            'Finance',
            'Telecaller',
            'Data Upload Date',
            'Action Taken',
            'Is Important',
            'Interested',
            'Already Sale',
            'Sale In GB'
        ];

        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                $row['recordId'] ?? '',
                $row['regDate'] ?? '',
                $row['regDateMonth'] ?? '',
                $row['regNumber'] ?? '',
                $row['ownerName'] ?? '',
                $row['address'] ?? '',
                $row['vehicleMaker'] ?? '',
                $row['vehicleModel'] ?? '',
                $row['fuelType'] ?? '',
                $row['saleAmt'] ?? '',
                $row['seatCapacity'] ?? '',
                $row['cubicCapacity'] ?? '',
                $row['mobile'] ?? '',
                $row['expiryDate'] ?? '',
                $row['prevInsuCompany'] ?? '',
                $row['finance'] ?? '',
                $row['telecallerName'] ?? ($row['telecaller'] ?? ''),
                $row['dataUploadDate'] ?? '',
                !empty($row['actionTaken']) ? 'Yes' : 'No',
                !empty($row['isImportant']) ? 'Yes' : 'No',
                !empty($row['isIntrested']) ? 'Yes' : 'No',
                !empty($row['alreadySale']) ? 'Yes' : 'No',
                !empty($row['saleInGb']) ? 'Yes' : 'No',
            ];
        }

        $filename = 'calling_data_' . date('Y-m-d_H-i-s');
        exportToExcel($data, $filename, $headers);
    }
    public function subscriptionDetails(){
        $db = \Config\Database::connect();

         $builder = $db->table('subscriptions');

        $today = date('Y-m-d');

        // 🔄 Update all expired subscriptions
        $builder->where('endDate <', $today)
                ->where('status !=', 'Expired')
                ->update(['status' => 'Expired']);

        $builder = $db->table('employee');
        $builder->select('employee.employeeId, employee.profilePhoto, employee.jobTitle, employee.name, employee.username, employee.password, employee.isActive, employee.hireDate,employee.gender, subscriptions.startDate, subscriptions.endDate, subscriptions.status, subscriptions.amount');
        $builder->join('subscriptions', 'subscriptions.employeeId = employee.employeeId', 'left'); 
        // use 'inner' if you only want employees who have subscriptions

        $query = $builder->get();
        $data['employees'] = $query->getResultArray();
        
        return view('admin/subscription/employee_subscriptions', $data);
       
    }


    public function uploadProfilePhoto()
    {
        $session = session();
        $employeeId = $this->request->getPost('employeeId');
     
        $file = $this->request->getFile('profilePhoto');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validate size (800 KB max) and type
            if ($file->getSize() > 800 * 1024) {
                return redirect()->back()->with('error', 'File too large. Max 800KB.');
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (! in_array($file->getMimeType(), $allowedTypes)) {
                return redirect()->back()->with('error', 'Invalid file type.');
            }

            // Generate unique filename
            $newName = $employeeId . '_' . time() . '.' . $file->getExtension();

            // Move file to public/uploads/profile
            $file->move(FCPATH . 'uploads/profile', $newName);

            // Update employee record
            $employeeModel = new EmployeeModel();
            $employeeModel->update($employeeId, [
                'profilePhoto' => $newName
            ]);

            if ($session->get('employeeId') === (string) $employeeId) {
                $session->set('profilePhoto', $newName);
            }
            return redirect()->back()->with('success', 'Profile photo updated successfully.');
        }
        return redirect()->back()->with('error', 'No file selected or upload failed.');
    }
    
    public function renewEmpSubscription()
    {
        $employeeIds = $this->request->getPost('employeeIds') ?: [];

        if (!is_array($employeeIds)) {
            $employeeIds = [$employeeIds];
        }

        $result = $this->subscriptionService->renewSubscriptions(
            $employeeIds,
            $this->request->getFile('paymentScreenshot')
        );

        if (!empty($result['error'])) {
            return redirect()->to('/admin/subscription')->with('error', $result['error']);
        }
                        
        return redirect()->to('/admin/subscription')->with('success', $result['success']);
    }

    

    public function editPolicyView($policy_id)
    {
        /*
        $policy_id = $this->request->getGet('policy_id');
        if (!$policy_id) {
            return redirect()->back()->with('error', 'Policy number missing.');
        }*/

        $policy = $this->policyModel->where('policy_id', $policy_id)->first();
        if (!$policy) {
            return redirect()->back()->with('error', 'Policy not found.');
        }

        $expiryDate = $policy['expiry_date'] ?? null;
        $status = 'Unknown';
        $countdown = null;

        if ($expiryDate) {
            $today = new DateTime();
            $expiry = new DateTime($expiryDate);

            if ($expiry >= $today) {
                $status = 'Active';
                $interval = $today->diff($expiry);
                $countdown = $interval->days . ' days left';
            } else {
                $status = 'Expired';
            }
        }

        // Fetch only active employees
        $employees = $this->employeeModel->where('isActive', 1)->findAll();

        // telecaller is employeeId, so fetch employee record
        $telecaller = $this->employeeModel->find($policy['telecaller']);

        return view('admin/policy/editpolicy', [
            'policy'     => $policy,
            'employees'  => $employees,
            'telecaller' => $telecaller['name'] ?? '',   // pass employee name
            'status'     => $status,
            'countdown' => $countdown
        ]);
    }

public function previewPolicy($id)
{
    $policy = $this->policyModel->find($id);

    if (! $policy || empty($policy['file_path'])) {
        return $this->response->setStatusCode(404)
                              ->setBody('Policy file not found.');
    }

    $filePath = WRITEPATH . 'uploads/policies/' . basename($policy['file_path']);

    if (! file_exists($filePath)) {
        return $this->response->setStatusCode(404)
                              ->setBody('File not found on server.');
    }

    // Stream PDF inline
    return $this->response->setHeader('Content-Type', 'application/pdf')
                          ->setHeader('Content-Disposition', 'inline; filename="'.basename($filePath).'"')
                          ->setBody(file_get_contents($filePath));
}

    public function postUpdatePolicy()
    {
        $policyId = $this->request->getPost('policy_id');
        if (!$policyId) {
            return redirect()->back()->with('error', 'Policy ID missing.');
        }

        $data = [
            'holder_name'   => $this->request->getPost('holderName'),
            'policy_number' => $this->request->getPost('policyNumber'),
            'company_name'  => $this->request->getPost('companyName'),
            'vehicle_number'=> $this->request->getPost('vehicleNumber'),
            'mobileNo'      => $this->request->getPost('mobileNo'),
            'telecaller'    => $this->request->getPost('telecaller'), // employeeId
            'cashback'       => $this->request->getPost('cashback'),
            'premium'       => $this->request->getPost('premium'),
            'policyType'    => $this->request->getPost('policyType'),
            'issue_date'    => $this->request->getPost('issueDate'),
            'expiry_date'   => $this->request->getPost('expiryDate'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        // Perform update
        $this->policyModel->update($policyId, $data);

        // Correct redirect pathreturn 
        
        return redirect()->to('admin/edit-policy-view/' . $policyId)
                 ->with('success', 'Policy updated successfully');
    }

    public function searchCustomerAjax()
    {
        $keyword = $this->request->getGet('keyword');

        $result = $this->policyModel->groupStart()
                ->like('holder_name', $keyword)
                ->orLike('policy_number', $keyword)
                ->orLike('vehicle_number', $keyword)
                ->orLike('mobileNo', $keyword)
            ->groupEnd()
            ->limit(10)
            ->findAll();

        return $this->response->setJSON($result);
    }

public function exportAttendancePdf($employeeId, $startDate, $endDate)
{
    $employeeModel = new EmployeeModel();

    if ($employeeId == 0) {
        // Export all employees
        $employees = $employeeModel->where('isActive', 1)->findAll();
        $reports   = [];

        foreach ($employees as $emp) {
            $records = $this->getAttendanceWithWeeklyOffAndHolidays(
                $emp['employeeId'],
                $startDate,
                $endDate
            );

            $report = $this->buildReportData($emp, $records, $startDate);
            $report['weeklyOffDays'] = count(array_filter($report['attendance'], function ($r) {
                return isset($r['status']) && $r['status'] === 'Weekly Off';
            }));

            $reports[] = $report;
        }

        $html = view('admin/attendance/pdf_report_all', [
            'reports' => $reports,
            'period'  => [$startDate, $endDate],
        ]);
    } else {
        $employee = $employeeModel->find($employeeId);
        $records = $this->getAttendanceWithWeeklyOffAndHolidays(
            $employeeId,
            $startDate,
            $endDate
        );

        $report = $this->buildReportData($employee, $records, $startDate);
        $report['weeklyOffDays'] = count(array_filter($report['attendance'], function ($r) {
            return isset($r['status']) && $r['status'] === 'Weekly Off';
        }));

        $html = view('admin/attendance/pdf_report', [
            'report' => $report,
            'period' => [$startDate, $endDate],
        ]);
    }

    // Generate PDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("attendance-report-$employeeId.pdf");
}


    /**
     * Build attendance records including Weekly Off + Holidays
     */
    private function getAttendanceWithWeeklyOffAndHolidays($employeeId, $startDate, $endDate)
    {
        $attendanceModel = new AttendanceModel();

        // Fetch existing records
        $records = $attendanceModel
            ->where('employee_id', $employeeId)
            ->where('attendance_date >=', $startDate)
            ->where('attendance_date <=', $endDate)
            ->orderBy('attendance_date', 'ASC')
            ->findAll();

        // Index records by date
        $indexed = [];
        foreach ($records as $r) {
            $indexed[$r['attendance_date']] = $r;
        }

        // Weekly off (Sunday for all employees)
        $weeklyOffDays = ['Sunday'];

        // Example holiday list (better to keep in DB)
        $holidays = [
            '2026-08-15' => 'Independence Day',
            '2026-10-02' => 'Gandhi Jayanti',
            '2026-12-25' => 'Christmas',
        ];

        $start = new \DateTime($startDate);
        $end   = new \DateTime($endDate);
        $allRecords = [];

        while ($start <= $end) {
            $date    = $start->format('Y-m-d');
            $dayName = $start->format('l');

            if (isset($indexed[$date])) {
                $allRecords[] = $indexed[$date];
            } elseif (in_array($dayName, $weeklyOffDays)) {
                $allRecords[] = [
                    'attendance_date' => $date,
                    'status'          => 'Weekly Off',
                    'check_in_time'   => null,
                    'check_out_time'  => null,
                    'remarks'         => 'Weekly holiday',
                ];
            } elseif (array_key_exists($date, $holidays)) {
                $allRecords[] = [
                    'attendance_date' => $date,
                    'status'          => 'Holiday',
                    'check_in_time'   => null,
                    'check_out_time'  => null,
                    'remarks'         => $holidays[$date],
                ];
            } else {
                $allRecords[] = [
                    'attendance_date' => $date,
                    'status'          => 'Absent',
                    'check_in_time'   => null,
                    'check_out_time'  => null,
                    'remarks'         => 'No login',
                ];
            }

            $start->modify('+1 day');
        }

        return $allRecords;
    }

    /**
     * Helper to calculate salary + summary
     */
    private function buildReportData($employee, $records, $startDate)
    {
        $monthlySalary = $employee['salary'];
        $daysInMonth   = date('t', strtotime($startDate));
        $salaryPerDay  = $monthlySalary / $daysInMonth;
        $salaryPerHour = $salaryPerDay / 8;

        $presentDays = $absentDays = $halfDays = $leaveDays = $weeklyOffDays = 0;
        $totalPayable = 0;
        $weekWorked = [];

        foreach ($records as &$record) {
            $record['duration'] = '-';
            $record['duration_hours'] = 0;
            $record['week_key'] = null;

            if (!empty($record['attendance_date'])) {
                $dateTs = strtotime($record['attendance_date']);
                if ($dateTs !== false) {
                    $dayOfWeek = (int) date('w', $dateTs);
                    $record['week_key'] = date('Y-m-d', strtotime("-{$dayOfWeek} days", $dateTs));
                    if (in_array($record['status'], ['Present', 'Half Day', 'Leave'], true)) {
                        $weekWorked[$record['week_key']] = true;
                    } elseif (!array_key_exists($record['week_key'], $weekWorked)) {
                        $weekWorked[$record['week_key']] = false;
                    }
                }
            }

            if (!empty($record['check_in_time']) && !empty($record['check_out_time'])) {
                $start = strtotime($record['check_in_time']);
                $end = strtotime($record['check_out_time']);
                if ($start !== false && $end !== false) {
                    if ($end < $start) {
                        $end += 24 * 60 * 60;
                    }
                    $diff = $end - $start;
                    $hours = floor($diff / 3600);
                    $minutes = floor(($diff % 3600) / 60);
                    $record['duration_hours'] = $hours + ($minutes / 60);
                    $record['duration'] = sprintf('%dh %02dm', $hours, $minutes);
                }
            }
        }

        foreach ($records as &$record) {
            $weekHasWork = !empty($record['week_key']) && !empty($weekWorked[$record['week_key']]);

            switch ($record['status']) {
                case 'Present':
                    $presentDays++;
                    $workHours = $record['duration_hours'];
                    if ($workHours > 0) {
                        $record['payable'] = min($workHours, 8) * $salaryPerHour;
                    } else {
                        $record['payable'] = $salaryPerDay;
                    }
                    break;
                case 'Absent':
                    $absentDays++;
                    $record['payable'] = 0;
                    break;
                case 'Half Day':
                    $halfDays++;
                    $workHours = $record['duration_hours'];
                    if ($workHours > 0) {
                        $record['payable'] = min($workHours, 4) * $salaryPerHour;
                    } else {
                        $record['payable'] = $salaryPerDay / 2;
                    }
                    break;
                case 'Leave':
                    $leaveDays++;
                    $record['payable'] = $salaryPerDay;
                    break;
                case 'Weekly Off':
                    if ($weekHasWork) {
                        $weeklyOffDays++;
                        $record['payable'] = $salaryPerDay;
                    } else {
                        $record['payable'] = 0;
                    }
                    break;
                case 'Holiday':
                    $record['payable'] = $weekHasWork ? $salaryPerDay : 0;
                    break;
                default:
                    $record['payable'] = 0;
                    break;
            }
            $totalPayable += $record['payable'];
        }

        // Example extras (replace with DB values if available)
        $bonus          = 0;
        $deductions     = 0;
        $previousSalary = 0;
        $advanceLoan    = 0;
        $finalSalary    = ($totalPayable + $bonus + $previousSalary) - ($deductions + $advanceLoan);
        $gender         = $employee['gender'];

        return [
            'employee'       => $employee,
            'attendance'     => $records,
            'salaryPerDay'   => $salaryPerDay,
            'salaryPerHour'  => $salaryPerHour,
            'presentDays'    => $presentDays,
            'absentDays'     => $absentDays,
            'halfDays'       => $halfDays,
            'leaveDays'      => $leaveDays,
            'weeklyOffDays'  => $weeklyOffDays,
            'totalPayable'   => $totalPayable,
            'bonus'          => $bonus,
            'deductions'     => $deductions,
            'previousSalary' => $previousSalary,
            'advanceLoan'    => $advanceLoan,
            'finalSalary'    => $finalSalary,
            'gender'         => $gender
        ];
    }

    public function displayRecord($recordId = 0){
        $session       = session();
        $employeeModel = new EmployeeModel();
        $historyModel  = new HistoryModel();
        $dataModel     = new DataModel();
        
        // Check if employee is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/admin/login')->with('error', 'Please log in to access the dashboard');
        }
        $record = $dataModel
                ->where(['recordId' => $recordId])
                ->first();
        

        // If record found
        if ($record) {
            $data = $record; // use the whole row directly
            $data['name']        = $session->get('name');
            $data['historyData'] = $historyModel->where('recordId', $record['recordId'])->findAll();
            $data['alreadySale'] = 0;

            if (!empty($data['historyData'])) {
                foreach ($data['historyData'] as $row) {
                    if ($row['status'] === "Already Sale") {
                        $data['alreadySale'] = 1;
                        break;
                    }
                }
            }
            $data['telecallers'] = $employeeModel->where(['jobTitle' => 'Telecaller'],['isActive' => 1])->findAll();
            $data['isDataAvailable'] = true;
        } else {
            $data = ['isDataAvailable' => false];
        }

        return view('admin/lead', $data);
    }
    

    public function previousRecord($param = 0){
        $session = session();
        $dataModel     = new DataModel();
        $record = $dataModel
            ->where('recordId <', $param)
            ->orderBy('recordId', 'DESC')
            ->first();

        if ($record) {
            return redirect()->to('/admin/display-record/'.$record['recordId']);
        } 
        return redirect()->to('/admin/display-record/'.$param);
    }
    public function forwardRecord($param = 0){
            
        $session = session();
        $dataModel     = new DataModel();
            $record = $dataModel
            ->where('recordId >', $param)
            ->orderBy('recordId', 'ASC')
            ->first();
        if ($record) {
            return redirect()->to('/admin/display-record/'.$record['recordId']);
        } 
        return redirect()->to('/admin/display-record/'.$param);

    }

    public function changeOwnerPostAjax()
    {
        $recordId = $this->request->getPost('recordId');
        $newOwnerId = $this->request->getPost('telecallerId');
                    /*
        if (!$recordId || !$newOwnerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing parameters'
            ]);
        }*/
        if (!$recordId ) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing recordID'
            ]);
        }
        if (!$newOwnerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing new owner ID'
            ]);
        }

        $dataModel = new DataModel();
        $historyModel = new HistoryModel();

        // Update owner
        $updateSuccess = $dataModel->update($recordId, ['telecaller' => $newOwnerId]);

        if ($updateSuccess) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Owner changed successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to change owner'
            ]);
        }
    }

}
