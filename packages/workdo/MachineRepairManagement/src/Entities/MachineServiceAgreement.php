<?php

namespace Workdo\MachineRepairManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MachineServiceAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'customer_id',
        'start_date',
        'end_date',
        'coverage_details',
        'details',
        'workspace',
        'created_by',
    ];

}
