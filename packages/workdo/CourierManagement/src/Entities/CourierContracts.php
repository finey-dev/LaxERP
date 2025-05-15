<?php

namespace Workdo\CourierManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourierContracts extends Model
{
    use HasFactory;
    protected $table = 'courier_contracts';

    protected $guarded = [];
    public function servicetype()
    {
        return $this->belongsTo(Servicetype::class, 'service_type');
    }
}
