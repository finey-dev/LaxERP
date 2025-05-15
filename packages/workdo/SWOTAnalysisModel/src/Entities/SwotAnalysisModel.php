<?php

namespace Workdo\SWOTAnalysisModel\Entities;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\Planning\Entities\PlanningChallenge;
use Workdo\Planning\Entities\PlanningStage;
use Workdo\Planning\Entities\PlanningStatus;

class SwotAnalysisModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dsescription',
        'status',
        'stage',
        'challenge',
        'visibility_type',
        'rating',
        'thumbnail_image',
        'video_file',
        'user_id',
        'role_id',
        'order',
        'swotanalysismodel_attachments',
        'strengths',
        'weaknesses',
        'opportunities',
        'threats',
        'notes',
        'workspace',
        'created_by',
    ];



    public function Stage()
    {
        return $this->hasOne(PlanningStage::class, 'id', 'stage');
    }
    public function Status()
    {
        return $this->hasOne(PlanningStatus::class, 'id', 'status');
    }

    public function Challenge()
    {
        return $this->hasOne(PlanningChallenge::class, 'id', 'challenge');
    }

    public function Role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function countCharterComments()
    {
        return SwotanalysisModelComment::where('swotanalysis_model_id', '=', $this->id)->count();
    }

    public function countAttachment()
    {
        $attachments = json_decode($this->swotanalysismodel_attachments, true);
        return is_array($attachments) ? count($attachments) : 0;
    }

    public function type()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
