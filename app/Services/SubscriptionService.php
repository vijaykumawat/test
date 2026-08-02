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

        helper('ocr'); 

        if (! $image || ! $image->isValid()) {
            return ['error' => 'Please upload a valid image file.'];
            
            //return redirect()->to('/admin/subscription')
              //              ->with('error', 'Please upload a valid image file.');
        }

        $extension = strtolower($image->getClientExtension() ?: pathinfo($image->getClientName(), PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg','jpeg','png','webp','bmp','gif'];
        if (! in_array($extension, $allowedExtensions)) {
            return ['error' => 'Only image files are allowed (jpg, png, webp, bmp, gif).'];
            //return redirect()->to('/admin/subscription')
              //              ->with('error', 'Only image files are allowed (jpg, png, webp, bmp, gif).');
        }

        $uploadPath = FCPATH . 'uploads/receipts/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = $image->getRandomName();
        if (! $image->move($uploadPath, $newName)) {
            return ['error' => 'Failed to save uploaded image.'];
            //return redirect()->to('/admin/subscription')
              //              ->with('error', 'Failed to save uploaded image.');
        }
        
        $imagePath = $uploadPath . $newName;

        $result = validateImage($imagePath, $this->ocrProcessor);

        if (!empty($result['error'])) {
            return ['error' => $result['error']];
        }

        // Check for existing record by transactionId or UTR
        $existingPayment = $this->paymentModel
            ->groupStart()
                ->where('transactionId', $result['transactionId'])
                ->orWhere('utrNumber', $result['utr'])
            ->groupEnd()
            ->first();

        if ($existingPayment) {
            return ['error' => 'This is an old screenshot. Payment already recorded.'];
            //return redirect()->to('/admin/subscription')
              //              ->with('error', 'This is an old screenshot. Payment already recorded.');
        }                

        //update subscriptionb table
        $baseDate = normalizeDateString($result['dateText'])?
                    strtotime(normalizeDateString($result['dateText'])) : time();
        $updateData = [
            'endDate'   => date('Y-m-d', strtotime('+30 days', $baseDate)),
            'status'    => 'Active',
            'updatedAt' => date('Y-m-d H:i:s')
        ];

        $this->empSubscriptionModel->where('employeeId', $employeeId)
                        ->set($updateData)
                        ->update();



        // ✅ Save payment record
        $paymentData = [
            'employeeId'     => $employeeId,
            'subscriptionId' => $this->empSubscriptionModel->where('employeeId', $employeeId)->first()['id'] ?? null,
            'transactionId'  => $result['transactionId'],
            'utrNumber'      => $result['utr'],
            'amount'         => $result['amount'],
            'screenshotPath' => 'uploads/receipts/' . $newName, // ✅ store relative path
            'paymentDate'    => date('Y-m-d H:i:s', $baseDate),
            'status'         => 'Success'
        ];
        $this->paymentModel->insert($paymentData);
        return ['success' => 'Payment screenshot verified successfully. Subscription renewed.'];
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
