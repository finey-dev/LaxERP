<?php

namespace Workdo\Recruitment\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InterviewSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate',
        'user_id',
        'employee',
        'date',
        'time',
        'start_time',
        'end_time',
        'comment',
        'employee_response',
        'workspace',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Workdo\Recruitment\Database\factories\InterviewScheduleFactory::new();
    }

    public function applications()
    {
       return $this->hasOne(JobApplication::class,'id','candidate');
    }

    public function users()
    {
        return $this->hasOne(\Workdo\Hrm\Entities\Employee::class, 'id', 'employee');
    }

    public function username()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
