<?php

namespace Workdo\MeetingHub\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingHubAttachment extends Model
{
    use HasFactory;

    protected $table = "meetinghub_attachments";
    protected $fillable = [
        'meeting_minute_id',
        'user_id',
        'files',
        'created_by',
        'workspace_id'
    ];
    
}
