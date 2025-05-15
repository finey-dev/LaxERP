<?php

namespace Workdo\TeamWorkload\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkloadStaffSetting extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'user_id',
        'working',
        'workspace_id',
        'enable_holiday',
        'total_hours',
        'created_by'
    ];

    public function workspace(){
        return $this->hasOne('App\Models\WorkSpace', 'id', 'workspace_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function workingHours($day)
    {
        $workingData = json_decode($this->working, true);

        foreach ($workingData as $workload) {
            if ($workload['working_days'] === $day) {
                return $workload['working_hours'];
            }
        }
        return 0;
    }

    public static $week_days = [

        'mon' => 'Monday',
        'tue' => 'Tuesday',
        'wed' => 'Wednesday',
        'thu' => 'Thursday',
        'fri' => 'Friday',
        'sat' => 'Saturday',
        'sun' => 'Sunday',
    ];


}
