<?php

namespace Workdo\FixEquipment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Depreciation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'created_by',
        'workspace',
    ];

}
