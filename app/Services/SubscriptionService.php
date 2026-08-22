<?php
namespace App\Services;

use App\Models\EmployeeSubscriptionModel;
use App\Models\PaymentModel;
use App\Libraries\OCRProcessor;

class SubscriptionService {

        protected $empSubscriptionModel;
        protected $paymentModel;
        protected $ocrProcessor;

    public function __construct()
    {
        $this->empSubscriptionModel = new EmployeeSubscriptionModel();
        $this->paymentModel      = new PaymentModel();
        $this->ocrProcessor = new OCRProcessor();
    }

    public function renewSubscription($employeeId, $image) {
        return $this->renewSubscriptions([$employeeId], $image);
    }

    public function renewSubscriptions(array $employeeIds, $image)
    {
        helper('ocr');

        $employeeIds = array_values(array_filter(array_unique($employeeIds)));
        if (empty($employeeIds)) {
            return ['error' => 'Please select at least one employee.'];
        }

        if (! $image || ! $image->isValid()) {
            return ['error' => 'Please upload a valid image file.'];
        }

        $extension = strtolower($image->getClientExtension() ?: pathinfo($image->getClientName(), PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg','jpeg','png','webp','bmp','gif'];
        if (! in_array($extension, $allowedExtensions)) {
            return ['error' => 'Only image files are allowed (jpg, png, webp, bmp, gif).'];
        }

        $uploadPath = FCPATH . 'uploads/receipts/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = $image->getRandomName();
        if (! $image->move($uploadPath, $newName)) {
            return ['error' => 'Failed to save uploaded image.'];
        }

        $imagePath = $uploadPath . $newName;
        $result = validateImage($imagePath, $this->ocrProcessor);

        if (! empty($result['error'])) {
            return ['error' => $result['error']];
        }

        $existingPayment = $this->paymentModel
            ->groupStart()
                ->where('transactionId', $result['transactionId'])
                ->orWhere('utrNumber', $result['utr'])
            ->groupEnd()
            ->first();

        if ($existingPayment) {
            return ['error' => 'This is an old screenshot. Payment already recorded.'];
        }

        $subscriptions = $this->empSubscriptionModel->whereIn('employeeId', $employeeIds)->findAll();
        if (empty($subscriptions)) {
            return ['error' => 'No subscription records found for selected employees.'];
        }

        $foundEmployeeIds = array_column($subscriptions, 'employeeId');
        $missingIds = array_diff($employeeIds, $foundEmployeeIds);
        if (! empty($missingIds)) {
            return ['error' => 'Some selected employees do not have active subscription records.'];
        }

        $baseDate = normalizeDateString($result['dateText']) ? strtotime(normalizeDateString($result['dateText'])) : time();
        $updateData = [
            'endDate'   => date('Y-m-d', strtotime('+30 days', $baseDate)),
            'status'    => 'Active',
            'updatedAt' => date('Y-m-d H:i:s')
        ];

        $this->empSubscriptionModel->whereIn('employeeId', $employeeIds)
                                   ->set($updateData)
                                   ->update();

        $paymentData = [];
        foreach ($subscriptions as $subscription) {
            $paymentData[] = [
                'employeeId'     => $subscription['employeeId'],
                'subscriptionId' => $subscription['id'] ?? null,
                'transactionId'  => $result['transactionId'],
                'utrNumber'      => $result['utr'],
                'amount'         => $result['amount'],
                'screenshotPath' => 'uploads/receipts/' . $newName,
                'paymentDate'    => date('Y-m-d H:i:s', $baseDate),
                'status'         => 'Success'
            ];
        }

        if (! empty($paymentData)) {
            $this->paymentModel->insertBatch($paymentData);
        }

        //$this->sendRenewalEmail($subscriptions, $imagePath, $result);

        return ['success' => 'Payment screenshot verified successfully. Subscription renewed for ' . count($employeeIds) . ' employee(s).'];
    }

    private function sendRenewalEmail(array $subscriptions, string $imagePath, array $result): void
    {
        try {
            $email = \Config\Services::email();
            $email->setFrom('no-reply@gbinsurance.com', 'Subscription System');
            $email->setTo('vijeykumawatt@gmail.com');
            $email->setSubject('Payment Details');

            $employeeIds = array_column($subscriptions, 'employeeId');
            $employeeList = implode("\n", array_map(function ($id) {
                return '- ' . $id;
            }, $employeeIds));

            $message = "Payment screenshot uploaded and validated.\n\n";
            $message .= "Employees renewed: " . count($subscriptions) . "\n";
            $message .= "Employee IDs:\n" . $employeeList . "\n\n";
            $message .= "Payment details:\n";
            $message .= "Transaction ID: " . ($result['transactionId'] ?? 'N/A') . "\n";
            $message .= "UTR: " . ($result['utr'] ?? 'N/A') . "\n";
            $message .= "Amount: " . ($result['amount'] ?? 'N/A') . "\n";
            $message .= "Date detected: " . ($result['dateText'] ?? 'N/A') . "\n\n";
            $message .= "Regards,\nSubscription System";

            $email->setMessage($message);
            $email->attach($imagePath);

            if (! $email->send()) {
                log_message('error', 'Subscription payment email failed: ' . $email->printDebugger(['headers']));
            }
        } catch (\Exception $e) {
            log_message('error', 'Subscription payment email exception: ' . $e->getMessage());
        }
    }

    public function purchaseSubscription($img, array $employeeData)
    {
        helper('ocr');
        $image = $img;
        if (! $image || ! $image->isValid()) {
            return ['success' => false, 'message' => 'Please upload a valid Payment screenshot file.'];
        }

        $extension = strtolower($image->getClientExtension() ?: pathinfo($image->getClientName(), PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg','jpeg','png','webp','bmp','gif'];
        if (! in_array($extension, $allowedExtensions)) {
            return ['success' => false, 'message' => 'Only image files are allowed (jpg, png, webp, bmp, gif).'];
        }

        $uploadPath = FCPATH . 'uploads/receipts/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = $image->getRandomName();
        if (! $image->move($uploadPath, $newName)) {
            return ['success' => false, 'message' => 'Failed to save uploaded image.'];
        }

        $imagePath = $uploadPath . $newName;
        //$ocrResult = $this->runOcrPython($imagePath);

        $result = validateImage($imagePath, $this->ocrProcessor);
        
        if (!empty($result['error'])) {
            //return ['error' => $result['error']];
            return ['success' => false, 'message' => $result['error']];
        }

        // Check for existing record by transactionId or UTR
        $existingPayment = $this->paymentModel
            ->groupStart()
                ->where('transactionId', $result['transactionId'])
                ->orWhere('utrNumber', $result['utr'])
            ->groupEnd()
            ->first();

        if ($existingPayment) {
            //return ['error' => 'This is an old screenshot. Payment already recorded.'];
                return ['success' => false, 'message' => 'This is an old screenshot. Payment already recorded.'];
        
            //return redirect()->to('/admin/subscription')
              //              ->with('error', 'This is an old screenshot. Payment already recorded.');
        }

        // ✅ Only insert if validation passed
        
        $insertData = [
            'employeeId' => $employeeData['employeeId'],
            'startDate'  => date('Y-m-d'),
            'endDate'    => date('Y-m-d', strtotime('+1 month')),
            'status'     => 'Active',
            'amount'     => 100.00
        ];

        $subscriptionId = $this->empSubscriptionModel->insert($insertData);
        if ($subscriptionId === false) {
            return [
                'success' => false,
                'message' => 'Failed to insert subscription',
                'errors'  => $this->empSubscriptionModel->errors()
            ];
        }
        $paymentData = [
            'employeeId'     => $employeeData['employeeId'],
            'subscriptionId' => $this->empSubscriptionModel->where('employeeId', $employeeData['employeeId'])->first()['id'] ?? null,
            'transactionId'  => $result['transactionId'],
            'utrNumber'      => $result['utr'],
            'amount'         => $result['amount'],
            'screenshotPath' => 'uploads/receipts/' . $newName, // ✅ store relative path
            'paymentDate'    => date('Y-m-d H:i:s'),
            'status'         => 'Success'
        ];
        $this->paymentModel->insert($paymentData);

        return [
            'success'        => true,
            'message'        => 'Payment screenshot verified successfully.',
            'receiver'       => 'Vijay Kailas kumawat',
            'date'           => $result['dateText'] ?: date('Y-m-d'),
            'subscriptionId' => $subscriptionId
        ];
    }
 
}
