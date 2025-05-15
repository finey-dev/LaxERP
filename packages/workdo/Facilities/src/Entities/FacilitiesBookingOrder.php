<?php

namespace Workdo\Facilities\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FacilitiesBookingOrder extends Model
{
    use HasFactory;

    protected $fillable = [];

    public static $orderstage = [
        "Booked",
        "In Use",
        "Invoiced",
        "Closed",
    ];
}
