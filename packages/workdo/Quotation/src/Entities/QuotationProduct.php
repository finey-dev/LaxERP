<?php

namespace Workdo\Quotation\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuotationProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'tax',
        'discount',
        'price',
    ];
    protected static function newFactory()
    {
        return \Workdo\Quotation\Database\factories\QuotationProductFactory::new();
    }


    public function product()
    {
        $quotation =  $this->hasMany(Quotation::class, 'id', 'quotation_id')->first();

        if(!empty($quotation))
        {
            if(module_is_active('ProductService'))
            {
                return $this->hasOne(\Workdo\ProductService\Entities\ProductService::class, 'id', 'product_id')->first();

            }
            else
            {
                return [];
            }
        }

    }


}
