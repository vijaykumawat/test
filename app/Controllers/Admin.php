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
    
    public function __construct()
    {
        $this->policyModel = new PolicyModel();
        $this->policyExtractor = new PolicyExtractor();
        $this->ocrProcessor = new OCRProcessor();
        $this->dataModel = new DataModel();
        $this->attendanceModel = new AttendanceModel();     
        $this->historyModel = new HistoryModel();
        $this->employeeModel = new EmployeeModel();
    }

    /**
     * Display admin dashboard
     */
     public function index()
    {   
        $db = \Config\Database::connect();
        $builder = $db->table('employee');
        $builder->select('employee.employeeId, employee.profilePhoto, employee.name,employee.gender, subscriptions.endDate, subscriptions.status');
        $builder->join('subscriptions', 'subscriptions.employeeId = employee.employeeId', 'left'); 

        $query = $builder->get();
        $employees = $query->getResultArray();

        // Add daysRemaining field
        foreach ($employees as &$emp) {
            if (!empty($emp['endDate'])) {
                $endDate = strtotime($emp['endDate']);
                $today   = strtotime(date('Y-m-d'));
                $emp['daysRemaining'] = ceil(($endDate - $today) / (60 * 60 * 24));
            } else {
                $emp['daysRemaining'] = null;
            }
        }

        $data['employees'] = $employees;
        // Total policies
        $data['totalPolicies'] = $this->policyModel->countAllResults();

        // Total data
        $data['totalData'] = $this->dataModel->countAllResults();
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

    /**
     * API endpoint for search with pagination
     */
    public function searchPolicyApi()
    {
        $search  = $this->request->getVar('q') ?? '';
        $page    = (int)($this->request->getVar('page') ?? 1);
        $perPage = (int)($this->request->getVar('per_page') ?? 25);

        if ($perPage === 0 || $perPage > 200) {
            $perPage = 25;
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
        $search = $this->request->getVar('q') ?? '';
        $page = (int)($this->request->getVar('page') ?? 1);
        $perPage = (int)($this->request->getVar('per_page') ?? 25);

        if ($perPage === 0 || $perPage > 200) {
            $perPage = 25;
        }

        $offset = ($page - 1) * $perPage;
        $cache = \Config\Services::cache();
        $countCacheKey = 'expired_next_month_count';

        $total = $cache->get($countCacheKey);
        if ($total === null) {
            $total = $this->policyModel->countExpiredNextMonth();
            $cache->save($countCacheKey, $total, 0);
        }

        $policies = $this->policyModel->getExpiredNextMonth($perPage, $offset);

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

    /**
     * Handle image OCR extraction
     */
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

    private function purchaseSubscription($img, array $employeeData)
    {
    $image = $img;
    if (! $image || ! $image->isValid()) {
        return ['success' => false, 'message' => 'Please upload a valid image file.'];
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
    $ocrResult = $this->runOcr($imagePath);
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

    // Block upload if table already has data
    $count = $this->dataModel->countAll();
    if ($count >= 1) {
        return $this->response->setStatusCode(400)
            ->setJSON(['success' => false, 'message' => 'Data table already has records. Please clear before uploading.']);
    }

    // DB fields that the user can map (exclude recordId from mapping)
    $dbFields = [
        'regDate','regDateMonth','regNumber','ownerName',
        'address','vehicleMaker','vehicleModel','fuelType','saleAmt',
        'seatCapacity','mobile','expiryDate','prevInsuCompany','finance','telecaller'
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
        $rowData['recordId'] = $this->generateRecordId();

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

    try {
        $builder = $this->dataModel->db->table('data');
        $builder->insertBatch($rows);
    } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON(['success'=>false,'message'=>'DB insert failed: '.$e->getMessage()]);
    }

    return $this->response->setJSON(['success'=>true,'message'=>'Data uploaded successfully']);
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
        
        $res = $this->purchaseSubscription(
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
            // Check if attendance already exists
            if ($this->attendanceModel->attendanceExists($employeeId, $attendanceDate)) {
                $skippedCount++;
                continue;
            }

            $attendanceData = [
                'employee_id'     => $employeeId,
                'attendance_date' => $attendanceDate,
                'status'          => $this->request->getPost("status_$employeeId") ?? 'Present',
                'check_in_time'   => $this->request->getPost("check_in_$employeeId") ?: null,
                'check_out_time'  => $this->request->getPost("check_out_$employeeId") ?: null,
                'remarks'         => $this->request->getPost("remarks_$employeeId") ?: null,
            ];

            if (!$this->attendanceModel->insert($attendanceData)) {
                $errors[] = "Failed to save attendance for employee $employeeId";
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

        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $employeeId = $this->request->getPost('employee_id');
        $status = $this->request->getPost('status');

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
                      ->select('attendance.*, employee.name as employee_name, employee.jobTitle')
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

        return $this->response->setJSON([
            'success' => true,
            'data'    => $records,
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
        if (!$this->request->is('ajax')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

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
            return redirect()->back()->with('error', 'Invalid employee');
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
        // Fetch all rows from the data table
        $rows = $this->dataModel->findAll();

        // Pass them to the view
        return view('admin/all_data', ['rows' => $rows]);
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
        $employeeId = $this->request->getPost('employeeId');

        $image = $this->request->getFile('paymentScreenshot');
        if (! $image || ! $image->isValid()) {
            return redirect()->to('/admin/subscription')
                            ->with('error', 'Please upload a valid image file.');
        }

        $extension = strtolower($image->getClientExtension() ?: pathinfo($image->getClientName(), PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg','jpeg','png','webp','bmp','gif'];
        if (! in_array($extension, $allowedExtensions)) {
            return redirect()->to('/admin/subscription')
                            ->with('error', 'Only image files are allowed (jpg, png, webp, bmp, gif).');
        }

        $uploadPath = FCPATH . 'uploads/receipts/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = $image->getRandomName();
        if (! $image->move($uploadPath, $newName)) {
            return redirect()->to('/admin/subscription')
                            ->with('error', 'Failed to save uploaded image.');
        }

        $imagePath = $uploadPath . $newName;
        $ocrResult = $this->runOcr($imagePath);
        if (! empty($ocrResult['error'])) {
            return redirect()->to('/admin/subscription')
                            ->with('error', 'OCR failed: ' . $ocrResult['error']);
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

      
        if (!$receiverValid || !$dateValid) {
            return redirect()->to('/admin/subscription')
                            ->with('error', 'Wrong screenshot attached.');
        }

        $subscriptionModel = new EmployeeSubscriptionModel();
        $baseDate = strtotime($dateText);

        $updateData = [
            'endDate'   => date('Y-m-d', strtotime('+1 month', $baseDate)),
            'status'    => 'Active',
            'updatedAt' => date('Y-m-d H:i:s')
        ];

        $subscriptionModel->where('employeeId', $employeeId)
                        ->set($updateData)
                        ->update();

        return redirect()->to('/admin/subscription')
                        ->with('success', 'Payment screenshot verified successfully. Subscription renewed.');
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

        // Fetch only active employees
        $employees = $this->employeeModel->where('isActive', 1)->findAll();

        // telecaller is employeeId, so fetch employee record
        $telecaller = $this->employeeModel->find($policy['telecaller']);

        return view('admin/policy/editpolicy', [
            'policy'     => $policy,
            'employees'  => $employees,
            'telecaller' => $telecaller['name'] ?? ''   // pass employee name
        ]);
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
   
}
