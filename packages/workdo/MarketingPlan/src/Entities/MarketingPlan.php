<?php

namespace Workdo\MarketingPlan\Entities;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\Planning\Entities\PlanningChallenge;
use Workdo\Planning\Entities\PlanningComment;
use Workdo\Planning\Entities\PlanningStage;
use Workdo\Planning\Entities\PlanningStatus;
use Workdo\MarketingPlan\Entities\MarketingPlanComment;

class MarketingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'status',
        'stage',
        'challenge',
        'visibility_type',
        'description',
        'thumbnail_image',
        'business_summary',
        'company_description',
        'team',
        'business_initiative',
        'target_market',
        'marketing_channels',
        'budget',
        'notes',
        'role_id',
        'user_id',
        'video_file',
        'marketing_attachments',
        'rating',
        'order',
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
    public function countBusinessComments()
    {
        return MarketingPlanComment::where('marketing_plan_id', '=', $this->id)->count();
    }
    public function countBusinessAttachment()
    {
        $attachments = json_decode($this->marketing_attachments, true);
        return is_array($attachments) ? count($attachments) : 0;
    }
}
