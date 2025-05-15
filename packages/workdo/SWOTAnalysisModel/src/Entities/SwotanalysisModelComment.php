<?php

namespace Workdo\SWOTAnalysisModel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SwotanalysisModelComment extends Model
{
    use HasFactory;

    protected $fillable = ['swotanalysis_model_id',
    'file',
    'comment',
    'parent',
    'workspace',
    'comment_by',];




    public function commentUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'comment_by');
    }

    public function subComment()
    {
        return $this->hasMany('Workdo\SWOTAnalysisModel\Entities\SwotanalysisModelComment', 'parent' , 'id');
    }
}
