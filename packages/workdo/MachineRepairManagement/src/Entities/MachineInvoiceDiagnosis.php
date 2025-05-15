<?php

namespace Workdo\MachineRepairManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MachineInvoiceDiagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'request_id',
        'product_type',
        'product_id',
        'quantity',
        'tax',
        'discount',
        'description',
        'price',
    ];
    
    public function product()
    {
        $invoice =  $this->hasMany(MachineInvoice::class, 'id', 'invoice_id')->first();
        if(!empty($invoice))
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
