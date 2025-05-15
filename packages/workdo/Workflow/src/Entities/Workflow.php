<?php

namespace Workdo\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'event',
        'do_this',
        'json_data',
        'message',
        'webhook_url',
        'workspace',
        'created_by'
    ];

    public function module()
    {
        return $this->hasOne(WorkflowModule::class, 'id', 'event');
    }

    public function workmodule()
    {
        if($this->do_this)
        {
            return Workflowdothis::whereIn('id', explode(',', $this->do_this))->pluck('submodule');
        }
        return collect();
 
    }

    public static $condition = [
        'is' =>'is' ,
        'is empty' => 'is empty',
        'is not empty' => 'is not empty',
        'contains' => 'contains',
        'greater than' => 'greater than',
        'less than' => 'less than',
        'equal' => 'equal',
        'not equal to' => 'not equal to',
    ];

    public static $condition_symbol  = [
        'is' =>'=' ,
        'is empty' => '=',
        'is not empty' => '=',
        'contains' => 'contains',
        'greater than' => '>',
        'less than' => '<',
        'equal' => '=',
        'not equal to' => '!=',
    ];

    public static $where = [
        'and' => 'AND',
        'or' => 'OR',
    ];

    public static $gender = [
        'male' => 'male',
        'female' => 'female',
        'other' => 'other',
    ];

    public static $serviceType = [
        'Free' => 'Free',
        'Paid' => 'Paid',
    ];

    public static $wash = [
        'Yes' => 'Yes',
        'No' => 'No',
    ];

    public static $rentType = [
        'Monthly' => 'Monthly',
        'Quarterly' => 'Quarterly',
        'Yearly' => 'Yearly',
    ];

    public function workflow_dothis()
    {
        return $this->hasMany(WorkflowModuleField::class, 'workmodule_id', 'event');
    }

    public function workflow_event()
    {
        return $this->hasMany(WorkflowModuleField::class, 'workmodule_id', 'event');
    }
}
