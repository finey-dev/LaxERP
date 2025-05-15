<?php

namespace Workdo\MeetingHub\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
class MeetingHubMeeting extends Model
{
    use HasFactory;

    protected $table = "meetinghub_meetings";
    protected $fillable = [
        'sub_module',
        'caller',
        'user_id',
        'lead_id',
        'meeting_type',
        'location',
        'subject',
        'description',
        'created_by',
        'workspace_id',
    ];
    public static $statues = [
        'Completed',
        'Busy',
        'No Answer',
        'Cancelled',

    ];
    
    public function workspace()
    {
        return $this->hasOne('App\Models\WorkSpace', 'id', 'workspace_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'assign_user');
    }

    public function meetingtype()
    {
        return $this->hasOne('Workdo\MeetingHub\Entities\MeetingType', 'id', 'meeting_type');
    }


    public function submodules()
    {
        return $this->hasOne('Workdo\MeetingHub\Entities\MeetingHubModule', 'id', 'sub_module');
    }

    public static function getSubModuleUser()
    {
        $meetings = MeetingHubMeeting::with('workspace')
                ->select([
                    'meetinghub_meetings.*',
                    'meetinghub_meeting_types.name as meeting_type',
                ])
                ->join('meetinghub_meeting_types', 'meetinghub_meeting_types.id', '=', 'meetinghub_meetings.meeting_type')
                ->where('meetinghub_meetings.workspace_id', getActiveWorkSpace())
                ->get();

            $meetinglogusers = [];
            foreach ($meetings as $calluser) {
                $userIds = explode(',', $calluser->user_id);
                $userNames = User::whereIn('id', $userIds)->pluck('name')->toArray();
                $meetinglogusers[$calluser->id] = implode(', ', $userNames);
            }
        return $meetinglogusers;
    }
}
