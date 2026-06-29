<?php
namespace App\Services;

use App\Models\SubscriptionModel;
use App\Models\EmployeeSubscriptionModel;

class SubscriptionService {
    public function renew($ocrResult, $imagePath) {
        // validate OCR result
        $receiverValid = $this->containsReceiverName($ocrResult['text']);
        $dateValid     = $this->isTodayDate($ocrResult['date']);

        $subscriptionModel = new SubscriptionModel();
        $subscriptionModel->insert([
            'receiver_name' => $receiverValid ? 'Vijay Kailas Kumawat' : 'Not found',
            'screenshot'    => $imagePath,
            'status'        => ($receiverValid && $dateValid) ? 1 : 0
        ]);

        return ['receiverValid'=>$receiverValid,'dateValid'=>$dateValid];
    }

    public function purchase($employeeData, $ocrResult) {
        $receiverValid = $this->containsReceiverName($ocrResult['text']);
        $dateValid     = $this->isTodayDate($ocrResult['date']);
        if (!$receiverValid || !$dateValid) {
            return ['success'=>false,'message'=>'Wrong screenshot attached.'];
        }

        $model = new EmployeeSubscriptionModel();
        $id = $model->insert([
            'employeeId'=>$employeeData['employeeId'],
            'startDate'=>date('Y-m-d'),
            'endDate'=>date('Y-m-d',strtotime('+1 month')),
            'status'=>'Active',
            'amount'=>100.00
        ]);

        return ['success'=>true,'subscriptionId'=>$id];
    }

    // You can reuse containsReceiverName(), extractDateFromText(), isTodayDate() from your OCR helper
}
