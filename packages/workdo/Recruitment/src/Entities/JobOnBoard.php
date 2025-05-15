<?php

namespace Workdo\Recruitment\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobOnBoard extends Model
{
    use HasFactory;

    protected $fillable = [
        'application',
        'joining_date',
        'type',
        'branch_id',
        'user_id',
        'status',
        'convert_to_employee',
        'workspace',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected static function newFactory()
    {
        return \Workdo\Recruitment\Database\factories\JobOnBoardFactory::new();
    }

    public function applications()
    {
        return $this->hasOne(JobApplication::class, 'id', 'application');
    }

    public function branches()
    {
        return $this->hasOne(\Workdo\Hrm\Entities\Branch::class, 'id', 'branch_id');
    }

    public function UserName()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static $status = [
        '' => 'Select Status',
        'pending' => 'Pending',
        'cancel' => 'Cancel',
        'confirm' => 'Confirm',
    ];

    public static $job_type = [
        '' => 'Select Job Type',
        'full_time' => 'Full Time',
        'part_time' => 'Part Time',

    ];

    public static $salary_duration = [
        '' => 'Select Salary Duration',
        'monthly' => 'Monthly',
        'weekly' => 'Weekly',
    ];
}
