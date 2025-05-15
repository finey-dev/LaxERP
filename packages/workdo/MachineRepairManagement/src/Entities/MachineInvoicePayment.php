<?php

namespace Workdo\MachineRepairManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MachineInvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'date',
        'amount',
        'payment_method',
        'reference',
        'description',
        'order_id',
        'currency',
        'txn_id',
        'payment_type',
        'receipt',
        'add_receipt',
    ];
}
