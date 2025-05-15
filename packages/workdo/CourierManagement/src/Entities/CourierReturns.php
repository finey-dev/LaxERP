<?php

namespace Workdo\CourierManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourierReturns extends Model
{
    use HasFactory;
    protected $table = 'courier_returns';

    protected $guarded = [];

    public function package()
    {
        return $this->belongsTo(CourierPackageInfo::class, 'package_id'); // Use 'package_id' as the foreign key
    }

    public function customer()
    {
        return $this->belongsTo(CourierReceiverDetails::class, 'customer_id'); // Use 'customer_id' as the foreign key
    }

}
