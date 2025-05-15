<?php

namespace Workdo\TeamWorkload\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'occasion',
        'workspace',
        'created_by'
    ];


}
