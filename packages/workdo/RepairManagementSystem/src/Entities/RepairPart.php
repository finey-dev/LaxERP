<?php

namespace Workdo\RepairManagementSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairPart extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Workdo\RepairManagementSystem\Database\factories\RepairPartFactory::new();
    }

    public static function total_quantity($type, $quantity, $product_id)
    {
        if (module_is_active('ProductService')) {
            $product      = \Workdo\ProductService\Entities\ProductService::find($product_id);
            if (!empty($product)) {
                if (($product->type == 'parts')) {
                    $pro_quantity = $product->quantity;
                    if ($type == 'minus') {
                        $product->quantity = $pro_quantity - $quantity;
                    } else {
                        $product->quantity = $pro_quantity + $quantity;
                    }
                    $product->save();
                }
            }
        }
    }

    public function product()
    {
        if (module_is_active('ProductService')) {
            return $this->hasOne(\Workdo\ProductService\Entities\ProductService::class, 'id', 'product_id')->first();
        } else {
            return [];
        }
    }
}
