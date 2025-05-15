<?php

namespace Workdo\TimeTracker\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\WorkSpace;
use Carbon\Carbon;
use Workdo\Taskly\Entities\Project;
use Workdo\Taskly\Entities\Task;

class TimeTracker extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'task_id',
        'is_active',
        'workspace_id',
        'name',
        'start_time',
        'end_time',
        'total_time',
        'traker_id',
    ];

    public function project_name()
    {
        return $this->hasOne(Project::class, 'id','project_id');
    }

    public function project_task()
    {
        return $this->hasOne(Task::class, 'id','task_id');
    }

    public function project_workspace()
    {
        return $this->hasOne(WorkSpace::class, 'id','workspace_id');
    }
    public static function second_to_time($seconds = 0)
    {
        $H = floor($seconds / 3600);
        $i = ($seconds / 60) % 60;
        $s = $seconds % 60;

        $time = sprintf("%02d:%02d:%02d", $H, $i, $s);

        return $time;
    }
    public static function diffance_to_time($start, $end)
    {
        $start         = new Carbon($start);
        $end           = new Carbon($end);
        $totalDuration = $start->diffInSeconds($end);

        return $totalDuration;
    }

}
