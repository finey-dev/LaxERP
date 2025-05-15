<?php

namespace Workdo\FixEquipment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EquipmentLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_name',
        'address',
        'attachment',
        'location_description',
        'workspace',
        'created_by'
    ];

}
