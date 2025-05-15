<?php

namespace Workdo\Procurement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RfxItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfx_id',
            'product_type',
            'product_id',
            'product_quantity',
            'product_tax',
            'product_discount',
            'product_price',
            'product_description',
            'rfx_quantity',
            'rfx_tax',
            'rfx_discount',
            'rfx_price',
            'rfx_description',
            'workspace',
            'created_by',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\Procurement\Database\factories\RfxItemFactory::new();
    }

    public function product()
    {
        if(module_is_active('ProductService'))
        {
            return $this->hasOne(\Workdo\ProductService\Entities\ProductService::class, 'id', 'product_id')->first();
        }
    }
}
