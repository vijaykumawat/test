<?php

namespace App\Libraries;

use Smalot\PdfParser\Parser;

class PolicyExtractor
{
    public function idExtract($filePath)
    {
        $parser = new \Smalot\PdfParser\Parser();

        try {
            $pdf  = $parser->parseFile($filePath);
            $text = $pdf->getText();
        } catch (\Exception $e) {
            return "Secured/Unsupported PDF";
        }

        if (! $text) {
            return "No text found in PDF";
        }

        // Extract details using regex
        $result = [];

       // Name (after "To" or before DOB)
        if (preg_match('/To\s+[^\n]*?([A-Za-z\s]+[A-Za-z])/u', $text, $m)) {
            $rawName = trim($m[1]);

            // Split into words
            $words = preg_split('/\s+/', $rawName);

            $cleanWords = [];
            foreach ($words as $w) {
                // Skip empty and words that follow double spaces
                if ($w === '') continue;
                // Reject if preceded by two spaces in the original string
                if (preg_match('/\s{2}' . preg_quote($w, '/') . '/', $rawName)) {
                    continue;
                }
                $cleanWords[] = $w;
            }
        // Limit to max 3 words
            $result['name'] = implode(' ', array_slice($cleanWords, 0, 3));
        }
        // Mobile number (first 10-digit number)
        if (preg_match('/\b\d{10}\b/', $text, $m)) {
            $result['mobile'] = $m[0];
        }

        // DOB
        if (preg_match('/DOB[:\s]*([0-9]{2}\/[0-9]{2}\/[0-9]{4})/', $text, $m)) {
            $result['dob'] = $m[1];
        }

        // Gender
        if (preg_match('/\b(MALE|FEMALE|OTHER)\b/i', $text, $m)) {
            $result['gender'] = strtoupper($m[1]);
        }

        // Address + Pincode
        if (preg_match('/Address:\s*(.*?)(\d{6})/s', $text, $m)) {
            $result['address'] = trim($m[1]);
            $result['pincode'] = $m[2];
        }

        return $result;
    }


    public function extractPolicyDetails($filePath)
    {
        $parser = new Parser();

        try {
            $pdf  = $parser->parseFile($filePath);
            $text = $pdf->getText();
        } catch (\Exception $e) {
            return [
                "policyNumber"     => null,
                "holderName"       => "Secured/Unsupported PDF",
                "vehicleNumber"    => null,
                "insuranceType"    => null,
                "totalPremium"     => null,
                "companyName"      => null,
                "policyStart"      => null,
                "expiryDate"       => null
            ];
        }

        $policyNumber = $this->getPolicyNumber($text);
        $holderName   = $this->getPolicyHolderName($text);
        $vehicleNumber= $this->getVehicleNumber($text);
        $insuranceType= $this->getVehicleNumber($text) ? "Vehicle" : null;
        $totalPremium = $this->getTotalPremium($text);
        $filterText   = $this->sanitizeText($text);
        $period       = $this->getPeriodOfInsurance($filterText);
        $policyStart  = $period['startDate'];
        $expiryDate   = $period['endDate'];
        $companyName  = $this->getCompanyName($text);

        if (is_null($policyStart) && is_null($expiryDate)) {
            $period      = $this->getPeriodOfInsurance($text);
            $policyStart = $period['startDate'];
            $expiryDate  = $period['endDate'];
        }

        if (is_null($insuranceType)) {
            $insuranceType = $this->getInsuranceType($text);
        }

        if (is_null($vehicleNumber)) {
            $vehicleNumber = 'NA';
        }

        return [
            "policyNumber"  => $policyNumber,
            "holderName"    => $holderName,
            "vehicleNumber" => $vehicleNumber,
            "insuranceType" => $insuranceType,
            "totalPremium"  => $totalPremium,
            "companyName"   => $companyName,
            "policyStart"   => $policyStart,
            "expiryDate"    => $expiryDate
        ];
    }

    
    private function getCompanyName($text)
    {
        $map = [
            "quickinsure insurance" => "QuickInsure",
            "D2c Insurance" => "D2c Insurance",
            "TURTLEMINT INSURANCE" => "Turtlemint",
            "SHRIRAM GENERAL INSURANCE COMPANY LIMITED" => "Shriram",
            "IndusInd Commercial Vehicles" => "Indusind",
            "ICICI Lombard General Insurance Company Limited" => "ICICI",
            "TATA AIG" => "Tata Aig",
            "Niva Bupa" => "Niva Bupa",
            "Generali Central" => "Generali Central",
            "SBI General" => "SBI General"
        ];

        foreach ($map as $pattern => $label) {
            if (stripos($text, $pattern) !== false) {
                return $label;
            }
        }

        return null;
    }

    private function getPolicyNumber($text)
    {
        $pattern = "/Policy\\s+(?:No\\.?|Number)\\s*[-:\\s]*([0-9A-Z\\/\\-]{8,})/im";
        if (preg_match($pattern, $text, $matches)) {
            $candidate = trim($matches[1]);
            if (preg_match("/^[0-9A-Z\\/\\-]{8,}$/", $candidate)) {
                return $candidate;
            }
        }

        $patternRaw = "/\\b[0-9]{4}\\/[0-9A-Z]{6,}\\/[0-9]{2}\\/[0-9]{3}\\b/";
        if (preg_match($patternRaw, $text, $matches)) {
            return trim($matches[0]);
        }

        $patternNumeric = "/\\b[0-9]{10,}\\b/";
        if (preg_match($patternNumeric, $text, $matches)) {
            return trim($matches[0]);
        }

        return null;
    }

    private function getPolicyHolderName($text)
    {
        $holderName = null;

        if (preg_match("/(?i)(M\\/S\\s+)?([A-Z][A-Za-z\\s&\\.]+?TOURS AND TRAVELS)/", $text, $m)) {
            $holderName = (isset($m[1]) ? $m[1] : "") . $m[2];
            $holderName = preg_replace("/\\s{2,}/", " ", $holderName);
            $holderName = trim($holderName);
        }

        if (empty($holderName)) {
            if (preg_match("/(?i)Dear\\s+([A-Z][A-Za-z\\s&\\.]+?)(?=,|\\r?\\n|$)/", $text, $m)) {
                $holderName = preg_replace("/\\s{2,}/", " ", $m[1]);
                $holderName = trim($holderName);
            }
        }

        if (empty($holderName)) {
            if (preg_match("/(?i)Name\\s+of\\s+Insured\\/Proposer\\s*:?\\s*([A-Z\\s\\.]+?)(?=\\s{2,}[A-Z][a-zA-Z\\s]+\\s*:|\\n|$)/m", $text, $m)) {
                $holderName = $m[1];
                $holderName = preg_replace("/IN-[0-9]+\\s*\\/\\s*/", "", $holderName);
                $holderName = str_replace("\n", " ", $holderName);
                $holderName = preg_replace("/\\s{2,}/", " ", $holderName);
                $holderName = trim($holderName);
            }
        }

        if (empty($holderName)) {
            if (preg_match("/(?i)(Mr\\s+[A-Z][A-Za-z\\s]+?)(?=,|\\r?\\n|$)/", $text, $m)) {
                $holderName = preg_replace("/\\s{2,}/", " ", $m[1]);
                $holderName = trim($holderName);
            }
        }

        if (empty($holderName)) {
            if (preg_match("/(?:Insured(?:'s Code\\/)? Name|Insured\\s+Name|Policy Holder Name|Proposer Name|Name of\\s*\\n?\\s*Insured\\/Proposer)\\s*:?[ \\t]*(.*?)(?=(?:\\s+[A-Z][A-Za-z\\/ ]+\\s*:|\\n|$))/im", $text, $m)) {
                $holderName = $m[1];
                $holderName = preg_replace("/IN-[0-9]+\\s*\\/\\s*/", "", $holderName);
                $holderName = str_replace("\n", " ", $holderName);
                $holderName = preg_replace("/\\s{2,}/", " ", $holderName);
                $holderName = trim($holderName);
            }
        }

        return $holderName;
    }

    private function getVehicleNumber($text)
    {
        return preg_match("/\\b([A-Z]{2}\\s*-?\\s*[0-9]{2}\\s*-?\\s*[A-Z]{2}\\s*-?\\s*[0-9]{4})\\b/i", $text, $m)
            ? preg_replace("/[\\s-]/", "", $m[1]) : null;
    }

    private function getTotalPremium($text)
    {
        $text = preg_replace('/[^\\x20-\\x7E]/', '', $text);
        $patterns = [
            "/TOTAL PREMIUM PAYABLE\\s*:?\\s*[₹]?\\s*([0-9,]+\\.?[0-9]*)/i",
            "/PREMIUM AMOUNT\\s*:?\\s*[₹]?\\s*([0-9,]+\\.?[0-9]*)/i",
            "/Gross Premium Paid\\s*:?\\s*[₹]?\\s*([0-9,]+\\.?[0-9]*)/i",
            "/Policy premium including Tax\\s*:?\\s*[₹]?\\s*([0-9,]+\\.?[0-9]*)/i",
            "/Total Premium\\s*[₹]?\\s*([0-9]{1,3}(?:,\\d{3})*(?:\\.\\d{1,2})?)/i"
        ];
        $premium = null;
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[1] as $value) {
                    $raw = preg_replace("/[,₹\\s]/", "", $value);
                    if (is_numeric($raw)) {
                        $candidate = (float)$raw;
                        if ($premium === null || $candidate > $premium) {
                            $premium = $candidate;
                        }
                    }
                }
            }
        }
        return $premium;
    }

    private function getPeriodOfInsurance($text)
    {
        $text = preg_replace('/[^\\x20-\\x7E]/', '', $text);

        if (preg_match("/From.*?(?:Hrs|Hours)?\\s*of\\s*(\\d{2}\\/\\d{2}\\/\\d{4}|\\d{2}-[A-Za-z]{3}-\\d{4}|[A-Za-z]{3}\\s+\\d{1,2},\\s*\\d{4}).*?Midnight\\s+Of\\s+(\\d{2}\\/\\d{2}\\/\\d{4}|\\d{2}-[A-Za-z]{3}-\\d{4}|[A-Za-z]{3}\\s+\\d{1,2},\\s*\\d{4})/i", $text, $m)) {
            return ["startDate" => $this->normalizeDate($m[1]), "endDate" => $this->normalizeDate($m[2])];
        }

        if (preg_match("/From[:\\s]+(?:Hrs|Hours|of)?\\s*(\\d{2}\\/\\d{2}\\/\\d{4}|\\d{2}-[A-Za-z]{3}-\\d{4}|[A-Za-z]{3}\\s+\\d{1,2},\\s*\\d{4}).*?To[:\\s]+(?:Midnight|of)?\\s*(\\d{2}\\/\\d{2}\\/\\d{4}|\\d{2}-[A-Za-z]{3}-\\d{4}|[A-Za-z]{3}\\s+\\d{1,2},\\s*\\d{4})/i", $text, $m)) {
            return ["startDate" => $this->normalizeDate($m[1]), "endDate" => $this->normalizeDate($m[2])];
        }

        if (preg_match("/From.*?Hrs\\s+on\\s+(\\d{2}\\/\\d{2}\\/\\d{4}|\\d{2}-[A-Za-z]{3}-\\d{4}|[A-Za-z]{3}\\s+\\d{1,2},\\s*\\d{4}).*?Midnight\\s+of\\s+(\\d{2}\\/\\d{2}\\/\\d{4}|\\d{2}-[A-Za-z]{3}-\\d{4}|[A-Za-z]{3}\\s+\\d{1,2},\\s*\\d{4})/i", $text, $m)) {
            return ["startDate" => $this->normalizeDate($m[1]), "endDate" => $this->normalizeDate($m[2])];
        }

        if (preg_match("/([A-Za-z]{3}\\s+\\d{1,2},\\s*\\d{4})\\s+to\\s+([A-Za-z]{3}\\s+\\d{1,2},\\s*\\d{4})/i", $text, $m)) {
            return ["startDate" => $this->normalizeDate($m[1]), "endDate" => $this->normalizeDate($m[2])];
        }

        if (preg_match("/(\\d{2}[\\/-]\\d{2}[\\/-]\\d{4})\\s+to\\s+Midnight\\s+of\\s+(\\d{2}[\\/-]\\d{2}[\\/-]\\d{4})/i", $text, $m)) {
            return ["startDate" => $this->normalizeDate($m[1]), "endDate" => $this->normalizeDate($m[2])];
        }

        if (preg_match("/(\\d{2}[\\/-]\\d{2}[\\/-]\\d{4})\\s*\\([^)]*\\)\\s*to\\s*(\\d{2}[\\/-]\\d{2}[\\/-]\\d{4})\\s*\\(Midnight\\)/i", $text, $m)) {
            return [
                "startDate" => $this->normalizeDate($m[1]),
                "endDate"   => $this->normalizeDate($m[2])
            ];
        }

        return ["startDate" => null, "endDate" => null];
    }

    private function normalizeDate($dateStr)
    {
        $formats = ['d/m/Y', 'd-m-Y', 'd-M-Y', 'M d, Y', 'M d Y'];
        foreach ($formats as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, trim($dateStr));
            if ($dt) {
                return $dt->format('Y-m-d');
            }
        }

        return trim($dateStr);
    }

    private function getInsuranceType($text)
    {
        if (preg_match("/\\bhealth\\b/i", $text)) {
            return "Health";
        }

        return null;
    }

    private function sanitizeText($text)
    {
        $stopKeywords = [
            "Previous Policy Details",
            "PREVIOUS POLICY DETAILS",
            "Previous Insurance Details",
            "Previous Policy"
        ];

        foreach ($stopKeywords as $keyword) {
            $pos = stripos($text, $keyword);
            if ($pos !== false) {
                $text = substr($text, 0, $pos);
                break;
            }
        }

        return $text;
    }
}
