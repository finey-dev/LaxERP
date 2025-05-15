<?php

namespace Workdo\FixEquipment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EquipmentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'color',
        'created_by',
        'workspace'
    ];

}
