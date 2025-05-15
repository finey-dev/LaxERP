<?php

namespace Workdo\FixEquipment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'license_number',
        'asset',
        'purchase_date',
        'purchase_price',
        'expire_date',
        'account',
        'created_by',
        'workspace'
    ];


    public function equipmentCategory()
    {
        return $this->hasOne(EquipmentCategory::class, 'id', 'category');
    }

    public function fixAsset()
    {
        return $this->hasOne(FixAsset::class, 'id', 'asset');
    }
}
