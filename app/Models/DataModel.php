<?php

namespace App\Models;

use CodeIgniter\Model;

class DataModel extends Model
{
    protected $table      = 'data';
    protected $primaryKey = 'recordId';

    // If you want auto increment on recordId, set this to true
    protected $useAutoIncrement = true;

    // List of fields allowed for insert/update
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
        'finance',
        'telecaller',
        'dataUploadDate',
        'actionTaken',
        'isImportant',
        'alreadySale',
        'modifiyDate',
        'isIntrested',
        'saleInGb'
    ];

    // Optional: timestamps if you want CI4 to auto‑manage created/updated fields
    protected $useTimestamps = false;

    // Validation rules (optional, can be extended)
    protected $validationRules = [
        'regNumber' => 'required|min_length[5]',
        'ownerName' => 'required',
        'mobile'    => 'required|min_length[10]|max_length[14]',
    ];
    
}
