<?php

namespace Workdo\Sales\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'item',
        'qty',
        'tax_rate',
        'list_price',
        'unit_price',
        'description',
        'workspace',
        'created_by',
    ];

    public function items()
    {
        if(module_is_active('ProductService'))
        {
            return $this->hasOne(\Workdo\ProductService\Entities\ProductService::class, 'id', 'item')->first();
        }
        else
        {
            return [];
        }
    }

    public function taxs()
    {
        return $this->hasOne('Workdo\ProductService\Entities\Tax', 'id', 'tax');
    }

    public function tax($taxes)
    {
        $taxArr = explode(',', $taxes);
        $taxes = [];
        foreach($taxArr as $tax)
        {
            $taxes[] = \Workdo\ProductService\Entities\Tax::find($tax);
        }
        return $taxes;
    }
}
