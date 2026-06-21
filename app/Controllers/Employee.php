<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\DataModel;
use App\Models\EmployeeLoginHistoryModel;
use App\Models\HistoryModel;

class Employee extends BaseController
{
    
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


}