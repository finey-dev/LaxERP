<?php

namespace Workdo\MeetingHub\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingHubTask extends Model
{
    use HasFactory;

    protected $table = "meetinghub_tasks";
    protected $fillable = [
        'meeting_minute_id',
        'name',
        'date',
        'time',
        'priority',
        'status',
        'created_by',
        'workspace_id'
    ];
    
}
