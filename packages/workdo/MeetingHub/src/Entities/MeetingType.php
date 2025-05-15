<?php

namespace Workdo\MeetingHub\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingType extends Model
{
    use HasFactory;

    protected $table = "meetinghub_meeting_types";
    protected $fillable = [
        'name',
        'workspace',
        'created_by',
    ];
    
}
