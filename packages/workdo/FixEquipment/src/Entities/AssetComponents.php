<?php

namespace Workdo\FixEquipment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetComponents extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'asset',
        'price',
        'quantity',
        'created_by',
        'workspace'
    ];

 

    public function equipmentCategory(){
        return $this->hasOne(EquipmentCategory::class, 'id', 'category');
    }

    public function fixAsset(){
        return $this->hasOne(FixAsset::class, 'id', 'asset');;
    }
}
