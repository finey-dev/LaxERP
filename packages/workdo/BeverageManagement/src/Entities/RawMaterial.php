<?php

namespace Workdo\BeverageManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RawMaterial extends Model
{
    use HasFactory;

    protected $table = 'raw_materials';

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Workdo\BeverageManagement\Database\factories\RawMeterialFactory::new();
    }

    public function collectionCenter()
    {
        return $this->hasOne(CollectionCenter::class, 'id', 'collection_center_id');
    }

    public function productService()
    {
        if (module_is_active('ProductService')) {
            return $this->hasOne(\Workdo\ProductService\Entities\ProductService::class, 'id', 'item_id');
        }
    }
}
