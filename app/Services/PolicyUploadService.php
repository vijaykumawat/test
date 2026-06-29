<?php

namespace App\Services;

use App\Models\PolicyModel;
use App\Libraries\PolicyExtractor;

class PolicyUploadService
{
    protected $policyModel;
    protected $policyExtractor;

    public function __construct()
    {
        $this->policyModel    = new PolicyModel();
        $this->policyExtractor = new PolicyExtractor();
    }

    public function processFile(\CodeIgniter\HTTP\Files\UploadedFile $file, string $uploadPath): array
    {
        $errors   = [];
        $warnings = [];
        $results  = [];

        if (! $file->isValid() || strtolower($file->getClientExtension()) !== 'pdf') {
            return ['errors' => [$file->getClientName() . ' - Invalid file']];
        }

        $details = $this->policyExtractor->extractPolicyDetails($file->getTempName());
        if (empty($details['policyNumber'])) {
            return ['errors' => [$file->getClientName() . ' - Could not extract policy number']];
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        $insertData = [
            'policy_number' => $details['policyNumber'],
            'holder_name'   => $details['holderName'],
            'company_name'  => $details['companyName'],
            'vehicle_number'=> $details['vehicleNumber'],
            'insurance_type'=> $details['insuranceType'],
            'mobileNo'      => '',
            'issue_date'    => $details['policyStart'],
            'expiry_date'   => $details['expiryDate'],
            'file_path'     => 'writable/uploads/policies/' . $newName,
        ];

        if (! $this->policyModel->insert($insertData)) {
            $dbErrors = $this->policyModel->errors();
            if (file_exists($uploadPath . $newName)) unlink($uploadPath . $newName);
            return ['errors' => [$file->getClientName() . ' - DB error: ' . implode(' ', $dbErrors)]];
        }

        $results[] = [
            'fileName' => $file->getClientName(),
            'details'  => $details,
            'path'     => 'writable/uploads/policies/' . $newName,
        ];

        return ['results' => $results, 'errors' => $errors, 'warnings' => $warnings];
    }
}
