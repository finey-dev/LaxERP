<?php

namespace Workdo\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkflowModuleField extends Model
{
    use HasFactory;

    protected $fillable = [
        'workmodule_id',
        'field_name',
        'input_type',
        'model_name',
    ];

}
