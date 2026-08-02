<?php

namespace App\Models;

use CodeIgniter\Model;

class DataModel extends Model{

    protected $table = 'data';
    protected $primaryKey = 'recordId';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'recordId',
        'regDate',
        'regDateMonth',
        'regNumber',
        'ownerName',
        'address',
        'vehicleMaker',
        'vehicleModel',
        'fuelType',
        'saleAmt',
        'seatCapacity',
        'cubicCapacity',
        'mobile',
        'expiryDate',
        'prevInsuCompany',
        'telecaller',
        'dataUploadDate',
        'actionTaken',
        'isImportant',
        'isIntrested',
        'alreadySale',
        'saleInGb',
        'modifiyDate'
    ];

    public function findAllWithTelecaller()
    {
        return $this->select('data.recordId, data.regDate, data.regNumber, data.ownerName, data.mobile,data.alreadySale, data.actionTaken,data.expiryDate, employee.name as telecaller')
                    ->join('employee', 'employee.employeeId = data.telecaller', 'left')
                    ->findAll();
    }

}