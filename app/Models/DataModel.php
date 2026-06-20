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
}