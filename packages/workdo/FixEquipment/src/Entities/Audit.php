<?php

namespace Workdo\FixEquipment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_title',
        'audit_data' => 'json',
        'audit_date',
        'asset'
    ];

}
