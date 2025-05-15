<?php

namespace Workdo\BeverageManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\ProductService\Entities\Tax;

class BillMaterialItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'bill_material_items';
    
    protected static function newFactory()
    {
        return \Workdo\BeverageManagement\Database\factories\BillMaterialItemFactory::new();
    }

    public function rawMaterial()
    {
        return $this->hasOne(RawMaterial::class, 'id', 'raw_material_id');
    }
    
    public static function tax($taxes)
    {
        if(module_is_active('ProductService'))
        {
            $taxArr = explode(',', $taxes);
            $taxes  = [];
            foreach($taxArr as $tax)
            {
                $taxes[] = Tax::find($tax);
            }

            return $taxes;
        }
        else
        {
            return [];
        }
    }
}
