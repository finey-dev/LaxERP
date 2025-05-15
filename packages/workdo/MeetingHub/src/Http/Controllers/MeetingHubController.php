<?php

namespace Workdo\MeetingHub\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\HospitalManagement\Entities\Doctor;
use Workdo\LegalCaseManagement\Entities\Advocate;
use Workdo\MeetingHub\Entities\MeetingHubMeeting;
use Workdo\MeetingHub\Entities\MeetingHubModule;
use Workdo\MeetingHub\Entities\MeetingHubMeetingMinute;
use Workdo\MeetingHub\Entities\MeetingType;
use App\Models\User;
use Workdo\Lead\Entities\Lead;
use Carbon\Carbon;
use Workdo\MeetingHub\DataTables\MeetingHubDataTable;
use Workdo\MeetingHub\Events\CreateMeeingHubMeeting;
use Workdo\MeetingHub\Events\DestroyMeeingHubComment;
use Workdo\MeetingHub\Events\UpdateMeeingHubMeeting;
use Workdo\MusicInstitute\Entities\MusicTeacher;
use Workdo\School\Entities\SchoolParent;
class MeetingHubController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function report(Request $request)
    {
        if (Auth::user()->isAbleTo('meetinghub report manage')) {
            $query = MeetingHubMeeting::where('workspace_id', getActiveWorkSpace());

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $date_range = [$start_date, $end_date];
                $date_range = [$start_date, $end_date];

                $query->whereBetween('created_at', $date_range);
            } else {
                $start_date = Carbon::now()->startOfMonth()->toDateString();
                $end_date = Carbon::now()->endOfMonth()->toDateString();
                $date_range = [$start_date, $end_date];

                $query->whereBetween('created_at', $date_range);
            }

            $meetings = $query->get();


            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $date_range = [$start_date, $end_date];

                $query->whereBetween('created_at', $date_range);
            } else {
                $start_date = Carbon::now()->startOfMonth()->toDateString();
                $end_date = Carbon::now()->endOfMonth()->toDateString();
                $date_range = [$start_date, $end_date];

                $query->whereBetween('created_at', $date_range);
            }

            $meetings = $query->get();

            $previous_days = strtotime($start_date . " -1 day");
            for ($i = 0; $i < 30; $i++) {
                $previous_days = strtotime(date('Y-m-d', $previous_days) . " +1 day");
                $dataArr['month'][] = date('d-M', $previous_days);
                $date = date('Y-m-d', $previous_days);
                $meetingCount = $meetings->where('created_at', '>=', $date . ' 00:00:00')
                    ->where('created_at', '<=', $date . ' 23:59:59')
                    ->count();
                $dataArr['meetings'][] = $meetingCount;
            }
            return view('meeting-hub::meeting.report', compact('dataArr'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function index(MeetingHubDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('meetinghub manage')) {
            return $dataTable->render('meeting-hub::meeting.index');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Auth::user()->isAbleTo('meetinghub create')) {
            $status = MeetingHubMeeting::$statues;
            $users = User::where('created_by', '=', creatorId())->where('type', '!=', 'client')->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            $meetingtypes = MeetingType::where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $modules = MeetingHubModule::select('module', 'submodule')->get();
            $module_calllogfiled = [];

            foreach ($modules as $module) {
                if (module_is_active($module->module) || $module->module == 'Base') {
                    $submodule_calllogfiled = MeetingHubModule::select('module', 'submodule', 'id')->where('module', $module->module)->get();
                    $temp = [];

                    foreach ($submodule_calllogfiled as $sb) {
                        $temp[$sb->id] = $sb->submodule;
                    }
                    $module_calllogfiled[Module_Alias_Name($module->module)] = $temp;
                }
            }
            return view('meeting-hub::meeting.create', compact('status', 'users', 'meetingtypes', 'modules', 'module_calllogfiled'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('meetinghub create')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'subject' => 'required',
                    'meeting_type' => 'required',
                    'users' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $meeting_module = MeetingHubModule::find($request->module);
            $moduleName = $meeting_module->id;

            $meeting               = new MeetingHubMeeting();
            $meeting->sub_module   = $moduleName;
            $meeting->caller       = Auth::user()->name;
            $meeting->subject      = $request->subject;
            $meeting->meeting_type = $request->meeting_type;
            $meeting->description  = $request->description;
            $meeting->location     = $request->location;
            $users                 = $request->input('users', []);
            if (isset($request->leads)) {
                $leads             = $request->input('leads', []);
                $meeting->lead_id  = implode(",", array_filter($leads));
            }
            $meeting->user_id      = implode(",", array_filter($users));
            $meeting->workspace_id = getActiveWorkSpace();
            $meeting->created_by   = creatorId();
            $meeting->save();
            event(new CreateMeeingHubMeeting($request, $meeting));
            return redirect()->back()->with('success', __('The meeting has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('meeting-hub::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('meetinghub edit')) {
            $meeting = MeetingHubMeeting::find($id);
            $users = User::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            $users->prepend(__('Select Users'), '');
            $meeting->user_id  = explode(',', $meeting->user_id);
            $meetingtypes = MeetingType::where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $modules = MeetingHubModule::select('module', 'submodule')->get();
            $module_calllogfiled = [];

            foreach ($modules as $module) {
                if (module_is_active($module->module) || $module->module == 'Base') {
                    $submodule_calllogfiled = MeetingHubModule::select('module', 'submodule', 'id')->where('module', $module->module)->get();
                    $temp = [];

                    foreach ($submodule_calllogfiled as $sb) {
                        $temp[$sb->id] = $sb->submodule;
                    }
                    $module_calllogfiled[Module_Alias_Name($module->module)] = $temp;
                }
            }
            return view('meeting-hub::meeting.edit', compact('meeting', 'users', 'meetingtypes', 'module_calllogfiled'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('meetinghub edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'subject' => 'required',
                    'meeting_type' => 'required',
                    'users' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $meetings_module  = MeetingHubModule::find($request->module);
            $moduleName = $meetings_module->id;

            $meeting               = MeetingHubMeeting::find($id);
            $meeting->sub_module   = $moduleName;
            $meeting->caller       = Auth::user()->name;
            $meeting->subject      = $request->subject;
            $meeting->meeting_type = $request->meeting_type;
            $meeting->description  = $request->description;
            $meeting->location     = $request->location;
            $users                 = $request->input('users', []);
            if (isset($request->leads)) {
                $leads             = $request->input('leads', []);
                $meeting->lead_id  = implode(",", array_filter($leads));
            } else {
                $meeting->lead_id  = null;
            }
            $meeting->user_id      = implode(",", array_filter($users));
            $meeting->workspace_id = getActiveWorkSpace();
            $meeting->created_by   = creatorId();
            $meeting->save();
            event(new UpdateMeeingHubMeeting($request, $meeting));
            return redirect()->back()->with('success', __('The meeting details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('meetinghub delete')) {
            $meeting = MeetingHubMeeting::find($id);
            $meeting_minute = MeetingHubMeetingMinute::where('meeting_id',$id)->delete();
            event(new DestroyMeeingHubComment($meeting));
            $meeting->delete();

            return redirect()->back()->with('success', __('The meeting has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function getcondition(Request $request)
    {
        $meeting = [];
        if (isset($request->id)) {
            $meeting = MeetingHubMeeting::find($request->id);
        }
        $calllog = MeetingHubModule::find($request->submodule_id);
        if ($calllog != null) {
            $modelName = $calllog->model_name;
            $users = null;
            if ($modelName == 'Client') {
                $users = User::where('workspace_id', getActiveWorkSpace())->where('type', 'client')->get()->pluck('name', 'id')->toArray();
            } elseif ($modelName == 'Vendor') {
                $users = User::where('workspace_id', getActiveWorkSpace())->where('type', 'vendor')->get()->pluck('name', 'id')->toArray();
            } elseif ($modelName == 'Lead') {

                $leads = Lead::where('created_by', creatorId())
                    ->where('workspace_id', getActiveWorkSpace())
                    ->get()
                    ->pluck('name', 'id')
                    ->toArray();
                $users = [];
                if (count($leads) > 0) {
                    $lead = array_key_first($leads);
                    $lead_users = Lead::where('id', $lead)
                        ->get()
                        ->pluck('user_id')
                        ->toArray();
                    $users = User::whereIn('id', $lead_users)
                        ->where('created_by', creatorId())
                        ->where('workspace_id', getActiveWorkSpace())
                        ->get()
                        ->pluck('name', 'id')
                        ->toArray();
                }
                $returnHTML = view('meeting-hub::meeting.inputs', compact('leads', 'meeting', 'modelName', 'users'))->render();
                $response = [
                    'is_success' => true,
                    'message' => '',
                    'html' => $returnHTML,
                ];
                return response()->json($response);
            } elseif ($modelName == 'Employee') {

                $users = User::where('workspace_id', getActiveWorkSpace())->where('type', 'staff')->get()->pluck('name', 'id')->toArray();
            }elseif ($modelName == 'Teacher') {

                $users = MusicTeacher::where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
            }elseif ($modelName == 'Parent') {

                $users = SchoolParent::where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
            }
            elseif ($modelName == 'Advocate') {
                $users = User::where('workspace_id', getActiveWorkSpace())->where('type', 'advocate')->get()->pluck('name', 'id')->toArray();
            }
            elseif ($modelName == 'Agriculture User') {
                $users = User::where('workspace_id', getActiveWorkSpace())->where('type', 'farmer')->get()->pluck('name', 'id')->toArray();
            }
            elseif ($modelName == 'Agent') {
                $users = User::where('workspace_id', getActiveWorkSpace())->where('type', 'agent')->get()->pluck('name', 'id')->toArray();
            }
            elseif ($modelName == 'Journalist') {
                $users = User::where('workspace_id', getActiveWorkSpace())->where('type', 'staff')->get()->pluck('name', 'id')->toArray();
            }
            elseif ($modelName == 'Tenants') {
                $users = User::where('workspace_id', getActiveWorkSpace())->where('type', 'tenant')->get()->pluck('name', 'id')->toArray();
            }
            elseif ($modelName == 'Doctor') {
                $users = Doctor::where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
            }
             else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
            $returnHTML =  view('meeting-hub::meeting.inputs', compact('modelName', 'meeting', 'users'))->render();
            $response = [
                'is_success' => true,
                'message' => '',
                'html' => $returnHTML,
            ];
            return response()->json($response);
        }
    }

    public function updateUsersSelect(Request $request)
    {
        $meeting = [];

        if (isset($request->meetingId)) {

            $meeting = MeetingHubMeeting::find($request->meetingId);
        }
        $dropdownType = $request->input('dropdownType');
        if ($dropdownType == 'lead') {
            $users = Lead::where('leads.id', $request->selectvalue)
                ->where('leads.created_by', creatorId())
                ->where('leads.workspace_id', getActiveWorkSpace())
                ->join('users', 'leads.user_id', '=', 'users.id')
                ->select('users.name', 'leads.user_id')
                ->get()
                ->pluck('name', 'user_id')
                ->toArray();
        }
        $htmlView = view('meeting-hub::meeting.options', compact('users', 'meeting'))->render();
        $response = [
            'is_success' => true,
            'message' => '',
            'html' => $htmlView,
        ];

        return response()->json($response);
    }
}
