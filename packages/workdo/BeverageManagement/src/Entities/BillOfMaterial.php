<?php

namespace Workdo\BeverageManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillOfMaterial extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'bill_of_materials';

    protected static function newFactory()
    {
        return \Workdo\BeverageManagement\Database\factories\BillOfMaterialFactory::new();
    }

    public function rawMaterial()
    {
        return $this->hasOne(RawMaterial::class, 'id', 'raw_material_id');
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
