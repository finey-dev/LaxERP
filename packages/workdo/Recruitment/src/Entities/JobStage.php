<?php

namespace Workdo\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'order',
        'workspace',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Workdo\Recruitment\Database\factories\JobStageFactory::new();
    }

    public function applications($filter)
    {
        $application = JobApplication::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->where('is_archive', 0)->where('stage', $this->id);
        $application->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($filter['start_date'])));
        $application->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($filter['end_date'])));

        if(!empty($filter['job']))
        {
            $application->where('job', $filter['job']);
        }

        if(!empty($filter['stage']))
        {
            $application->where('stage', $filter['stage']);
        }

        $application = $application->orderBy('order')->get();

        return $application;
    }
}
