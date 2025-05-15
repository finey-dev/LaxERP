<?php

namespace Workdo\RepairManagementSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairInvoice extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected static function newFactory()
    {
        return \Workdo\RepairManagementSystem\Database\factories\RepairInvoiceFactory::new();
    }

    public function repairOrderRequest()
    {
        return $this->hasOne(RepairOrderRequest::class, 'id', 'repair_id');
    }

    public function repairParts()
    {
        return $this->hasMany(RepairPart::class, 'repair_id', 'id');
    }
}
