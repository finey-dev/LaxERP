<?php

namespace Workdo\Facilities\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FacilitiesWorking extends Model
{
    use HasFactory;

    protected $fillable = [
        'opening_time',
        'closing_time',
        'breck_start',
        'breck_end',
        'day_of_week',
        'holiday_setting'
    ];

    public static $week_days = [

        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];
}
