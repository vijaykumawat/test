<?php

namespace App\Models;

use CodeIgniter\Model;

class UploadModel extends Model
{
    protected $table      = 'uploads';
    protected $primaryKey = 'record_id';

    protected $allowedFields = [
        'batch_id',
        'dataset_type',
        'telecaller_id',
        'status',
        'data',
        'uploaded_at'
    ];

    protected $useTimestamps = false; // we already handle uploaded_at manually
}
