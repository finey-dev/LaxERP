<?php

namespace Workdo\BeverageManagement\Entities;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CollectionCenterStock extends Model
{
    use HasFactory;

    protected $gaurded = [];

    protected static function newFactory()
    {
        return \Workdo\BeverageManagement\Database\factories\CollectionCenterStockFactory::new();
    }

    public function product()
    {
        if (module_is_active('ProductService')) {
            return $this->hasOne(\Workdo\ProductService\Entities\ProductService::class, 'id', 'item_id');   //->first()
        }
    }

    public function fromWarehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'from_warehouse'); //->first()
    }

    public function toCollectionCenter()
    {
        return $this->hasOne(CollectionCenter::class, 'id', 'to_collection_center');
    }
}
