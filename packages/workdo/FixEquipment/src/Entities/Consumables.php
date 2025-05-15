<?php

namespace Workdo\FixEquipment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consumables extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'asset',
        'manufacturer',
        'price',
        'quantity',
        'date',
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
        ;
    }

    public function Manufacturers()
    {
        return $this->hasOne(Manufacturer::class, 'id', 'manufacturer');
    }
}
