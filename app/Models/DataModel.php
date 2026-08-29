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
        return $this->select('data.*, employee.name as telecaller, data.telecaller as telecallerId')
                    ->join('employee', 'employee.employeeId = data.telecaller', 'left')
                    ->findAll();
    }

    /**
     * Base query builder with the telecaller join.
     * Shared by the paginated methods so we never load every row at once.
     */
    private function baseWithTelecaller()
    {
        return $this->select('data.*, employee.name as telecaller, data.telecaller as telecallerId')
                    ->join('employee', 'employee.employeeId = data.telecaller', 'left');
    }

    /**
     * Apply the global search filter to a builder.
     * Matches the columns shown on the All Data page: regNumber, owner name,
     * vehicle maker/model, fuel, mobile, previous insurance and telecaller name.
     */
    private function applyTelecallerSearch($builder, string $search)
    {
        $builder->groupStart()
                ->like('data.recordId', $search)
                ->orLike('data.regNumber', $search)
                ->orLike('data.ownerName', $search)
                ->orLike('data.vehicleMaker', $search)
                ->orLike('data.vehicleModel', $search)
                ->orLike('data.fuelType', $search)
                ->orLike('data.mobile', $search)
                ->orLike('data.prevInsuCompany', $search)
                ->orLike('employee.name', $search);

        return $builder->groupEnd();
    }

    /**
     * Fetch a single chunk (page) of data rows for DataTables server-side
     * processing. Only the requested page of records is pulled from the
     * database instead of the whole table.
     *
     * @param int    $limit       Number of rows to fetch
     * @param int    $offset      Starting row index
     * @param string $search      Optional global search term
     * @param string $orderColumn Column to order by (e.g. "data.recordId")
     * @param string $orderDir    Order direction (ASC/DESC)
     */
    public function findAllWithTelecallerPaginated(int $limit, int $offset, string $search = '', string $orderColumn = 'data.recordId', string $orderDir = 'ASC')
    {
        $builder = $this->baseWithTelecaller();

        if ($search !== '') {
            $builder = $this->applyTelecallerSearch($builder, $search);
        }

        return $builder->orderBy($orderColumn, $orderDir)
                       ->limit($limit, $offset)
                       ->findAll();
    }

    /**
     * Total number of data rows (unfiltered) - returned as recordsTotal.
     */
    public function countAllWithTelecaller(): int
    {
        return $this->countAllResults();
    }

    /**
     * Number of data rows matching the search term - returned as recordsFiltered.
     */
    public function countFilteredWithTelecaller(string $search = ''): int
    {
        $builder = $this->baseWithTelecaller();

        if ($search !== '') {
            $builder = $this->applyTelecallerSearch($builder, $search);
        }

        return $builder->countAllResults();
    }

}