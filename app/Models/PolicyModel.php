<?php

namespace App\Models;

use CodeIgniter\Model;

class PolicyModel extends Model
{
    protected $table = 'policies';
    protected $primaryKey = 'policy_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'policy_number',
        'holder_name',
        'company_name',
        'vehicle_number',
        'insurance_type',
        'mobileNo',
        'telecaller',
        'issue_date',
        'expiry_date',
        'file_path',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all policies
     */
    public function getAllPolicies($limit = null, $offset = 0)
    {
        $query = $this->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $query = $query->limit($limit, $offset);
        }
        
        return $query->findAll();
    }

    /**
     * Search policies by policy number, holder name, or vehicle number
     */
    public function searchPolicies($searchTerm, $limit = null, $offset = 0)
    {
        $query = $this->where("policy_number LIKE '%{$searchTerm}%'", null, false)
                      ->orWhere("holder_name LIKE '%{$searchTerm}%'", null, false)
                      ->orWhere("vehicle_number LIKE '%{$searchTerm}%'", null, false)
                      ->orderBy('created_at', 'DESC');

        if ($limit) {
            $query = $query->limit($limit, $offset);
        }

        return $query->findAll();
    }

    /**
     * Get policies expiring in current month
     */
    public function getExpiredCurrentMonth($limit = null, $offset = 0)
    {
        $currentYear = date('Y');
        $currentMonth = date('m');

        $query = $this->where("YEAR(expiry_date) = {$currentYear}", null, false)
                      ->where("MONTH(expiry_date) = {$currentMonth}", null, false)
                      ->orderBy('expiry_date', 'ASC');

        if ($limit) {
            $query = $query->limit($limit, $offset);
        }

        return $query->findAll();
    }

    /**
     * Get policies expiring in next month
     */
    public function getExpiredNextMonth($limit = null, $offset = 0)
    {
        $nextMonth = date('m') + 1;
        $nextYear = date('Y');
        
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        $query = $this->where("YEAR(expiry_date) = {$nextYear}", null, false)
                      ->where("MONTH(expiry_date) = {$nextMonth}", null, false)
                      ->orderBy('expiry_date', 'ASC');

        if ($limit) {
            $query = $query->limit($limit, $offset);
        }

        return $query->findAll();
    }

    /**
     * Count total search results
     */
    public function countSearch($searchTerm)
    {
        return $this->where("policy_number LIKE '%{$searchTerm}%'", null, false)
                    ->orWhere("holder_name LIKE '%{$searchTerm}%'", null, false)
                    ->orWhere("vehicle_number LIKE '%{$searchTerm}%'", null, false)
                    ->countAllResults();
    }

    /**
     * Count expired current month
     */
    public function countExpiredCurrentMonth()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');

        return $this->where("YEAR(expiry_date) = {$currentYear}", null, false)
                    ->where("MONTH(expiry_date) = {$currentMonth}", null, false)
                    ->countAllResults();
    }

    /**
     * Count expired next month
     */
    public function countExpiredNextMonth()
    {
        $nextMonth = date('m') + 1;
        $nextYear = date('Y');
        
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        return $this->where("YEAR(expiry_date) = {$nextYear}", null, false)
                    ->where("MONTH(expiry_date) = {$nextMonth}", null, false)
                    ->countAllResults();
    }

    /**
     * Get policy by policy number
     */
    public function getPolicyByNumber($policyNumber)
    {
        return $this->where('policy_number', $policyNumber)->first();
    }

public function getAllPoliciesWithTelecaller($limit, $offset)
{
    return $this->select('policies.*, employee.name as telecaller_name')
                ->join('employee', 'employee.employeeId = policies.telecaller', 'left')
                ->limit($limit, $offset)
                ->findAll();
}

public function searchPoliciesWithTelecaller($search, $limit, $offset)
{
    return $this->select('policies.*, employee.name as telecaller_name')
                ->join('employee', 'employee.employeeId = policies.telecaller', 'left')
                ->groupStart()
                    ->like('policy_id', $search)
                    ->orLike('policy_number', $search)
                    ->orLike('holder_name', $search)
                    ->orLike('company_name', $search)
                    ->orLike('vehicle_number', $search)
                    ->orLike('insurance_type', $search)
                    ->orLike('mobileNo', $search)
                    ->orLike('employee.name', $search)   // search by telecaller name
                    ->orLike('issue_date', $search)
                    ->orLike('expiry_date', $search)
                    ->orLike('file_path', $search)
                ->groupEnd()
                ->limit($limit, $offset)
                ->findAll();
}
     
}
