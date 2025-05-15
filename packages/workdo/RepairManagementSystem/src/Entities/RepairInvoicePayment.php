<?php

namespace Workdo\RepairManagementSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairInvoicePayment extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected static function newFactory()
    {
        return \Workdo\RepairManagementSystem\Database\factories\RepairInvoicePaymentFactory::new();
    }
    public function repairPayment()
    {
        return $this->hasOne(RepairInvoice::class, 'id', 'invoice_id');
    }
}
