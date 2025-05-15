<?php

namespace Workdo\Planning\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanningComment extends Model
{
    use HasFactory;

    protected $fillable = ['charter_id',
    'file',
    'comment',
    'parent',
    'workspace',
    'comment_by',];

    protected static function newFactory()
    {
        return \Workdo\Planning\Database\factories\PlanningCommentFactory::new();
    }

    public function commentUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'comment_by');
    }

    public function subComment()
    {
        return $this->hasMany('Workdo\Planning\Entities\PlanningComment', 'parent' , 'id');
    }


}
