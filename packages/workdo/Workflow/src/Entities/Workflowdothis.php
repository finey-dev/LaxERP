<?php

namespace Workdo\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workflowdothis extends Model
{
    use HasFactory;

    protected $table = 'workflow_dothis';

    protected $fillable = [
        'submodule',
        'module'
    ];

}
