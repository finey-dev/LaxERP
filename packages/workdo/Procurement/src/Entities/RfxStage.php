<?php

namespace Workdo\Procurement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RfxStage extends Model
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
        return \Workdo\Procurement\Database\factories\RfxStageFactory::new();
    }

    public function applications($filter)
    {

        $application = RfxApplication::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('is_archive', 0)->where('stage', $this->id);
        if(!empty($filter['start_date']))
        {
            $application->where('created_at', '>=', $filter['start_date']);
        }
        if(!empty($filter['end_date']))
        {
            $application->where('created_at', '<=', $filter['end_date']);
        }
        
        
        if (!empty($filter['rfx'])) {
            $application->where('rfx', $filter['rfx']);
        }
        $application = $application->orderBy('order')->get();
        return $application;
    }

    public function applicationsProject($filter, $createdBy, $workspace)
    {
        $application = RfxApplication::where('created_by', $createdBy)->where('workspace', $workspace)->where('is_archive', 0)->where('stage', $this->id);
        if (!empty($filter['rfx'])) {
            $application->where('rfx', $filter['rfx']);
        }

        $application = $application->orderBy('order')->get();

        return $application;
    }
}
