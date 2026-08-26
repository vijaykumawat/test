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

    /**
     * Base query builder with employee join (shared by paginated methods)
     */
    private function baseWithEmployee()
    {
        return $this->select('expirydata.*, employee.name as employeeName')
                    ->join('employee', 'employee.employeeId = expirydata.employeeId', 'left');
    }

    /**
     * Apply the global search filter to a builder.
     * Searches regNumber, expiryDate, employeeId, employee name,
     * plus status labels ("pending", "completed", "skipped").
     */
    private function applySearch($builder, string $search)
    {
        $builder->groupStart()
                    ->like('expirydata.regNumber', $search)
                    ->orLike('expirydata.expiryDate', $search)
                    ->orLike('expirydata.employeeId', $search)
                    ->orLike('employee.name', $search);

        // Allow searching by status label (partial match supported,
        // e.g. "comp", "complete", "COMPLETED", "skip", "pend")
        $statusLabels = [
            1 => 'completed',
            2 => 'skipped',
            0 => 'pending',
        ];
        $key = strtolower(trim($search));
        $matchingStatuses = [];
        foreach ($statusLabels as $value => $label) {
            if (str_contains($label, $key) || str_contains($key, $label)) {
                $matchingStatuses[] = $value;
            }
        }
        if ($matchingStatuses !== []) {
            $builder->orWhereIn('expirydata.status', $matchingStatuses);
        }

        $builder->groupEnd();

        return $builder;
    }

    /**
     * Fetch a single chunk (page) of expiry data rows.
     *
     * @param int    $limit   Number of rows to fetch
     * @param int    $offset  Starting row index
     * @param string $search  Optional search term (matches regNumber, expiryDate, employee name/id, status)
     * @param string $orderColumn Column name to order by
     * @param string $orderDir    Order direction (ASC/DESC)
     */
    public function getPaginatedWithEmployee(int $limit, int $offset, string $search = '', string $orderColumn = 'expirydata.id', string $orderDir = 'DESC')
    {
        $builder = $this->baseWithEmployee();

        if ($search !== '') {
            $builder = $this->applySearch($builder, $search);
        }

        return $builder->orderBy($orderColumn, $orderDir)
                       ->limit($limit, $offset)
                       ->findAll();
    }

    /**
     * Count ALL expiry data rows (unfiltered total)
     */
    public function countAllWithEmployee(): int
    {
        return $this->countAllResults();
    }

    /**
     * Count expiry data rows matching the search term
     */
    public function countFilteredWithEmployee(string $search = ''): int
    {
        $builder = $this->baseWithEmployee();

        if ($search !== '') {
            $builder = $this->applySearch($builder, $search);
        }

        return $builder->countAllResults();
    }

    /**
     * Apply the global search filter for self-scoped (employee) queries.
     * Searches regNumber, expiryDate plus status labels
     * ("pending", "complete(d)", "skip(ped)"). No join required.
     */
    private function applySelfSearch($builder, string $search)
    {
        $builder->groupStart()
                    ->like('regNumber', $search)
                    ->orLike('expiryDate', $search);

        // Allow searching by status label (partial match supported,
        // e.g. "comp", "complete", "COMPLETED", "skip", "pend")
        $statusLabels = [
            1 => 'completed',
            2 => 'skipped',
            0 => 'pending',
        ];
        $key = strtolower(trim($search));
        $matchingStatuses = [];
        foreach ($statusLabels as $value => $label) {
            if (str_contains($label, $key) || str_contains($key, $label)) {
                $matchingStatuses[] = $value;
            }
        }
        if ($matchingStatuses !== []) {
            $builder->orWhereIn('status', $matchingStatuses);
        }

        $builder->groupEnd();

        return $builder;
    }

    /**
     * Fetch a single chunk (page) of expiry data rows for one employee.
     *
     * @param string $employeeId Logged-in employee id (scope filter)
     * @param int    $limit      Number of rows to fetch
     * @param int    $offset     Starting row index
     * @param string $search     Optional search term (regNumber, expiryDate, status label)
     * @param string $orderColumn Column name to order by
     * @param string $orderDir    Order direction (ASC/DESC)
     */
    public function getPaginatedForEmployee(string $employeeId, int $limit, int $offset, string $search = '', string $orderColumn = 'id', string $orderDir = 'ASC')
    {
        $builder = $this->where('employeeId', $employeeId);

        if ($search !== '') {
            $builder = $this->applySelfSearch($builder, $search);
        }

        return $builder->orderBy($orderColumn, $orderDir)
                       ->limit($limit, $offset)
                       ->findAll();
    }

    /**
     * Count ALL expiry data rows for one employee (unfiltered total)
     */
    public function countAllForEmployee(string $employeeId): int
    {
        return $this->where('employeeId', $employeeId)->countAllResults();
    }

    /**
     * Count expiry data rows matching the search term for one employee
     */
    public function countFilteredForEmployee(string $employeeId, string $search = ''): int
    {
        $builder = $this->where('employeeId', $employeeId);

        if ($search !== '') {
            $builder = $this->applySelfSearch($builder, $search);
        }

        return $builder->countAllResults();
    }
}
