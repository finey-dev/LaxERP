<?php

namespace Workdo\RepairManagementSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\RepairManagementSystem\Entities\RepairOrderRequest;
use Workdo\ProductService\Entities\ProductService;

class RepairWarranty extends Model
{
    use HasFactory;

    protected $fillable = ['repair_order_id','part_id','warranty_number','start_date','end_date','warranty_terms','claim_status','workspace','created_by'];

    public function RepairOrder()
    {
        return $this->hasOne(RepairOrderRequest::class, 'id', 'repair_order_id');
    }
    public function RepairParts()
    {
        return $this->hasOne(ProductService::class, 'id', 'part_id');
    }
}
