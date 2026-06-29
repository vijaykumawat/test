<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table      = 'employee';
    protected $primaryKey = 'employeeId';
    /*
    protected $allowedFields = [
        'employeeId', 'name', 'dateOfBirth', 'gender', 'email', 'phoneNumber',
        'address', 'pincode', 'username', 'password', 'profilePhoto',
        'hireDate', 'employmentStatus', 'salary', 'bonusEligible', 'benefits',
        'createdAt', 'updatedAt', 'isActive','jobTitle','nationalId'
    ];*/

    protected $allowedFields = [
    'employeeId','name','dateOfBirth','gender','email','phoneNumber',
    'address','pincode','username','password','profilePhoto',
    'hireDate','employmentStatus','salary','bonusEligible','benefits',
    'createdAt','updatedAt','isActive','jobTitle','nationalId',
    'bankAccountNumber','workLocation'
    ];

    protected $useTimestamps = true; // will auto-manage createdAt & updatedAt
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';

    /**
     * Get employees with subscription info
     */
    public function getEmployeesWithSubscriptions()
    {
        return $this->db->table('employee e')
            ->select('e.employeeId, e.name, e.profilePhoto, s.status, s.endDate')
            ->join('subscriptions s', 's.employeeId = e.employeeId', 'left')
            ->get()
            ->getResultArray();
    }

}
