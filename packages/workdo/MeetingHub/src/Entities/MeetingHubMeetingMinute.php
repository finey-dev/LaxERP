<?php

namespace Workdo\MeetingHub\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
class MeetingHubMeetingMinute extends Model
{
    use HasFactory;

    protected $table = "meetinghub_meeting_minutes";
    protected $fillable = [
        'meeting_id',
        'caller',
        'log_type',
        'contact_user',
        'phone_no',
        'call_start_time',
        'call_end_time',
        'duration',
        'important',
        'completed',
        'status',
        'priority',
        'assign_user',
        'note',
        'created_by',
        'workspace_id'
    ];

    public function meetinghub()
    {
        return $this->hasOne(MeetingHubMeeting::class, 'id', 'meeting_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'assign_user');
    }

    public function contactUser()
    {
        return $this->hasOne(User::class, 'id', 'contact_user');
    }

    public function files()
    {
        return $this->hasMany(MeetingHubAttachment::class, 'meeting_minute_id' , 'id');
    }

    public function getDuration()
    {
        $start_time = strtotime($this->call_start_time);
        $end_time = strtotime($this->call_end_time);
        $difference = $end_time - $start_time;

        $hours = floor($difference / 3600);
        $minutes = floor(($difference % 3600) / 60);
        $seconds = $difference % 60;

        $formattedHours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $formattedSeconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);

        $duration = "$formattedHours:$formattedMinutes:$formattedSeconds";
        return $duration;
    }
}
