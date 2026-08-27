<?php namespace App\Controllers;

use App\Libraries\Pdf;
use App\Models\DataModel;
use App\Libraries\tcpdf\tcpdf;
use Config\Insurance;
require_once FCPATH . 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;
use DateTime;
class Tcpdfexample extends BaseController
{
    public function quote(){
        $session = session();
        helper(['form']);
        helper('common');

        $dataModel = new DataModel();

        // Fetch record
        $record = $dataModel->where([
            'telecaller' => $session->get('employeeId'),
            'recordId'   => $this->request->getVar('recordId')
        ])->first();

        if (!$record) {
            return "No record found.";
        }

        // Load insurer config
        $config = new Insurance();
        $company = $this->request->getVar('company');
        $companyConfig = $config->insurers[$company];

        // Inputs
        //$odDiscountInput = (float)$this->request->getVar('od_discount');
        //$age  = (int)$this->request->getVar('age');
        
        $idv  = (float)$this->request->getVar('idv');
        $ncb  = (int)$this->request->getVar('ncb');
        $cc   = (int)$this->request->getVar('cc');
        $isCngInstalled = $this->request->getVar('cng');
        $isZeroDep = $this->request->getVar('zero_dep');
        $seatCapacity = (int)$this->request->getVar('seatCapacity');
        $regDate = $this->request->getVar('regDate');
        $ownerName = $this->request->getVar('ownerName');
        $regNumber = $this->request->getVar('regNumber');
        $vehicleModel = $this->request->getVar('vehicleModel'); 
        $vehicleMaker = $this->request->getVar('vehicleMaker');
        $mobile = $this->request->getVar('mobile');
        $fuelType = $this->request->getVar('fuelType');

        //section A Own Damage
        /*
        $dateString = $regDate;
        $regYear = date('Y', strtotime($dateString));
        $currentYear = date('Y');  // gets current year, e.g. 2026
        $age = $currentYear - $regYear;
        */

        $age=0;
        $currentYear = date('Y');
        $dateString = $regDate;

        // Try parsing with d-m-Y
        $date = DateTime::createFromFormat('d-m-Y', $dateString);

        // If that fails, try d/m/Y
        if (!$date) {
            $date = DateTime::createFromFormat('d/m/Y', $dateString);
        }

        if ($date) {
            $regYear = $date->format('Y');
            //$currentYear = date('Y');
            $age = $currentYear - $regYear;

        } else {
            echo "Invalid date format!";
        }

        
        
        $band = getCCRange($cc);
        $odRate = $band ? $config->insurers['SHRIRAM']['od_rates'][$band] : null;
        $BasicForVehicle = $idv * $odRate /100;
        if($isCngInstalled)
        {
            $cngRate = $band ? $config->insurers['SHRIRAM']['cng_rates'][$band] : null;
            $cngMatchAmt = $cc <= 1000 ? 1 : 2; 
            $cngKit = ($idv * $cngRate / 100) + $cngMatchAmt;
        }
        else
        {
            $cngKit = 0;
        }
        $basicODPremium = $BasicForVehicle + $cngKit; 
        if($company == 'SHRIRAM'){
            $odDiscount = $config->insurers['SHRIRAM']['od_discount'];
            $odDiscountAmt = $basicODPremium * $odDiscount['detariff'] / 100;
        }
        if ($company === 'SBI') {
            $claimStatus = ($ncb == 0) ? 'claim_yes' : 'claim_no';

            // Fetch OD discount from config
            if (isset($config->insurers['SBI']['od_discount'][$claimStatus])) {
                $odDiscount = $config->insurers['SBI']['od_discount'][$claimStatus];
            } else {
                $odDiscount = 0; // fallback if not defined
            }
            $odDiscountAmt = $basicODPremium * $odDiscount / 100;
        }
        if($company == 'RELIANCE'){
            $odDiscount = $config->insurers['RELIANCE']['od_discount'];
            $odDiscountAmt = $basicODPremium * $odDiscount['detariff'] / 100;
        }

        $basicOdPremiumAfterDiscount = $basicODPremium - $odDiscountAmt;
        $ncbAmt = $basicOdPremiumAfterDiscount * $ncb /100;
        $ownDamagePremiumA = $basicOdPremiumAfterDiscount - $ncbAmt;

        //Section B Add-ons

        $basicLiability = $band && isset($config->insurers['SHRIRAM']['tp_rates'][$band]) ? $config->insurers['SHRIRAM']['tp_rates'][$band]['basic_liability'] : null;
        $passengerCoverage = $band && isset($config->insurers['SHRIRAM']['tp_rates'][$band]) ? $config->insurers['SHRIRAM']['tp_rates'][$band]['per_passenger'] * ($seatCapacity -1): null;
        $cngLiability = $band && isset($config->insurers['SHRIRAM']['tp_rates'][$band]) ? $config->insurers['SHRIRAM']['tp_rates'][$band]['cng_liability'] : null;    
        $llLiability = $band && isset($config->insurers['SHRIRAM']['tp_rates'][$band]) ? $config->insurers['SHRIRAM']['tp_rates'][$band]['ll_driver'] : null;
        $thirdParty = $basicLiability + $passengerCoverage;
        $libilityB = $basicLiability + $passengerCoverage + $cngLiability + $llLiability;

        // Section C Addons
        if($isZeroDep)
        {
            if($company == 'SHRIRAM'){
                $ccBand = getCCRangeForZeroDep($cc);
                $ageBand = getAgeRange($age);
                $zeroDepRate = ($ccBand && $ageBand) ? $config->insurers['SHRIRAM']['zero_dep_rates'][$ccBand][$ageBand] : null;
                $zeroDep = $zeroDepRate * $idv ;
                $totalAddonC = $zeroDep;
            }

            if($company == 'SBI'){
                $ageBand = getAgeRange($age);
                $zeroDepRate = $config->insurers['SBI']['zero_dep_rates'][$ageBand];
                $zeroDep = $zeroDepRate * $idv ;
                $totalAddonC = $zeroDep;
            }

        }
        else
        {
            $zeroDep = 0;
            $totalAddonC = 0;
        }

        $totalPremiumWithoutGst = $ownDamagePremiumA + $libilityB + $totalAddonC;
        

        $sgst = $totalPremiumWithoutGst * 9 / 100;
        $cgst = $totalPremiumWithoutGst * 9 / 100;

        $finalPremium = $totalPremiumWithoutGst + $cgst + $cgst;

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomPart = substr(str_shuffle(str_repeat($characters, 15)), 0, 13); // 13 chars
        // Quotation data
        $quotationData = [
            'quoteNo' => 'Q-' . $randomPart,
            'ownerName' => $ownerName,
            'regNumber' => $regNumber,
            'vehicleModel' => $vehicleModel,
            'vehicleMaker' => $vehicleMaker,
            'manufacturingYear' => $regYear,
            'mobile' => $mobile,
            'fuelType' => $fuelType,
            'idv' => $idv,
            'policyType' => 'MOTOR PACKAGE POLICY',
            'odDiscountAmt'=> $odDiscountAmt,
            'ncb'=> $ncb,
            'ownDamagePremiumA'=> $ownDamagePremiumA + $totalAddonC,
            // Add these date fields
            'odStart' => date('d-m-Y'),
            'odEnd'   => date('d-m-Y', strtotime('+364 days')),
            'tpStart' => date('d-m-Y'),
            'tpEnd'   => date('d-m-Y', strtotime('+364 days')),

            // Premium breakdown
            'basicOD' => $BasicForVehicle,
            'cngKit' => $cngKit,
            'totalTP' => $thirdParty,
            'libilityB' => $libilityB,  // add this line
            'cngLiability'=> $cngLiability,
            //'basicTP' => $tp,   // add this line
            'nilDep'  => $zeroDep,   // add this line
            'legalLiability' => $llLiability,   // add this line
            'ncbDiscount' => $ncbAmt,
            //'netOD' => $netOD,
            //'totalOD' => $totalOD,   // add this line
            'totalPremium' => $totalPremiumWithoutGst,   // add this line
            //'addons' => $addonDetails,
            //'subtotal' => $subtotal,
            'sgst' => $sgst,          // add this line
            'cgst' => $cgst,          // add this line
            //'gst' => $gst,
            'finalPremium' => $finalPremium,

            // Other info
            'cngLiability' => $cngLiability,
            'company'=> $company,
            'quoteDate' => date('d-m-Y'),
            'employeeName' => $session->get('employeeName'),
            'employeeMobile' => $session->get('mobile'),
            'email' => 'gbinsurance@gmail.com' // or $record['email'] if stored
        ];

                // Render HTML view
        $html = view('quotation_template', $quotationData);

        // Generate PDF
            // Generate PDF// Generate PDF
        $options = new Options();
        $options->set([
            'isRemoteEnabled'         => true,
            'isFontSubsettingEnabled' => true,        // embed only used glyphs
            'defaultFont'             => 'DejaVu Sans' // safe, Unicode-compatible font
        ]);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->get_font("helvetica", "bold");

        // Get page dimensions
        $pageWidth  = $canvas->get_width();
        $pageHeight = $canvas->get_height();
        // Set transparency
        $canvas->set_opacity(0.2);

        // Draw one watermark at left bottom, large size, opposite angle
        // Draw one big watermark, top-left to bottom-right
        $canvas->rotate(45, 40, 60); // positive angle = top-left → bottom-right
        $canvas->text(40, 60, "GBINSURANCE", $font, 120);
        $canvas->rotate(0, 40, 60); // reset rotation
        // Save PDF to writable folder
        
        /*
        $dir = WRITEPATH . 'quotations';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
       
                            
        $filePath = $dir . "/{$this->request->getVar('regNumber')}.pdf";
        file_put_contents($filePath, $dompdf->output());

        // Force download via CodeIgniter response (works on mobile)
        return $this->response->download($filePath, null)
                            ->setFileName("{$this->request->getVar('regNumber')}.pdf");
                            */
        return $this->response
            ->setContentType('application/pdf')
            ->setHeader(
                'Content-Disposition',
                'attachment; filename="' . $this->request->getVar('regNumber') . '.pdf"'
            )
            ->setBody($dompdf->output());

    }

    public function quote_2()
    {
        $session = session();
        helper(['form']);
        helper('coomon');
        $dataModel = new DataModel();

        // Fetch record
        $record = $dataModel->where([
            'telecaller' => $session->get('employeeId'),
            'recordId'   => $this->request->getVar('recordId')
        ])->first();

        if (!$record) {
            return "No record found.";
        }

        // Load insurer config
        $config = new Insurance();
        $company = $this->request->getVar('company');
        $companyConfig = $config->insurers[$company];

        // Inputs
        $idv  = (float)$this->request->getVar('idv');
        $age  = (int)$this->request->getVar('age');
        $seat = (int)$this->request->getVar('seat');
        $ncb  = (int)$this->request->getVar('ncb');
        $cc   = (int)$this->request->getVar('cc');
        $odDiscountInput = (float)$this->request->getVar('od_discount');
        $cngLiability = $this->request->getVar('cng') ? ($companyConfig['liability']['cng'] ?? 0) : 0;


        // OD Rate
        $odRate = 0;
        foreach($companyConfig['od_rates'] as $range=>$rate){
            [$min,$max] = explode("-", $range);
            if($age >= $min && $age < $max){
                $odRate = $rate;
                break;
            }
        }
        $basicOD = ($idv * $odRate) / 100;

        
        // Apply discount if provided
        if ($odDiscountInput > 0) {
            $odAfterDiscount = $basicOD - (($basicOD * $odDiscountInput) / 100);
        } else {
            // fallback to config discounts
            $detariffDiscount = $basicOD * $companyConfig['od_discount']['detariff'];
            $specialDiscount  = $basicOD * $companyConfig['od_discount']['special'];
            $odAfterDiscount  = $basicOD - ($detariffDiscount + $specialDiscount);
        }

        // Discounts
        $detariffDiscount = $basicOD * $companyConfig['od_discount']['detariff'];
        $specialDiscount  = $basicOD * $companyConfig['od_discount']['special'];
        $odAfterDiscount  = $basicOD - ($detariffDiscount + $specialDiscount);

        // NCB
        $ncbDiscount = ($odAfterDiscount * $ncb) / 100;
        $netOD = $odAfterDiscount - $ncbDiscount;

        // TP
        $tp = 0;
        foreach($companyConfig['tp_rates'] as $range=>$data){
            [$min,$max] = explode("-", $range);
            if($cc >= $min && $cc <= $max){
                if(isset($data[$seat])){
                    $tp = $data[$seat];
                } elseif(isset($data['basic'])) {
                    $tp = $data['basic'];
                }
                break;
            }
        }

        // Addons
        $addonTotal = 0;
        $addonDetails = [];
        foreach($companyConfig['addons'] as $key=>$amount){
            if($this->request->getVar($key)){
                $addonTotal += $amount;
                $addonDetails[$key] = $amount;
            }
        }

        // Add to totals
        $subtotal = $netOD + $tp + $addonTotal + $cngLiability;
        $gst = ($subtotal * $companyConfig['gst']) / 100;
        $finalPremium = $subtotal + $gst;
        $nilDep = $this->request->getVar('zero_dep') ? $companyConfig['addons']['zero_dep'] : 0;
        $legalLiability = $companyConfig['liability']['paid_driver'] ?? 0;
        $totalOD = $netOD;
        $totalPremium = $subtotal; // OD + TP + Addons before GST
        $sgst = $subtotal * 0.09;
        $cgst = $subtotal * 0.09;
        
        // Quotation data
        $quotationData = [
        'quoteNo' => 'Q-' . mt_rand(1000,9999),
        'ownerName' => $record['ownerName'],
        'regNumber' => $record['regNumber'],
        'vehicleModel' => $record['vehicleModel'],
        'vehicleMaker' => $record['vehicleMaker'],
        'manufacturingYear' => $record['regDateMonth'],
        'mobile' => $record['mobile'],
        'fuelType' => $record['fuelType'],
        'idv' => $idv,
        'policyType' => 'MOTOR PACKAGE POLICY',

        // Add these date fields
        'odStart' => date('d-m-Y'),
        'odEnd'   => date('d-m-Y', strtotime('+364 days')),
        'tpStart' => date('d-m-Y'),
        'tpEnd'   => date('d-m-Y', strtotime('+364 days')),

        // Premium breakdown
        'basicOD' => $basicOD,
        'basicTP' => $tp,   // add this line
        'nilDep'  => $nilDep,   // add this line
        'legalLiability' => $legalLiability,   // add this line
        'detariffDiscount' => $detariffDiscount,
        'specialDiscount' => $specialDiscount,
        'ncbDiscount' => $ncbDiscount,
        'netOD' => $netOD,
        'totalOD'          => $totalOD,   // add this line
        'totalTP' => $tp,   // add this line
        'totalPremium' => $totalPremium,   // add this line
        'tp' => $tp,
        'addons' => $addonDetails,
        'subtotal' => $subtotal,
        'sgst' => $sgst,          // add this line
        'cgst' => $cgst,          // add this line
        'gst' => $gst,
        'finalPremium' => $finalPremium,

        // Other info
        'cngLiability' => $cngLiability,
        'company'=> $company,
        'quoteDate' => date('d-m-Y'),
        'employeeName' => $session->get('employeeName'),
        'employeeMobile' => $session->get('mobile'),
        'email' => 'gbinsurance@gmail.com' // or $record['email'] if stored
    ];


        // Render HTML view
        $html = view('quotation_template', $quotationData);

        // Generate PDF
            // Generate PDF// Generate PDF
        $options = new Options();
        $options->set([
            'isRemoteEnabled'         => true,
            'isFontSubsettingEnabled' => true,        // embed only used glyphs
            'defaultFont'             => 'DejaVu Sans' // safe, Unicode-compatible font
        ]);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Save PDF to writable folder
        
        $dir = WRITEPATH . 'quotations';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        $filePath = $dir . "/{$this->request->getVar('regNumber')}.pdf";
        file_put_contents($filePath, $dompdf->output());

        // Force download via CodeIgniter response (works on mobile)
        return $this->response->download($filePath, null)
                            ->setFileName("{$this->request->getVar('regNumber')}.pdf");
    }
}
