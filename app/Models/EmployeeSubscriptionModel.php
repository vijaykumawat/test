<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeSubscriptionModel extends Model
{
    protected $table            = 'subscriptions';   // table name
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'employeeId',
        'startDate',
        'endDate',
        'status',
        'amount',
        'createdAt',
        'updatedAt'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
}
