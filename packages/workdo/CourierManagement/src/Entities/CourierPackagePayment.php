<?php

namespace Workdo\CourierManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourierPackagePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_id',
        'courier_package_id',
        'payment_type',
        'payment_status',
        'workspace_id',
        'created_by',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\CourierManagement\Database\factories\CourierPackagePaymentFactory::new();
    }

    public function getCourierInformation()
    {
        return $this->hasOne(CourierReceiverDetails::class, 'tracking_id', 'tracking_id');
    }
}
