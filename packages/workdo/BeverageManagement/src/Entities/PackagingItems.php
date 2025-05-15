<?php

namespace Workdo\BeverageManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackagingItems extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected static function newFactory()
    {
        return \Workdo\BeverageManagement\Database\factories\PackagingItemsFactory::new();
    }

    public function packaging()
    {
        return $this->hasOne(Packaging::class, 'id', 'packaging_id');
    }

    public function rawMaterial()
    {
        return $this->hasOne(RawMaterial::class, 'id', 'raw_material_id');
    }
}
