<?php

namespace App\Controllers;

use App\Models\PolicyModel;
use App\Models\DataModel;
use App\Models\AttendanceModel;
use App\Models\HistoryModel;
use App\Models\EmployeeModel;
use App\Libraries\PolicyExtractor;
use App\Libraries\OCRProcessor;
use App\Libraries\OcrHelper;
use App\Services\SubscriptionService;

class Admin extends BaseController
{
    protected $policyModel;
    protected $dataModel;
    protected $attendanceModel;
    protected $historyModel;
    protected $employeeModel;
    protected $policyExtractor;
    protected $ocrProcessor;

    public function __construct()
    {
        helper('file_helper'); // load file_helper
        $this->policyModel     = new PolicyModel();
        $this->dataModel       = new DataModel();
        $this->employeeModel   = new EmployeeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->historyModel    = new HistoryModel();
        $this->policyExtractor = new PolicyExtractor();
        $this->ocrProcessor    = new OCRProcessor();
    }

    /* ================= Dashboard ================= */
    public function index()
    {
        return view('admin/dashboard', [
            'employees'     => $this->employeeModel->getEmployeesWithSubscriptions(),
            'totalPolicies' => $this->policyModel->countAllResults(),
            'totalData'     => $this->dataModel->countAllResults()
        ]);
    }

    /* ================= Policy Management ================= */
    public function uploadPolicy()
    {
        return view('admin/uploadpolicy', [
            'results' => session()->getFlashdata('uploadResults') ?? []
        ]);
    }

    public function uploadPolicyPost()
    {
        $files = $this->request->getFiles()['pdfs'] ?? [];
        $results = []; $errors = [];

        foreach ($files as $file) {
            $upload = validateUpload($file, ['pdf'], WRITEPATH.'uploads/policies/');
            if (!$upload['success']) { $errors[] = $upload['message']; continue; }

            $details = $this->policyExtractor->extractPolicyDetails($upload['path']);
            
            if (empty($details['policy_number'])) { $errors[] = 'Policy number missing'; continue; }

            $this->policyModel->insert(array_merge($details, ['file_path'=>$upload['path']]));

             $results[] = [
            'fileName' => $file->getClientName(),
            'details'  => $details,
            'path'     => $upload['path'] ?? ''   // ✅ always set path
            ];
        }

        if (!empty($results)) cache()->delete('all_policies_count');
        return redirect()->to('/admin/upload')
            ->with('uploadResults',$results)
            ->with('error', implode(' | ', $errors));
    }

    public function searchPolicy() { return view('admin/searchpolicy'); }
    //public function searchPolicyApi() { return $this->response->setJSON($this->policyModel->findAll()); }
        public function searchPolicyApi()
    {
        $search = $this->request->getVar('q') ?? '';
        $page = (int)($this->request->getVar('page') ?? 1);
        $perPage = (int)($this->request->getVar('per_page') ?? 25);

        if ($perPage === 0 || $perPage > 200) {
            $perPage = 25;
        }

        $offset = ($page - 1) * $perPage;

        if (!empty($search)) {
            $policies = $this->policyModel->searchPolicies($search, $perPage, $offset);
            $total = $this->policyModel->countSearch($search);
        } else {
            $cache = \Config\Services::cache();
            $cacheKey = 'all_policies_count';
            $total = $cache->get($cacheKey);

            if ($total === null) {
                $total = $this->policyModel->countAllResults();
                $cache->save($cacheKey, $total, 0);
            }

            $policies = $this->policyModel->getAllPolicies($perPage, $offset);
        }

        $totalPages = $perPage ? ceil($total / $perPage) : 1;

        return $this->response->setJSON([
            'success' => true,
            'data' => $policies,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages
        ]);
    }


    public function expiredCurrentMonth() { return view('admin/currentexpiries'); }
    public function expiredCurrentMonthApi() { return $this->response->setJSON($this->policyModel->getExpiredCurrentMonth()); }
    public function expiredNextMonth() { return view('admin/nextexpiries'); }
    public function expiredNextMonthApi() { return $this->response->setJSON($this->policyModel->getExpiredNextMonth()); }
    public function downloadPolicy($policyId) { return $this->response->download("policies/$policyId.pdf", null); }
    public function deletePolicy($policyId) { return $this->response->setJSON(['success'=>$this->policyModel->delete($policyId)]); }
    public function updatePolicy($policyId) { return $this->response->setJSON(['success'=>$this->policyModel->update($policyId, $this->request->getPost())]); }

    /* ================= OCR ================= */
    public function extractImageText()
    {
        $image = $this->request->getFile('image');
        $upload = $this->validateUpload($image, ['jpg','jpeg','png'], WRITEPATH.'uploads/images/');
        if (!$upload['success']) return $this->response->setJSON($upload);

        $result = $this->ocrProcessor->extractTextFromImage($upload['path']);
        unlink($upload['path']);
        return $this->response->setJSON($result);
    }

    /* ================= Expiry Exports ================= */
    public function exportExpiredExcel() { return $this->policyModel->exportExpiredToExcel(); }
    public function exportNextExpiriesExcel() { return $this->policyModel->exportNextExpiriesToExcel(); }

    /* ================= Subscriptions ================= */
    public function renewSubscription() { return view('admin/renew'); }
    public function renewSubscriptionPost($img)
    {
        $upload = $this->validateUpload($img, ['jpg','jpeg','png'], FCPATH.'uploads/receipts/');
        if (!$upload['success']) return $this->response->setJSON($upload);

        $ocrResult = OcrHelper::runPython($upload['path']);
        $service   = new SubscriptionService();
        return $this->response->setJSON($service->renew($ocrResult, $upload['path']));
    }

    private function purchaseSubscription($img, array $employeeData)
    {
        $upload = $this->validateUpload($img, ['jpg','jpeg','png'], FCPATH.'uploads/receipts/');
        if (!$upload['success']) return $upload;

        $ocrResult = OcrHelper::runPython($upload['path']);
        $service   = new SubscriptionService();
        return $service->purchase($employeeData, $ocrResult);
    }

    public function subscriptionDetails() { return view('admin/subscription'); }
    public function paymentHistory() { return view('admin/payment_history'); }
    public function renewEmpSubscription() { return $this->response->setJSON(['success'=>true]); }

    /* ================= Data Management ================= */
    public function uploadDataPost()
    {
        $file = $this->request->getFile('csv_file') ?: $this->request->getFile('csvFile');
        $mappingJson = $this->request->getPost('mapping');

        try {
            $importer = new \App\Libraries\CsvImporter($file, $mappingJson);
            $rows = $importer->process();
            $this->dataModel->insertBatch($rows);
            return $this->response->setJSON(['success'=>true,'message'=>'Data uploaded successfully']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>$e->getMessage()]);
        }
    }

    public function dataLoader() { return view('admin/dataloader'); }
    public function removeAllData()
    {
        try {
            $this->dataModel->db->table('data')->truncate();
            return $this->response->setJSON(['success'=>true,'message'=>'All data removed successfully!']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success'=>false,'message'=>$e->getMessage()]);
        }
    }
    public function allData() { return view('admin/all_data', ['rows'=>$this->dataModel->findAll()]); }
    public function extractData() { return $this->response->setJSON($this->dataModel->findAll()); }

   /* ================= Employees ================= */
    public function listEmployees()
    {
        $employees = $this->employeeModel->findAll();
        return view('admin/employees', ['employees' => $employees]);
    }

    public function newEmployee()
    {
        return view('admin/employee_new');
    }

    public function addEmployee()
    {
        try {
            $data = $this->request->getPost();
            $this->employeeModel->insert($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Employee added successfully']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function viewEmployee($id)
    {
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return redirect()->to('/admin/employees')->with('error', 'Employee not found');
        }
        return view('admin/employee_view', ['employee' => $employee]);
    }

    public function updateEmployee()
    {
        try {
            $id   = $this->request->getPost('id');
            $data = $this->request->getPost();
            $this->employeeModel->update($id, $data);
            return $this->response->setJSON(['success' => true, 'message' => 'Employee updated successfully']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function uploadProfilePhoto()
    {
        $file = $this->request->getFile('profilePhoto');
        if ($file && $file->isValid()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/profile_photos', $newName);
            return $this->response->setJSON(['success' => true, 'file' => $newName]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid file upload']);
    }

    /* ================= Attendance ================= */
    public function markAttendancePage()
    {
        return view('admin/attendance_mark');
    }

    public function saveAttendance()
    {
        try {
            $data = $this->request->getPost();
            $this->attendanceModel->insert($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Attendance saved']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function attendanceReportPage()
    {
        return view('admin/attendance_report');
    }

    public function getAttendanceReport()
    {
        $filters = $this->request->getPost();
        $report  = $this->attendanceModel->getReport($filters);
        return $this->response->setJSON(['success' => true, 'data' => $report]);
    }

    public function exportAttendanceReport()
    {
        try {
            return $this->attendanceModel->exportReportToExcel();
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function monthlyAttendancePage()
    {
        return view('admin/attendance_monthly');
    }

    public function getMonthlyAttendance()
    {
        $filters = $this->request->getPost();
        $report  = $this->attendanceModel->getMonthlyReport($filters);
        return $this->response->setJSON(['success' => true, 'data' => $report]);
    }

    public function employeeAttendanceHistory($employeeId = null)
    {
        $history = $employeeId
            ? $this->attendanceModel->getHistoryByEmployee($employeeId)
            : $this->attendanceModel->getAllHistory();

        return view('admin/attendance_history', ['history' => $history]);
    }

    public function getTodayStats()
    {
        $stats = $this->attendanceModel->getTodayStats();
        return $this->response->setJSON(['success' => true, 'data' => $stats]);
    }

    public function updateAttendance()
    {
        try {
            $id   = $this->request->getPost('id');
            $data = $this->request->getPost();
            $this->attendanceModel->update($id, $data);
            return $this->response->setJSON(['success' => true, 'message' => 'Attendance updated']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteAttendance()
    {
        try {
            $id = $this->request->getPost('id');
            $this->attendanceModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Attendance deleted']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}