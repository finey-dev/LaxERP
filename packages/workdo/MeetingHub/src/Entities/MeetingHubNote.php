<?php

namespace Workdo\MeetingHub\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingHubNote extends Model
{
    use HasFactory;

    protected $table = "meetinghub_notes";
    protected $fillable = [
        'meeting_minute_id',
        'user_id',
        'note',
        'created_by',
        'workspace_id'
    ];
    
}
