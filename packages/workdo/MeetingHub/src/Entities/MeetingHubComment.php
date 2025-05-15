<?php

namespace Workdo\MeetingHub\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingHubComment extends Model
{
    use HasFactory;

    protected $table = "meetinghub_comments";
    protected $fillable = [
        'meeting_minute_id',
        'contract_id',
        'user_id',
        'comment',
        'workspace',
    ];
    
}
