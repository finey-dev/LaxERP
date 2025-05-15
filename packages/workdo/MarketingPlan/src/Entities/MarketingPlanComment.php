<?php

namespace Workdo\MarketingPlan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketingPlanComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'marketing_plan_id',
        'file',
        'comment',
        'parent',
        'workspace',
        'comment_by',
    ];

    public function commentUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'comment_by');
    }

    public function subComment()
    {
        return $this->hasMany('Workdo\MarketingPlan\Entities\MarketingPlanComment', 'parent' , 'id');
    }
}
