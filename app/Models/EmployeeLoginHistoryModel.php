<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeLoginHistoryModel extends Model
{
    protected $table = 'employeeloginhistory';   // match DB exactly
    protected $primaryKey = 'id';
    protected $allowedFields = ['employeeId','loginTime','logoutTime','status'];
}