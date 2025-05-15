<?php

namespace Workdo\FixEquipment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Maintenance extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'equipment_maintenance';

    protected $fillable = [
        'maintenance_type',
        'asset',
        'price',
        'maintenance_date',
        'description',
        'account',
        'created_by',
        'workspace',
    ];

    public function fixAsset()
    {
        return $this->hasOne(FixAsset::class, 'id', 'asset');
    }
}
