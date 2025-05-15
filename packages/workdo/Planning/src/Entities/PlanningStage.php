<?php

namespace Workdo\Planning\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\BusinessModel\Entities\BusinessModel;
use Workdo\SWOTAnalysisModel\Entities\SwotAnalysisModel;
use Workdo\McKinseyModel\Entities\McKinseyModel;
use Workdo\PESTAnalysisModel\Entities\PESTAnalysisModel;
use Workdo\BusinessPlan\Entities\BusinessPlan;
use Workdo\PESTELAnalysisModel\Entities\PESTELAnalysisModel;
use Workdo\PortersFiveForcesModel\Entities\PortersModel;
use Workdo\MarketingPlan\Entities\MarketingPlan;

class PlanningStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'workspace',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Workdo\Planning\Database\factories\PlanningStageFactory::new();
    }
    public function charter()
    {
        $charter = PlanningCharters::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->where('stage', $this->id);
        $charter = $charter->get();

        return $charter;
    }

    public function businessmodel()
    {
        $charter = businessmodel::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->where('stage', $this->id);
        $charter = $charter->get();

        return $charter;
    }

    public function swotanalysismodel()
    {
        $charter = SwotAnalysisModel::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->where('stage', $this->id);
        $charter = $charter->get();

        return $charter;
    }

    public function mckinseymodel()
    {
        $charter = McKinseyModel::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->where('stage', $this->id);
        $charter = $charter->get();

        return $charter;
    }

    public function pestanalysismodel()
    {
        $charter = PESTAnalysisModel::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->where('stage', $this->id);
        $charter = $charter->get();

        return $charter;
    }

    public function businessplan()
    {
        $charter = BusinessPlan::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->where('stage', $this->id);

        $charter = $charter->get();

        return $charter;

    }

    public function creativity()
    {
        $creativity = PortersModel::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->where('stage', $this->id);
        $creativity = $creativity->get();

        return $creativity;
    }

    public function marketingplan()
    {
        $creativity = MarketingPlan::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->where('stage', $this->id);
        $creativity = $creativity->get();

        return $creativity;
    }
    public function pestelanalysismodel()
    {
        $charter = PESTELAnalysisModel::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->where('stage', $this->id);
        $charter = $charter->get();

        return $charter;
    }
}
