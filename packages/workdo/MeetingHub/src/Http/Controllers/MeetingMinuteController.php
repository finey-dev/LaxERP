<?php

namespace Workdo\MeetingHub\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\MeetingHub\Entities\MeetingHubMeeting;
use Workdo\MeetingHub\Entities\MeetingHubNote;
use Workdo\MeetingHub\Entities\MeetingHubAttachment;
use Workdo\MeetingHub\Entities\MeetingHubComment;
use Workdo\MeetingHub\Entities\MeetingHubMeetingMinute;
use Workdo\MeetingHub\Entities\MeetingHubTask;
use Workdo\MeetingHub\Events\MeetingHubTwilioMsg;
use Workdo\MeetingHub\Events\CreateMeeingHubAttachment;
use Workdo\MeetingHub\Events\CreateMeeingHubComment;
use Workdo\MeetingHub\Events\CreateMeeingHubMeetingMinute;
use Workdo\MeetingHub\Events\CreateMeeingHubNote;
use Workdo\MeetingHub\Events\DestroyMeeingHubAttachment;
use Workdo\MeetingHub\Events\DestroyMeeingHubComment;
use Workdo\MeetingHub\Events\DestroyMeeingHubMeetingMinute;
use Workdo\MeetingHub\Events\DestroyMeeingHubNote;
use App\Models\User;
use Workdo\Lead\Entities\Lead;
use Workdo\MeetingHub\DataTables\MeetingMinuteDataTable;

class MeetingMinuteController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(MeetingMinuteDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('meetinghub manage')) {
            return $dataTable->render('meeting-hub::meeting_minute.index');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function meeting_minute($id)
    {
        if (Auth::user()->isAbleTo('meetinghub create')) {
            $meeting = MeetingHubMeeting::find($id);
            $user_id = explode(',', $meeting->user_id);
            $status = MeetingHubMeeting::$statues;
            $meeting_users = User::whereIn('id', $user_id)->get();
            $users = User::where('created_by', '=', creatorId())
                ->where('type', '!=', 'client')
                ->where('workspace_id', getActiveWorkSpace())
                ->get()
                ->pluck('name', 'id');
            return view('meeting-hub::meeting_minute.create', compact('status', 'users', 'meeting', 'meeting_users'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function  meeting_minute_update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('meetinghub create')) {


            $validator = \Validator::make(
                $request->all(),
                [
                    'status' => 'required',
                    'priority' => 'required',
                    'note' => 'required',
                    'phone_call' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $meeting = MeetingHubMeeting::find($id);
            $meeting_minute = new MeetingHubMeetingMinute();
            $meeting_minute->meeting_id = $meeting->id;
            $meeting_minute->caller = Auth::user()->name;
            $meeting_minute->contact_user = $request->contact_user;
            if ($request->log_type == 'Call') {
                $meeting_minute->phone_no = $request->phone_call;
            } else if ($request->log_type == 'SMS') {
                $meeting_minute->phone_no = $request->phone_sms;
            }
            $meeting_minute->call_start_time = $request->call_start_time;
            $meeting_minute->call_end_time = $request->call_end_time;
            $meeting_minute->log_type = $request->log_type;
            $duration = $meeting_minute->getDuration();
            $meeting_minute->duration = $duration;
            $meeting_minute->important = $request->important;
            $meeting_minute->completed = $request->completed;
            $meeting_minute->status = $request->status;
            $meeting_minute->priority = $request->priority;
            $meeting_minute->assign_user = $request->assign_user;
            $meeting_minute->note = $request->note;
            $meeting_minute->workspace_id = getActiveWorkSpace();
            $meeting_minute->created_by = creatorId();
            $meeting_minute->save();
            event(new CreateMeeingHubMeetingMinute($request, $meeting_minute));
            return redirect()->route('meeting-minutes.index')->with('success', __('The meeting minute details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function  getNumber(Request $request)
    {
        $user = User::find($request->user_id);

        if ($user) {
            $userData = [
                'name' => $user->name,
                'mobile_no' => $user->mobile_no,
            ];
            return response()->json($userData);
        } else {
            return response()->json(['error' => 'User not found']);
        }
    }
    public function sendSms(Request $request)
    {
        $phoneNumber = $request->input('phone');
        $message = $request->input('message');


        if ((!empty($phoneNumber)) && (!empty($message))) {
            event(new MeetingHubTwilioMsg($phoneNumber, $message));


            $data = [];
            $data['success'] = 1;
            $data['msg'] = "SMS sent successfully";
            return $data;
        } else {
            return response()->json(['error' => 'Invalid Twilio settings'], 400);
        }
    }
    public function calculateDuration(Request $request)
    {
        $start_time = strtotime($request->input('start_time'));
        $end_time = strtotime($request->input('end_time'));
        $difference = $end_time - $start_time;
        $hours = floor($difference / 3600);
        $minutes = floor(($difference % 3600) / 60);
        $formattedHours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $duration = "$formattedHours:$formattedMinutes";
        return $duration;
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('meeting-hub::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if (Auth::user()->isAbleTo('meetinghub show')) {
            $meeting_minute = MeetingHubMeetingMinute::where('id', $id)->where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->first();
            if ($meeting_minute) {
                $meeting        = MeetingHubMeeting::where('id', $meeting_minute->meeting_id)->where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->first();
                $userIds = explode(',', $meeting->user_id);
                $userNames = User::whereIn('id', $userIds)->pluck('name')->toArray();
                $meetinglogusers = implode(', ', $userNames);
                $files = MeetingHubAttachment::where('meeting_minute_id', $meeting_minute->id)->get();
                $submodule = \Workdo\MeetingHub\Entities\MeetingHubModule::find($meeting->sub_module);
                if ($submodule->submodule == 'Lead') {
                    $lead = Lead::where('id', $meeting->lead_id)->first();
                }
                $meetingleadname = isset($lead->name) ? $lead->name : "";
                $notes = MeetingHubNote::where('meeting_minute_id', $meeting_minute->id)->get();
                $comments = MeetingHubComment::where('meeting_minute_id',$meeting_minute->id)->get();
                $tasks = MeetingHubTask::where('meeting_minute_id',$meeting_minute->id)->get();
                $status = MeetingHubMeeting::$statues;
                return view('meeting-hub::meeting_minute.show', compact('tasks','comments','files', 'notes', 'status', 'meeting_minute', 'meeting', 'meetinglogusers', 'meetingleadname'));
            } else {
                return redirect()->back()->with('error', __('Record not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function commentStore(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('meetinghub comment create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'comment' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $comment                     = new MeetingHubComment();
            $comment->comment            = $request->comment;
            $comment->meeting_minute_id  = $id;
            $comment->user_id            = Auth::user()->id;
            $comment->workspace_id       = getActiveWorkSpace();
            $comment->created_by         = creatorId();
            $comment->save();
            event(new CreateMeeingHubComment($request, $comment));
            return redirect()->back()->with('success', __('The comments has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function commentDestroy($id)
    {
        if (Auth::user()->isAbleTo('meetinghub comment delete')) {
            $comment = MeetingHubComment::find($id);
            event(new DestroyMeeingHubComment($comment));
            $comment->delete();
            return redirect()->back()->with('success', __('The comment has been deleted!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function noteStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('meetinghub note create')) {
            $meeting_minute              = MeetingHubMeetingMinute::find($id);
            $notes                       = new MeetingHubNote();
            $notes->meeting_minute_id    = $meeting_minute->id;
            $notes->note                 = $request->note;
            $notes->user_id              = Auth::user()->id;
            $notes->workspace_id         = getActiveWorkSpace();
            $notes->created_by           = creatorId();
            $notes->save();
            event(new CreateMeeingHubNote($request, $notes));
            return redirect()->back()->with('success', __('The note has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
    public function noteDestroy($id)
    {
        if (Auth::user()->isAbleTo('meetinghub note delete')) {
            $meeting_minute_note = MeetingHubNote::find($id);
            if ($meeting_minute_note->created_by == creatorId() && $meeting_minute_note->workspace_id == getActiveWorkSpace()) {
                event(new DestroyMeeingHubNote($meeting_minute_note));
                $meeting_minute_note->delete();
                return redirect()->back()->with('success', __('The note has been deleted!'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
    public function descriptionStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('meetinghub edit')) {
            $minute              = MeetingHubMeetingMinute::find($id);
            $minute->description = $request->description;
            $minute->save();
            return redirect()->back()->with('success', __('The Description has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
    public function fileUpload($id, Request $request)
    {
        $meeting_minute = MeetingHubMeetingMinute::find($id);

        if ($meeting_minute->created_by == creatorId() && $meeting_minute->workspace_id == getActiveWorkSpace()) {
            $request->validate(['file' => 'required']);
            $files = $request->file->getClientOriginalName();
            $path = upload_file($request, 'file', $files, 'meeting_attachment');
            if ($path['flag'] == 1) {
                $file = $path['url'];
            } else {
                return redirect()->back()->with('error', __($path['msg']));
            }
            $file                 = MeetingHubAttachment::create(
                [
                    'meeting_minute_id' => $request->meeting_minute_id,
                    'user_id' => Auth::user()->id,
                    'workspace_id' => getActiveWorkSpace(),
                    'created_by' => creatorId(),
                    'files' => $file,
                ]
            );
            event(new CreateMeeingHubAttachment($meeting_minute, $file));
            $return               = [];
            $return['is_success'] = true;
            $return['download']   = route(
                'meeting.minute.file.download',
                [
                    $meeting_minute->id,
                    $file->id,
                ]
            );
            $return['delete']     = route(
                'meeting.minute.file.delete',
                [
                    $meeting_minute->id,
                    $file->id,
                ]
            );

            return response()->json(
                [
                    'is_success' => true,
                    'success' => __('The status has been changed successfully.'),
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ],
                401
            );
        }
    }
    public function fileDownload($id, $file_id)
    {
        $meeting_minute = MeetingHubMeetingMinute::find($id);
        if ($meeting_minute->created_by == creatorId() && $meeting_minute->workspace_id == getActiveWorkSpace()) {
            $file = MeetingHubAttachment::find($file_id);
            if ($file) {
                $file_path = get_base_file($file->files);

                return \Response::download(
                    $file_path,
                    $file->files,
                    [
                        'Content-Length: ' . get_size($file_path),
                    ]
                );
            } else {
                return redirect()->back()->with('error', __('File is not exist.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function fileDelete($id, $file_id)
    {

        $meeting_minute = MeetingHubMeetingMinute::find($id);
        $file = MeetingHubAttachment::find($file_id);
        if ($file) {
            $path = get_base_file($file->files);
            if (file_exists($path)) {
                \File::delete($path);
            }
            event(new DestroyMeeingHubAttachment($file));
            $file->delete();


            return redirect()->back()->with('success', __('Attechment successfully delete.'));
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('File is not exist.'),
                ],
                200
            );
        }
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('meeting-hub::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('meetinghub delete')) {
            $meeting_minute = MeetingHubMeetingMinute::find($id);
            event(new DestroyMeeingHubMeetingMinute($meeting_minute));
            $meeting_minute->delete();


            return redirect()->route('meeting-minutes.index')->with('success', __('The meeting minute has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
