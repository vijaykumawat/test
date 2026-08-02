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
use App\Models\EmployeeLoginHistoryModel;
//require_once APPPATH . '../public/dompdf/autoload.inc.php';
require_once FCPATH . 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

use DateTime;

class SuperAdmin extends BaseController
{
    protected $policyModel;
    protected $policyExtractor;
    protected $ocrProcessor;
    protected $dataModel;
    protected $attendanceModel;
    protected $historyModel;
    protected $employeeModel;
    protected $employeeLoginHistoryModel;
    protected $employeeSubscriptionModel;
    
    
    public function __construct()
    {
        $this->policyExtractor = new PolicyExtractor();
        $this->ocrProcessor = new OCRProcessor();
        $this->historyModel = new HistoryModel();
        $this->policyModel = new PolicyModel();
        $this->attendanceModel = new AttendanceModel();     
        $this->employeeLoginHistoryModel = new EmployeeLoginHistoryModel();
        $this->employeeSubscriptionModel = new EmployeeSubscriptionModel();
        $this->dataModel = new DataModel();
        $this->employeeModel = new EmployeeModel();
        
    }

    public function dashboard()
    {
        return view('superadmin/dashboard');
    }
    public function clearAllData(){
        $db = \Config\Database::connect();

        $db->query('SET FOREIGN_KEY_CHECKS=0');

        $this->employeeLoginHistoryModel->builder()->emptyTable();
        $this->attendanceModel->builder()->emptyTable();
        $this->employeeSubscriptionModel->builder()->emptyTable();
        $this->historyModel->builder()->emptyTable();
        $this->policyModel->builder()->emptyTable();
        $this->dataModel->builder()->emptyTable();
        $this->employeeModel->builder()->emptyTable();

        $db->query('SET FOREIGN_KEY_CHECKS=1');
        return redirect()->back()->with('success', 'All data has been cleared successfully.');   
    }
    
    public function deleteController()
    {
        /*
        $file = APPPATH . 'Controllers/Testas.php';

        if (file_exists($file)) {
            if (unlink($file)) {
                return redirect()->back()->with('success', 'Controller deleted successfully.');
            } else {
                return redirect()->back()->with('error', 'Unable to delete controller.');
            }
        }

        return redirect()->back()->with('error', 'Controller file not found.');
        */
        /*
         $controllers = [
        'Admin',
        'Employee'
        ];

        $deleted = [];
        $failed = [];

        foreach ($controllers as $controller) {
            $file = APPPATH . 'Controllers/' . $controller . '.php';

            if (file_exists($file)) {
                if (unlink($file)) {
                    $deleted[] = $controller;
                } else {
                    $failed[] = $controller;
                }
            } else {
                $failed[] = $controller . ' (Not Found)';
            }
        }

        return redirect()->back()->with(
            'success',
            count($deleted) . ' controller(s) deleted successfully.'
        );
        */
         $controllers = [
            'Admin',
            'Employee'
        ];

        foreach ($controllers as $controller) {

            $source = APPPATH . "Controllers/{$controller}.php";
            $destination = WRITEPATH . "module_backup/Controllers/{$controller}.php";

             // Controller already moved/deleted
            if (!file_exists($source)) {
                return redirect()->back()->with(
                    'warning',
                    "{$controller} controller is already deleted."
                );
            }

            if (!is_dir(dirname($destination))) {
                mkdir(dirname($destination), 0777, true);
            }

            if (file_exists($source)) {
                rename($source, $destination);
            }
        }
        return redirect()->back()->with(
            'success',
            'controller(s) deleted successfully.'
        );
    }

    public function restoreController(){
        $controllers = [
            'Admin',
            'Employee'
        ];

        foreach ($controllers as $controller) {

            $source = WRITEPATH . "module_backup/Controllers/{$controller}.php";
            $destination = APPPATH . "Controllers/{$controller}.php";

             // Controller already moved/deleted
            if (!file_exists($source)) {
                return redirect()->back()->with(
                    'warning',
                    "{$controller} controller is already restored."
                );
            }

            if (file_exists($source)) {
                rename($source, $destination);
            }
        }
        return redirect()->back()->with(
            'success',
            'controller(s) restored successfully.'
        );
    }


}