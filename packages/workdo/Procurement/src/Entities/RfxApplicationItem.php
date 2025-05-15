<?php

namespace Workdo\Procurement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RfxApplicationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'rfx_id',
        'product_type',
        'product_id',
        'product_tax',
        'product_discount',
        'product_price',
        'product_description',
        'product_total_amount',
        'rfx_quantity',
        'rfx_tax',
        'rfx_discount',
        'rfx_price',
        'rfx_description',
        'rfx_description',
        'rfx_total_amount',
        'workspace',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Workdo\Procurement\Database\factories\RfxApplicationItemFactory::new();
    }
    public function product()
    {
        if(module_is_active('ProductService'))
        {
            return $this->hasOne(\Workdo\ProductService\Entities\ProductService::class, 'id', 'product_id')->first();
        }
    }
}
