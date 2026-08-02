<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\DataModel;
use App\Models\EmployeeLoginHistoryModel;
use App\Models\HistoryModel;
use App\Models\PolicyModel;
use App\Libraries\PolicyExtractor;
use CodeIgniter\I18n\Time;
use App\Models\UploadModel;

use DateTime;

class Employee extends BaseController
{
        protected $employeeModel;
        protected $policyModel;
        protected $uploadModel;
    
    public function __construct()
    {
        $this->policyModel = new PolicyModel();
        $this->employeeModel = new EmployeeModel();
        $this->uploadModel = new UploadModel();
    }    

    
    public function dashboard($recordId = null)
    {
        $session       = session();
        $employeeModel = new EmployeeModel();
        $historyModel  = new HistoryModel();
        $dataModel     = new DataModel();
        
        // Check if employee is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/employee/login')->with('error', 'Please log in to access the dashboard');
        }

        // If no recordId passed, fetch first available record for this telecaller
        if ($recordId === null) {
            $record = $dataModel
                ->where(['telecaller' => $session->get('employeeId'), 'actionTaken' => 0])
                ->first();
        } else {
            $record = $dataModel
                ->where(['telecaller' => $session->get('employeeId'), 'recordId' => $recordId])
                ->first();
        }

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

            $data['isDataAvailable'] = true;
        } else {
            $data = ['isDataAvailable' => false];
        }

        return view('employee/dashboard', $data);
    }

    /*
    
    public function dashboard($recordId = null)
    {   
        $employeeModel = new EmployeeModel();
        $historyModel = new HistoryModel();
        $dataModel = new DataModel();
        
        if($recordId === null){
            $record = $dataModel->where(array('telecaller'=>$session->get('employeeId'),'actionTaken'=>0))->first();
            if($record){
                $data = [
            'recordId'       => $record['recordId'],
            'regDate'        => $record['regDate'],
            'regDateMonth'   => $record['regDateMonth'],
            'regNumber'      => $record['regNumber'],
            'ownerName'      => $record['ownerName'],
            'address'        => $record['address'],
            'vehicleMaker'   => $record['vehicleMaker'],
            'vehicleModel'   => $record['vehicleModel'],
            'fuelType'       => $record['fuelType'],
            'saleAmt'        => $record['saleAmt'],
            'seatCapacity'   => $record['seatCapacity'],
            'mobile'         => $record['mobile'],
            'expiryDate'     => $record['expiryDate'],
            'prevInsuCompany'=> $record['prevInsuCompany'],
            'finance'        => $record['finance'],
            'telecaller'     => $record['telecaller'],
            'dataUploadDate' => $record['dataUploadDate'],
            'actionTaken'    => $record['actionTaken'],
            'isImportant'    => $record['isImportant'],
            'alreadySale'    => $record['alreadySale'],
            'modifiyDate'    => $record['modifiyDate'],
            'isIntrested'    => $record['isIntrested'],
            'saleInGb'       => $record['saleInGb']
            ];
            $data['name'] = $session->get('name');
            $data['historyData'] = $historyModel->where('recordId',$recordId)->findAll();
            $data['alreadySale'] = 0;
            if($data['historyData']){
                foreach($data['historyData'] as $row){
                       if($row['status']=="Already Sale"){
                        $data['alreadySale'] = 1;
                       } 
                }
            }   
            $data['isDataAvailable'] = true;
            return view('employee/dashboard', $data);
        }
            else{
                $data['isDataAvailable'] = false; 
                return view('employee/dashboard', $data);
            }
        }
        $session = session();
        
        // Check if employee is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/employee/login')->with('error', 'Please log in to access the dashboard');
        }

        $employeeId = session()->get('employeeId');
        
        $record = $dataModel->where(array('telecaller'=>$session->get('employeeId'),'recordId'=>$recordId))->first();
        
        if($record){
            $data = [
        'recordId'       => $record['recordId'],
        'regDate'        => $record['regDate'],
        'regDateMonth'   => $record['regDateMonth'],
        'regNumber'      => $record['regNumber'],
        'ownerName'      => $record['ownerName'],
        'address'        => $record['address'],
        'vehicleMaker'   => $record['vehicleMaker'],
        'vehicleModel'   => $record['vehicleModel'],
        'fuelType'       => $record['fuelType'],
        'saleAmt'        => $record['saleAmt'],
        'seatCapacity'   => $record['seatCapacity'],
        'mobile'         => $record['mobile'],
        'expiryDate'     => $record['expiryDate'],
        'prevInsuCompany'=> $record['prevInsuCompany'],
        'finance'        => $record['finance'],
        'telecaller'     => $record['telecaller'],
        'dataUploadDate' => $record['dataUploadDate'],
        'actionTaken'    => $record['actionTaken'],
        'isImportant'    => $record['isImportant'],
        'alreadySale'    => $record['alreadySale'],
        'modifiyDate'    => $record['modifiyDate'],
        'isIntrested'    => $record['isIntrested'],
        'saleInGb'       => $record['saleInGb']
    ];
            $data['name'] = $session->get('name');
            $data['historyData'] = $historyModel->where('recordId',$recordId)->findAll();
            $data['alreadySale'] = 0;
            if($data['historyData']){
                foreach($data['historyData'] as $row){
                       if($row['status']=="Already Sale"){
                        $data['alreadySale'] = 1;
                       } 
                }
            }   
            $data['isDataAvailable'] = true;
            return view('employee/dashboard', $data);
        }
        else{
            $data['isDataAvailable'] = false; 
            return view('employee/dashboard', $data);
            //return "No Data Found!";
        }     

    }*/
    
    public function uploadPolicyPost()
    {
        if (! $this->request->is('post')) {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        $session = session();
        $recordId = $this->request->getPost('recordId');
        $files = $this->request->getFiles();
        $results = [];
        $errors = [];
        $warnings = [];

        if (empty($files['pdfs'])) {
            return redirect()->to('/employee/dashboard/' . ($recordId ?: ''))->with('error', 'No files selected');
        }

        $pdfFiles = $files['pdfs'];
        if (! is_array($pdfFiles)) {
            $pdfFiles = [$pdfFiles];
        }

        $uploadPath = WRITEPATH . 'uploads/policies/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $dataModel = new DataModel();
        $record = null;
        if (!empty($recordId)) {
            $record = $dataModel->where('recordId', $recordId)->first();
        }

        $policyModel = new PolicyModel();
        $policyExtractor = new PolicyExtractor();

        foreach ($pdfFiles as $file) {
            if (! $file instanceof \CodeIgniter\HTTP\Files\UploadedFile || ! $file->isValid()) {
                $errors[] = ($file->getClientName() ?? 'File') . ' - File upload failed.';
                continue;
            }

            $fileExtension = strtolower($file->getClientExtension());
            if ($fileExtension !== 'pdf') {
                $errors[] = $file->getClientName() . ' - Invalid file type. Only PDF files are allowed.';
                continue;
            }

            $mimeType = $file->getClientMimeType() ?: $file->getMimeType();
            if ($mimeType !== 'application/pdf') {
                $errors[] = $file->getClientName() . ' - Invalid PDF format (MIME: ' . $mimeType . ').';
                continue;
            }

            try {
                $details = $policyExtractor->extractPolicyDetails($file->getTempName());

                if (empty($details['policyNumber'])) {
                    $errors[] = $file->getClientName() . ' - Could not extract policy number. PDF may be invalid or secured.';
                    continue;
                }

                $existingPolicy = $policyModel->getPolicyByDetails(
                    $details['policyNumber'],
                    $details['policyStart'],
                    $details['expiryDate']
                );

                if ($existingPolicy) {
                    $warnings[] = $file->getClientName() . ' already exists in database. Skipped.';
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
                    'mobileNo' => $record['mobile'] ?? '',
                    'telecaller' => $session->get('employeeId') ?: '',
                    'cashback' =>  0,
                    'premium' => $record['saleAmt'] ?? '',
                    'policyType' => '',
                    'issue_date' => $details['policyStart'],
                    'expiry_date' => $details['expiryDate'],
                    'file_path' => 'writable/uploads/policies/' . $newName,
                ];

                $insertId = $policyModel->insert($insertData);
                if ($insertId === false) {
                    $dbErrors = $policyModel->errors();
                    $errors[] = $file->getClientName() . ' - Database error: ' . implode(' ', $dbErrors);
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

        //$redirectUrl = '/employee/dashboard/' . ($recordId ?: '');
        $redirectUrl = '/employee/policies-sold';
        
        if (empty($results) && ! empty($errors)) {
            return redirect()->to($redirectUrl)->with('error', implode(' | ', $errors));
        }

        $redirect = redirect()->to($redirectUrl)->with('success', 'Policy uploaded successfully.');
        if (! empty($errors)) {
            $redirect = $redirect->with('error', implode(' | ', $errors));
        }
        if (! empty($warnings)) {
            $redirect = $redirect->with('warning', implode(' | ', $warnings));
        }

        return $redirect;
    }

    public function uploadPolicyPostAjax()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $session = session();
        $recordId = $this->request->getPost('recordId');
        $files = $this->request->getFiles();
        $results = [];
        $errors = [];
        $warnings = [];

        if (empty($files['pdfs'])) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'No files selected']);
        }

        $pdfFiles = $files['pdfs'];
        if (! is_array($pdfFiles)) {
            $pdfFiles = [$pdfFiles];
        }

        $uploadPath = WRITEPATH . 'uploads/policies/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $dataModel = new DataModel();
        $record = null;
        if (!empty($recordId)) {
            $record = $dataModel->where('recordId', $recordId)->first();
        }

        $policyModel = new PolicyModel();
        $policyExtractor = new PolicyExtractor();

        foreach ($pdfFiles as $file) {
            if (! $file instanceof \CodeIgniter\HTTP\Files\UploadedFile || ! $file->isValid()) {
                $errors[] = ($file->getClientName() ?? 'File') . ' - File upload failed.';
                continue;
            }

            $fileExtension = strtolower($file->getClientExtension());
            if ($fileExtension !== 'pdf') {
                $errors[] = $file->getClientName() . ' - Invalid file type. Only PDF files are allowed.';
                continue;
            }

            $mimeType = $file->getClientMimeType() ?: $file->getMimeType();
            if ($mimeType !== 'application/pdf') {
                $errors[] = $file->getClientName() . ' - Invalid PDF format (MIME: ' . $mimeType . ').';
                continue;
            }

            try {
                $details = $policyExtractor->extractPolicyDetails($file->getTempName());

                if (empty($details['policyNumber'])) {
                    $errors[] = $file->getClientName() . ' - Could not extract policy number. PDF may be invalid or secured.';
                    continue;
                }

                $existingPolicy = $policyModel->getPolicyByDetails(
                    $details['policyNumber'],
                    $details['policyStart'],
                    $details['expiryDate']
                );

                if ($existingPolicy) {
                    $warnings[] = $file->getClientName() . ' already exists in database. Skipped.';
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
                    'mobileNo' => $record['mobile'] ?? '',
                    'cashback' => 0,
                    'telecaller' => $session->get('employeeId') ?: '',
                    'premium' => $record['saleAmt'] ?? '',
                    'policyType' => '',
                    'issue_date' => $details['policyStart'],
                    'expiry_date' => $details['expiryDate'],
                    'file_path' => 'writable/uploads/policies/' . $newName,
                ];

                $insertId = $policyModel->insert($insertData);
                if ($insertId === false) {
                    $dbErrors = $policyModel->errors();
                    $errors[] = $file->getClientName() . ' - Database error: ' . implode(' ', $dbErrors);
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

        if (! empty($results)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Policy uploaded successfully.'
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'success' => false,
            'message' => ! empty($errors) ? implode(' | ', $errors) : 'Policy upload failed.'
        ]);
    }

    public function save(){

        
        $historyModel = new HistoryModel();
        $dataModel = new DataModel();
        $alreadySale = 0;
        $isIntrested = 0;
        $saleInGb = 0;
        if($this->request->getVar('status')== "Already Sale"){
            $alreadySale = 1;
            
        }else{
        
            if($this->request->getVar('status')== "Intrested - Quote Sent"){
                $isIntrested = 1;
            }
            if($this->request->getVar('status')== "Not Intrested"){
                $isIntrested = 2;
            }
            if($this->request->getVar('status')== "Sale In GB"){
                $saleInGb = 1;
            }
        }
        $data = [
            'status'   => $this->request->getVar('status'),
            'remark'   => $this->request->getVar('remark'),
            'recordId' => $this->request->getVar('recordId')
        ];
        
        $historyModel->save($data);
        //return $alreadySale;
        $todayDate = date("Y-m-d");
        $data1 = [
            'actionTaken' => 1,
            'alreadySale' => $alreadySale,
            'modifiyDate' => $todayDate,
            'isIntrested' => $isIntrested,
            'saleInGb'    => $saleInGb
        ];
        $dataModel->update($this->request->getVar('recordId'),$data1);
        //return redirect()->to('/dashboard');
        return redirect()->to('/employee/nextRecord/'.$this->request->getVar('recordId'));
    
    }

    public function saveAjax()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Please log in']);
        }

        $historyModel = new HistoryModel();
        $dataModel = new DataModel();
        $recordId = $this->request->getPost('recordId');
        $status = $this->request->getPost('status');
        $remark = $this->request->getPost('remark');

        $alreadySale = ($status === 'Already Sale') ? 1 : 0;
        $isIntrested = 0;
        $saleInGb = 0;

        if ($status === 'Intrested - Quote Sent') {
            $isIntrested = 1;
        } elseif ($status === 'Not Intrested') {
            $isIntrested = 2;
        } elseif ($status === 'Sale In GB') {
            $saleInGb = 1;
        }

        $historyModel->save([
            'status' => $status,
            'remark' => $remark,
            'recordId' => $recordId
        ]);

        $dataModel->update($recordId, [
            'actionTaken' => 1,
            'alreadySale' => $alreadySale,
            'modifiyDate' => date('Y-m-d'),
            'isIntrested' => $isIntrested,
            'saleInGb' => $saleInGb
        ]);

        $nextRecord = $dataModel->where([
            'telecaller' => $session->get('employeeId'),
            'actionTaken' => 0
        ])->first();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Lead saved successfully.',
            'nextRecordId' => $nextRecord['recordId'] ?? null
        ]);
    }

    public function toggleStarAjax()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Please log in']);
        }

        $recordId = $this->request->getPost('recordId');
        $flag = (int) $this->request->getPost('flag');
        $dataModel = new DataModel();
        $dataModel->update($recordId, ['isImportant' => $flag]);

        return $this->response->setJSON([
            'success' => true,
            'starred' => (bool) $flag,
            'message' => $flag ? 'Marked as star record.' : 'Removed from star records.'
        ]);
    }
    
    public function policiesSold()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/employee/login')->with('error', 'Please log in to access this page');
        }

        $policyModel = new PolicyModel();
        $startOfMonth = date('Y-m-01 00:00:00');
        $endOfMonth   = date('Y-m-t 23:59:59');

        $policies = $policyModel
            ->where('telecaller', $session->get('employeeId'))
            ->where('created_at >=', $startOfMonth)
            ->where('created_at <=', $endOfMonth)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $policyCount = count($policies);

        return view('employee/policies_sold', [
            'policies' => $policies,
            'policyCount' => $policyCount,
        ]);
    }

    public function allData(){
        $session = session();
        $db = db_connect();
        $dataModel = new DataModel();
        $data['allData'] = $dataModel->where('telecaller',$session->get('employeeId'))->findAll();
        
            
        return view('employee/allData',$data);    
    }
    
    public function nextRecord($param = 0)
    {
     
        $session    = session();
        $dataModel  = new DataModel();
        // Find the next record for this telecaller with recordId greater than $param
        $record =  $dataModel->where([
                'telecaller' => $session->get('employeeId'), // or nickName
                'actionTaken' => 0
            ])->first();


        if ($record) {
            return redirect()->to('/employee/dashboard/'.$record['recordId']);
        }

        // No next record found → redirect to dashboard without changing ID
        return redirect()->to('/employee/dashboard/'.$param)
                        ->with('error', 'No next record found');
    }
    
    public function starRecord($recordId=0,$flag=0){
            

        $getLink = service('uri');
         
        
        $session = session();
        helper(['form']);
        $dataModel = new DataModel();
        
        $data = [
        'isImportant' => (int) $flag
        ];
       $dataModel->update($recordId,$data);
     
       return redirect()->to('/employee/dashboard/'.$recordId);
    }

    public function allStarRecord(){
        $session = session();
        $dataModel = new DataModel();
        $data['allData'] = $dataModel->where(array('telecaller'=>$session->get('employeeId'),'isImportant'=>1))->findAll();
        return view('employee/allStarRecord',$data);    
    }
     public function viewEmployee($id)
    {
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->find($id);

        if (! $employee) {
            return redirect()->to('/admin/employees')->with('error', 'Employee not found');
        }

        return view('employee/viewemployee', ['employee' => $employee]);
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
            $path = '/employee/' . $employeeId;
            return redirect()->to($path)->with('success', 'Employee updated successfully');
    }

    public function downloadPolicy($policyId)
    {
        $policy = $this->policyModel->find($policyId);

        if (! $policy) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Policy not found');
        }

        $filePath = null;
        if (!empty($policy['file_path'])) {
            $path = $policy['file_path'];
            if (preg_match('#^writable[\\/]+#i', $path)) {
                $path = preg_replace('#^writable[\\/]+#i', '', $path);
                $filePath = WRITEPATH . $path;
            } elseif (strpos($path, FCPATH) === 0) {
                $filePath = $path;
            } else {
                $filePath = FCPATH . ltrim($path, '/');
            }
        }

        if (! $filePath || ! file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        return $this->response->download($filePath, null)->setFileName(basename($filePath));
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

        return view('employee/editpolicy', [
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

        $filePath = null;
        if (!empty($policy['file_path'])) {
            $path = $policy['file_path'];
            if (preg_match('#^writable[\\/]+#i', $path)) {
                $path = preg_replace('#^writable[\\/]+#i', '', $path);
                $filePath = WRITEPATH . $path;
            } elseif (strpos($path, FCPATH) === 0) {
                $filePath = $path;
            } else {
                $filePath = FCPATH . ltrim($path, '/');
            }
        }

        if (! $filePath || ! file_exists($filePath)) {
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
            'cashback'      => $this->request->getPost('cashback'),
            'telecaller'    => $this->request->getPost('telecaller'), // employeeId
            'premium'       => $this->request->getPost('premium'),
            'policyType'    => $this->request->getPost('policyType'),
            'issue_date'    => $this->request->getPost('issueDate'),
            'expiry_date'   => $this->request->getPost('expiryDate'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        // Perform update
        $this->policyModel->update($policyId, $data);

        // Correct redirect pathreturn 
        
        return redirect()->to('employee/edit-policy-view/' . $policyId)
                 ->with('success', 'Policy updated successfully');
    }

    public function previousRecord($param = 0){
        $session = session();
        $dataModel     = new DataModel();
        $record = $dataModel
            ->where('telecaller', $session->get('employeeId'))
            ->where('recordId <', $param)
            ->orderBy('recordId', 'DESC')
            ->first();

        if ($record) {
            return redirect()->to('/employee/dashboard/'.$record['recordId']);
        } 
        return redirect()->to('/employee/dashboard/'.$param);
    }
    public function forwardRecord($param = 0){
            
        $session = session();
        $dataModel     = new DataModel();
            $record = $dataModel
            ->where('telecaller', $session->get('employeeId'))
            ->where('recordId >', $param)
            ->orderBy('recordId', 'ASC')
            ->first();
        if ($record) {
            return redirect()->to('/employee/dashboard/'.$record['recordId']);
        } 
        return redirect()->to('/employee/dashboard/'.$param);

    }

    
}