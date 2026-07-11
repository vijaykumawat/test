<?php namespace App\Controllers;

use App\Libraries\Pdf;
use App\Models\DataModel;
use App\Libraries\tcpdf\tcpdf;

//require_once APPPATH.'libraries/tcpdf/tcpdf.php';

 class Tcpdfexample extends BaseController {
    public $pdf;
    function __construct()
    {
        //$this->userModel = new UserModel();
        //$this->tpdf = new Pdf();
     
    }

    public function quote(){
        
        
        $session = session();
        helper(['form']);
        $dataModel = new DataModel();
        $record = $dataModel->where(array('telecaller'=>$session->get('employeeId'),'recordId'=>$this->request->getVar('recordId')))->first();
        //$record = $dataModel->where(array('regNumber'=>'MH12TV0773'))->first();
        
        $this->pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);   
                

        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Nicola Asuni');
        $this->pdf->SetTitle('QUOTATION');
        $this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set header and footer fonts
//$this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$this->pdf->setPrintHeader(false);
$this->pdf->setPrintFooter(false);
// set default monospaced font
$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$this->pdf->SetLeftMargin(22);
//$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $this->pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$this->pdf->SetFont('helvetica', '', 10);

// add a page
$this->pdf->AddPage();

// NOTE: Uncomment the following line to rasterize SVG image using the ImageMagick library.
//$pdf->setRasterizeVectorImages(true);

//$this->pdf->Image($file='http://localhost/dcs/theme/img/sbilogo.jpg', $x=15, $y=30, $w='', $h='', $link='http://www.tcpdf.org', $align='', $palign='', $border=1, $fitonpage=false);
$logo = "";
if($this->request->getVar('company')=="SBI"){
$this->pdf->Image($file='https://gbsoftsolution.com/dcs/theme/img/sbilogo.jpg', $x=20, $y=18, $w=40, $h=22, $link='', $align='', $palign='', $border=0, $fitonpage=false);
//$this->pdf->Image($file='http://localhost/dcs/theme/img/sbilogo.jpg', $x=20, $y=18, $w=40, $h=22, $link='', $align='', $palign='', $border=0, $fitonpage=false);
}
if($this->request->getVar('company')=="SHRIRAM"){
    $this->pdf->Image($file='https://gbsoftsolution.com/dcs/theme/img/shriramlogo.jpg', $x=150, $y=18, $w=40, $h=22, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    //$this->pdf->Image($file='http://localhost/dcs/theme/img/shriramlogo.jpg', $x=150, $y=18, $w=40, $h=22, $link='', $align='', $palign='', $border=0, $fitonpage=false);
}
if($this->request->getVar('company')=="RELIANCE"){
    $this->pdf->Image($file='https://gbsoftsolution.com/dcs/theme/img/reliancelogo.jpg', $x=20, $y=22, $w=60, $h=10, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    //$this->pdf->Image($file='http://localhost/dcs/theme/img/reliancelogo.jpg', $x=20, $y=22, $w=60, $h=10, $link='', $align='', $palign='', $border=0, $fitonpage=false);
}
$this->pdf->SetFont('helvetica', '', 8);


$this->pdf->SetY(40);
$txt = 'Date : '.date("d/m/Y");
$this->pdf->Write(0, $txt, '', 0, 'L', true, 0, false, false, 0);

$html = <<<HTML
<br><br><br><br>
<div><span style="font-size:8;font-weight: bold;">Intermediary Code </span>: 454407</div>
<div><span style="font-size:8;font-weight: bold;">Intermediary Name </span>: GB Insurance Services</div>
<div><span style="font-size:8;font-weight: bold;text-align: center;">PCV Insurance Quote</span></div>
HTML;
            
$this->pdf->writeHTMLCell(0, 0, '', 34, $html, 0, 1, 0, true, '', true);

$this->pdf->SetFont('helvetica', 'B', 8);
$this->pdf->SetY(69);
$this->pdf->Write(0, 'Motor Insurance Quote No-: QCMVPC0100000191'.mt_rand(1000, 9999), '', 0, 'C', 1, 0, false, false, 0);

$this->pdf->SetFont('helvetica', '', 8);
$this->pdf->SetY(79);
$txt = 'Dear '.$record['ownerName'].',';
$this->pdf->Write(0, $txt, '', 0, 'L', 1, 0, false, false, 0);

$this->pdf->SetY(88);
$txt = 'We hereby extend our gratitude of having given us an opportunity to participate in quoting for the captioned risk';
$this->pdf->Write(0, $txt, '', 0, 'L', 1, 0, false, false, 0);

$this->pdf->SetY(97);
$txt = 'Appended hereunder is a brief summation of the Terms we propose:-';
$this->pdf->Write(0, $txt, '', 0, 'L', 1, 0, false, false, 0);

$this->pdf->Ln(5);


$this->pdf->Ln(5);


$this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(0,0,0);
$this->pdf->MultiCell(23, 14, "Reg. No", 1, 'C', 1, 0, '', '', true);
$this->pdf->MultiCell(23, 14, "MAKE", 1, 'C', 1, 0, '', '', true);
$this->pdf->MultiCell(23, 14, "MODEL", 1, 'C', 1, 0, '', '', true);
$this->pdf->MultiCell(23, 14, "NCB  %", 1, 'C', 1, 0, '', '', true);
$this->pdf->MultiCell(23, 14, "CUBIC CAPACITY", 1, 'C', 1, 0, '', '', true);
$this->pdf->MultiCell(23, 14, "SEATING CAPACITY", 1, 'C', 1, 0, '', '', true);
$this->pdf->MultiCell(24, 14, "FIRST PURCHASE /REGISTRATION DATE", 1, 'C', 1, 1, '', '', true);




/*
$this->pdf->MultiCell(23, 14, $record['regNumber'], 1, 'C', 1, 0);
$this->pdf->MultiCell(23, 14, $record['vehicleMaker'], 'TB', 'C', 1, 0);
$this->pdf->MultiCell(23, 14, $record['vehicleModel'], 1, 'C', 1, 0);
$this->pdf->MultiCell(23, 14, $record['fuelType'], 1, 'C', 1, 0);
$this->pdf->MultiCell(23, 14, "-", 'TB', 'C', 1, 0);
$this->pdf->MultiCell(23, 14, $record['seatCapacity'], 1, 'C', 1, 0);
$this->pdf->MultiCell(24, 14, $record['regDate'], 1, 'C', 1, 0);
*/

$this->pdf->MultiCell(23, 14, $record['regNumber'], 1, 'C', 0, 0, '', '', true);  
$this->pdf->MultiCell(23, 14, $record['vehicleMaker'], 1, 'C', 1, 0, '' ,'', true);
$this->pdf->MultiCell(23, 14, $record['vehicleModel'], 1, 'C', 1, 0, '' ,'', true);
$this->pdf->MultiCell(23, 14, $this->request->getVar('ncb'), 1, 'C', 0, 0, '', '', true);  
$this->pdf->MultiCell(23, 14, $this->request->getVar('cubicCapacity'), 1, 'C', 1, 0, '' ,'', true);
$this->pdf->MultiCell(23, 14, $record['seatCapacity'], 1, 'C', 1, 0, '' ,'', true);
$this->pdf->MultiCell(24, 14, $record['regDate'], 1, 'C', 1, 0, '' ,'', true);
  
$this->pdf->Ln(7);
$this->pdf->Ln(7);

//$style2 = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
//$this->pdf->Rect(22, 124.5, 24, 14, 'D', array('all' => $style2));
//$this->pdf->Rect(91, 124.5, 24, 14, 'D', array('all' => $style2));

/*$this->pdf->SetFont('helvetica', '', 8);
$this->pdf->SetY(145);
$this->pdf->Write(0, 'Policy Period                               : Annual', '', 0, 'L', 1, 0, false, false, 0);
$this->pdf->SetY(152);
$this->pdf->Write(0, 'Cover Type                                 : Package', '', 0, 'L', 1, 0, false, false, 0);
$this->pdf->SetY(159);
$this->pdf->Write(0, 'APPLICABLE NCB %                 : '.$this->request->getVar('ncb').'%', '', 0, 'L', 1, 0, false, false, 0);
*/


$this->pdf->Ln(7);

$text = "Applicationa";

$this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(0,0,0);
//$this->pdf->MultiCell(55, 4, $text, 1, 'C', 1, 0);
//$this->pdf->MultiCell(54, 4, $text, 'TB', 'C', 1, 0);
//$this->pdf->MultiCell(54, 4, $text, 1, 'C', 1, 1);

  $this->pdf->MultiCell(55, 7, 'Net Premium', 1, 'L', 0, 0, '', '', true);  
  $this->pdf->MultiCell(54, 7, 'Taxes Applicable', 1, 'L', 1, 0, '' ,'', true);
  $this->pdf->MultiCell(54, 7, 'Total Premium', 1, 'L', 1, 1, '' ,'', true);
  
  $tax = $this->request->getVar('netPremium')*18/100;
  $consumable = $this->request->getVar('consumable'); 
  $towing = $this->request->getVar('towing');
  $returnToInvoice = $this->request->getVar('returnToInvoice');
  $PAOwner = $this->request->getVar('PAOwner');
  $totalPremium = $this->request->getVar('netPremium') + $tax + (int)$consumable + (int)$towing + (int)$returnToInvoice + (int)$PAOwner ; 
  $this->pdf->MultiCell(55, 7, $this->request->getVar('netPremium'), 1, 'L', 0, 0, '', '', true);  
  $this->pdf->MultiCell(54, 7, $tax, 1, 'L', 1, 0, '' ,'', true);
  $this->pdf->MultiCell(54, 7, $totalPremium, 1, 'L', 1, 1, '' ,'', true);
  
$this->pdf->Ln(7);

  $this->pdf->MultiCell(25, 7, 'IDV of the Vehicle', 1, 'C', 0, 0, '', '', true);  
  $this->pdf->MultiCell(37, 7, 'Non Electrical Accessories', 1, 'C', 1, 0, '' ,'', true);
  $this->pdf->MultiCell(44, 7, 'Electrical/Electronic Accessories', 1, 'C', 1, 0, '' ,'', true);
  $this->pdf->MultiCell(28, 7, 'CNG/LPG Kit Value', 1, 'C', 0, 0, '', '', true);  
  $this->pdf->SetFont('helvetica', 'B', 8);
  $this->pdf->MultiCell(29, 7, 'Total Sum Insured', 1, 'C', 1, 1, '' ,'', true);
  

  $this->pdf->SetFont('helvetica', '', 8);

  $this->pdf->MultiCell(25, 7, $this->request->getVar('idv'), 1, 'C', 0, 0, '', '', true);  
  $this->pdf->MultiCell(37, 7, '0', 1, 'C', 1, 0, '' ,'', true);
  $this->pdf->MultiCell(44, 7, '0', 1, 'C', 1, 0, '' ,'', true);
  $this->pdf->MultiCell(28, 7, '0', 1, 'C', 0, 0, '', '', true);  
  $this->pdf->SetFont('helvetica', 'B', 8);
  $this->pdf->MultiCell(29, 7, $this->request->getVar('idv'), 1, 'C', 1, 1, '' ,'', true);

  
  $this->pdf->Ln(7);
  
  $this->pdf->SetFont('helvetica', 'B', 10);
  $this->pdf->SetTextColor(0,0,255);
  //$this->pdf->MultiCell(30, 7, 'Clauses Applicable', 1, 'L', 0, 0, '', '', true);  
  $this->pdf->MultiCell(163, 7, 'Add on Covers Opted', 1, 'C', 1, 1, '' ,'', true);
  
  $consumableCheckBox = "";
  if(isset($_POST['consumableCheckBox'])){
      $consumableCheckBox = "Consumable";
  }
  $towingCheckBox = "";
  if(isset($_POST['towingCheckBox'])){
      $towingCheckBox = "Towing";
  }
  $returnToInvoiceCheckBox = "";
  if(isset($_POST['returnToInvoiceCheckBox'])){
      $returnToInvoiceCheckBox = "Return To Invoice";
  }
  $PAOwnerCheckBox = "";
  if(isset($_POST['PAOwnerCheckBox'])){
      $PAOwnerCheckBox = "PA Owner";
  }
  
  $this->pdf->SetFont('helvetica', '', 7);
  $this->pdf->SetTextColor(0,0,0);
  //$this->pdf->MultiCell(30, 7, '', 1, 'L', 0, 0, '', '', true);  
  $this->pdf->MultiCell(81, 5, 'Own Damage Basic', 1, 'L', 0, 0, '', '', true);
  $this->pdf->MultiCell(82, 5, $consumableCheckBox , 1, 'L', 1, 1, '' ,'', true);
  $this->pdf->MultiCell(81, 5, 'Third Party Bodily Injury', 1, 'L', 0, 0, '', '', true);
  $this->pdf->MultiCell(82, 5, $towingCheckBox, 1, 'L', 1, 1, '' ,'', true);
  $this->pdf->MultiCell(81, 5, 'Legal Liability to Paid Drivers', 1, 'L', 0, 0, '', '', true);
  $this->pdf->MultiCell(82, 5, $returnToInvoiceCheckBox, 1, 'L', 1, 1, '' ,'', true);
  $this->pdf->MultiCell(81, 5, 'Depreciation Reimbursement', 1, 'L', 0, 0, '', '', true);
  $this->pdf->MultiCell(82, 5, $PAOwnerCheckBox, 1, 'L', 1, 1, '' ,'', true);
  
  
  
  $this->pdf->Ln(7);

  if($this->request->getVar('company')=="SBI"){
    $html = <<<HTML
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    
    <div><span style="font-size:8;font-weight: bold;">Exclusions </span>:For detail list of exclusions, refer policy wordings available on web site <span style="color: blue;text-decoration: underline;">www.sbigeneral.in</span></div>
    <div><span style="font-size:8;font-weight: bold;">Please note that this quote is based on the information provided by you and valid for 30 days from date of issuance. Any
    change in material information may lead to change in Premium amount. </span></div>
    HTML;
                
    $this->pdf->writeHTMLCell(0, 0, '', 34, $html, 0, 1, 0, true, '', true);
        
}
    if($this->request->getVar('company')=="SHRIRAM"){
        $html = <<<HTML
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        
        <div><span style="font-size:8;font-weight: bold;">Exclusions </span>:For detail list of exclusions, refer policy wordings available on web site <span style="color: blue;text-decoration: underline;">www.shriramgi.com</span></div>
        <div><span style="font-size:8;font-weight: bold;">Please note that this quote is based on the information provided by you and valid for 30 days from date of issuance. Any
        change in material information may lead to change in Premium amount. </span></div>
        HTML;
                    
        $this->pdf->writeHTMLCell(0, 0, '', 34, $html, 0, 1, 0, true, '', true);
    }
    
    if($this->request->getVar('company')=="RELIANCE"){
        $html = <<<HTML
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        
        <div><span style="font-size:8;font-weight: bold;">Exclusions </span>:For detail list of exclusions, refer policy wordings available on web site <span style="color: blue;text-decoration: underline;">www.reliancegeneral.co.in</span></div>
        <div><span style="font-size:8;font-weight: bold;">Please note that this quote is based on the information provided by you and valid for 30 days from date of issuance. Any
        change in material information may lead to change in Premium amount. </span></div>
        HTML;
                    
        $this->pdf->writeHTMLCell(0, 0, '', 34, $html, 0, 1, 0, true, '', true);    
    }    
    $this->pdf->Ln(7);

    $this->pdf->SetFont('helvetica', '', 8);
    $this->pdf->SetY(250);
    $txt = 'Producer Name : '.$session->get('name');
    $this->pdf->Write(0, $txt, '', 0, 'L', 1, 0, false, false, 0);
    
    $this->pdf->SetY(257);
    
    $txt = 'Producer Contact : '.$session->get('contact');
    $this->pdf->Write(0, $txt, '', 0, 'L', 1, 0, false, false, 0);
    

        $this->response->setContentType('application/pdf');
        $txt = $record['regNumber'].'.pdf';
        $this->pdf->Output($txt, 'I');
    }
}
/* end tcpdfexample.php file for CodeIgniter 4 TCPDF Integration */
?>