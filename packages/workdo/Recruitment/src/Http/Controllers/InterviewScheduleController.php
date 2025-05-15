<?php

namespace Workdo\Recruitment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Entities\Employee;
use Workdo\Recruitment\Entities\InterviewSchedule;
use Workdo\Recruitment\Entities\JobApplication;
use Workdo\Recruitment\Entities\JobStage;
use Workdo\Recruitment\Events\CreateInterviewSchedule;
use Workdo\Recruitment\Events\DestroyInterviewSchedule;
use Workdo\Recruitment\Events\UpdateInterviewSchedule;

class InterviewScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('interview schedule manage')) {
            $schedules   = InterviewSchedule::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $arrSchedule = [];
            $today_date = date('m');
            $current_month_event = InterviewSchedule::select('id', 'candidate', 'date', 'employee', 'time', 'comment')->where('workspace', getActiveWorkSpace())->whereNotNull(['date'])->whereMonth('date', $today_date)->with('applications')->get();
            foreach ($schedules as $schedule) {
                $arr['id']     = $schedule['id'];
                $arr['title']  = !empty($schedule->applications) ? (!empty($schedule->applications->jobs) ? $schedule->applications->jobs->title : '') : '';
                $arr['start']  = $schedule['date'];
                $arr['url']    = route('interview-schedule.show', $schedule['id']);
                $arr['className'] = ' event-danger';
                $arrSchedule[] = $arr;
            }
            $arrSchedule = json_encode($arrSchedule);
            return view('recruitment::interviewSchedule.index', compact('arrSchedule', 'schedules', 'current_month_event'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($candidate = 0)
    {
        if (Auth::user()->isAbleTo('interview schedule create')) {
            $employees = Employee::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name', 'id');
            $employees->prepend('Select Employee', '');

            $candidates = JobApplication::where('is_employee', '!=', 1)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $candidates->prepend('Select Job Candidate', '');

            if ($candidate != 0) {
                $candidates = JobApplication::where('id', $candidate)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
                $candidates->prepend('Select Job Candidate', '');
            }

            $meetings = [];
            if (module_is_active('ZoomMeeting')) {
                $meetings[] = 'ZoomMeeting';
            }
            if (module_is_active('GoogleMeet')) {
                $meetings[] = 'GoogleMeet';
            }

            return view('recruitment::interviewSchedule.create', compact('employees', 'candidates', 'candidate', 'meetings'));
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
        if (Auth::user()->isAbleTo('interview schedule create')) {

            $rules = [
                'candidate' => 'required',
                'date' => 'required|after:yesterday',
                'time' => 'required',
            ];

            if (module_is_active('Hrm') && $request->has('employee')) {
                $rules['employee'] = 'required';
            }

            $validator = \Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $schedule             = new InterviewSchedule();
            $employee = Employee::where('id', '=', $request->employee)->first();
            if (!empty($employee)) {
                $schedule->user_id  = !empty($employee->user_id) ? $employee->user_id : '';
            }
            $schedule->candidate    = $request->candidate;
            $schedule->employee     = !empty($request->employee) ? $request->employee : '';
            $schedule->date         = $request->date;
            $schedule->time         = $request->time;
            $schedule->meeting_type = !empty($request->meeting_type) ? $request->meeting_type : '';
            if ($request->meeting_type == 'ZoomMeeting') {
                $schedule->start_time   = !empty($request->zoom_start_time) ? $request->zoom_start_time : '';
                $schedule->end_time     = !empty($request->zoom_end_time) ? $request->zoom_end_time : '';
            }elseif ($request->meeting_type == 'Google Meet') {
                $schedule->start_time   = !empty($request->google_start_time) ? $request->google_start_time : '';
                $schedule->end_time     = !empty($request->google_end_time) ? $request->google_end_time : '';
            } else{
                $schedule->start_time   = '';
                $schedule->end_time     = '';
            }
            $schedule->comment      = $request->comment;
            $schedule->workspace    = getActiveWorkSpace();
            $schedule->created_by   = creatorId();
            $schedule->save();

            event(new CreateInterviewSchedule($request, $schedule));

            return redirect()->back()->with('success', __('The interview schedule has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(InterviewSchedule $interviewSchedule)
    {
        if (Auth::user()->isAbleTo('interview schedule show')) {
            $stages = JobStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            return view('recruitment::interviewSchedule.show', compact('interviewSchedule', 'stages'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(InterviewSchedule $interviewSchedule)
    {
        if (Auth::user()->isAbleTo('interview schedule edit')) {
            $employees = Employee::where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $employees->prepend('Select Employee', '');

            $candidates = JobApplication::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $candidates->prepend('Select Interviewer', '');

            return view('recruitment::interviewSchedule.edit', compact('employees', 'candidates', 'interviewSchedule'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, InterviewSchedule $interviewSchedule)
    {
        if (Auth::user()->isAbleTo('interview schedule edit')) {
            $rules = [
                'candidate' => 'required',
                'date' => 'required|after:yesterday',
                'time' => 'required',
            ];

            if (module_is_active('Hrm') && $request->has('employee')) {
                $rules['employee'] = 'required';
            }

            $validator = \Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }


            $interviewSchedule->candidate = $request->candidate;
            $interviewSchedule->employee  = !empty($request->employee) ? $request->employee : 0;
            $interviewSchedule->date      = $request->date;
            $interviewSchedule->time      = $request->time;
            $interviewSchedule->comment   = $request->comment;
            $interviewSchedule->save();
            event(new UpdateInterviewSchedule($request, $interviewSchedule));
            return redirect()->back()->with('success', __('The interview schedule details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(InterviewSchedule $interviewSchedule)
    {
        if (Auth::user()->isAbleTo('interview schedule delete')) {
            event(new DestroyInterviewSchedule($interviewSchedule));
            $interviewSchedule->delete();

            return redirect()->back()->with('success', __('The interview schedule has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
