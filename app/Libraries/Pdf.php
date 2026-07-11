<?php

namespace App\Libraries;
//use App\Libraries\tcpdf\tcpdf;

//require_once APPPATH.'libraries/tcpdf/tcpdf.php';
require_once(dirname(__FILE__).'/tcpdf/tcpdf.php');


class Pdf extends \TCPDF
{
    public function __construct()
    {
        parent::__construct();
    }
}