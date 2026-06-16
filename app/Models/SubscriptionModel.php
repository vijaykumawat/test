<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionModel extends Model
{
    protected $table            = 'subscription';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    // Return results as arrays
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Allowed fields for insert/update
    protected $allowedFields = ['receiver_name','screenshot','status']; 


    // Optional: automatic timestamps if you want CI4 to manage created/updated dates
    protected $useTimestamps = false; // set true if you add created_at/updated_at columns
    protected $createdField  = 'created_date';
    protected $updatedField  = ''; // leave empty if not using

    // Validation rules (optional)
    protected $validationRules = []; 
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
