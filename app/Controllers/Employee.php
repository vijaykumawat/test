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

}