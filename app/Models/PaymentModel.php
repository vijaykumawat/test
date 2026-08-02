<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table      = 'payments';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'employeeId',   
        'subscriptionId',
        'transactionId',
        'utrNumber',
        'amount',
        'screenshotPath',   // ✅ new field
        'paymentDate',
        'status',
        'createdAt',
        'updatedAt'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
