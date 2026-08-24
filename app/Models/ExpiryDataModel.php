<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpiryDataModel extends Model
{
    protected $table = 'expirydata';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'regNumber',
        'expiryDate',
        'employeeId',
        'status'
    ];

    /**
     * Fetch all expiry data rows with the assigned employee name (if any)
     */
    public function findAllWithEmployee()
    {
        return $this->select('expirydata.*, employee.name as employeeName')
                    ->join('employee', 'employee.employeeId = expirydata.employeeId', 'left')
                    ->orderBy('expirydata.id', 'DESC')
                    ->findAll();
    }
}
