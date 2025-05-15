<?php

namespace Workdo\Assets\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'employee_id',
        'serial_code',
        'dist_number',
        'assign_date',
        'return_date',
        'quantity',
        'assets_branch',
        'notes',
    ];

}
