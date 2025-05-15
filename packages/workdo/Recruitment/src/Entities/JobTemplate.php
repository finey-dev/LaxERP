<?php

namespace Workdo\Recruitment\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'recruitment_type',
        'job_type',
        'job_id',
        'link_type',
        'job_link',
        'user_id',
        'description',
        'requirement',
        'is_post',
        'terms_and_conditions',
        'branch',
        'location',
        'category',
        'skill',
        'position',
        'start_date',
        'end_date',
        'status',
        'salary_from',
        'salary_to',
        'applicant',
        'visibility',
        'address',
        'code',
        'custom_question',
        'workspace',
        'created_by',
    ];

    public static $status = [
        'active' => 'Active',
        'in_active' => 'In Active',
    ];

    public static $job_type = [
        '' => 'Select Job Type',
        'full_time' => 'Full Time',
        'part_time' => 'Part Time',
    ];

    public function branches()
    {
        return $this->hasOne(\Workdo\Hrm\Entities\Branch::class, 'id', 'branch');
    }

    public function categories()
    {
        return $this->hasOne(JobCategory::class, 'id', 'category');
    }

    public function questions()
    {
        $ids = explode(',', $this->custom_question);

        return CustomQuestion::whereIn('id', $ids)->get();
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function UserName()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
