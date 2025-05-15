<?php

namespace Workdo\CourierManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceAgreements extends Model
{
    use HasFactory;
    protected $table = 'service_agreements';

    protected $guarded = [];
}
