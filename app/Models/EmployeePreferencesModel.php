<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeePreferencesModel extends Model
{
    protected $table      = 'employee_preferences';
    protected $primaryKey = 'employeeId';

    // Allowed fields for insert/update
    protected $allowedFields = [
        'employeeId',
        'visible_fields',
        'updated_at'
    ];

    // Automatically handle timestamps
    protected $useTimestamps = false; // we already have updated_at with default CURRENT_TIMESTAMP
    protected $returnType    = 'array';

    /**
     * Get preferences for a given employeeId
     */
    public function getPreferences(string $employeeId): array
    {
        $row = $this->where('employeeId', $employeeId)->first();
        if ($row && !empty($row['visible_fields'])) {
            return json_decode($row['visible_fields'], true);
        }
        return [];
    }

    /**
     * Save or update preferences for an employee
     */
    public function savePreferences(string $employeeId, array $fields): bool
    {
        $data = [
            'employeeId'    => $employeeId,
            'visible_fields'=> json_encode($fields),
        ];
        return $this->replace($data); // replace will insert or update based on PK
    }
}
