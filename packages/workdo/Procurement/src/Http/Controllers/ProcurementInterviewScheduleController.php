<?php

namespace Workdo\Procurement\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Procurement\Entities\ProcurementInterviewSchedule;
use Workdo\Procurement\Entities\RfxApplication;
use Workdo\Procurement\Entities\RfxStage;
use Workdo\Procurement\Events\CreateRfxInterviewSchedule;
use Workdo\Procurement\Events\DestroyRfxInterviewSchedule;
use Workdo\Procurement\Events\UpdateRfxInterviewSchedule;

class ProcurementInterviewScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('rfx interview schedule manage')) {
            $schedules = ProcurementInterviewSchedule::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $arrSchedule = [];
            $today_date = date('m');
            $current_month_event = ProcurementInterviewSchedule::select('id', 'applicant', 'date', 'employee', 'time', 'comment')->where('workspace', getActiveWorkSpace())->whereNotNull(['date'])->whereMonth('date', $today_date)->with('applications')->get();
            foreach ($schedules as $schedule) {
                $arr['id'] = $schedule['id'];
                $arr['title'] = !empty($schedule->applications) ? (!empty($schedule->applications->rfxs) ? $schedule->applications->rfxs->title : '') : '';
                $arr['start'] = $schedule['date'];
                $arr['url'] = route('rfx-interview-schedule.show', $schedule['id']);
                $arr['className'] = ' event-danger';
                $arrSchedule[] = $arr;
            }
            $arrSchedule = json_encode($arrSchedule);

            return view('procurement::interviewSchedule.index', compact('arrSchedule', 'schedules', 'current_month_event'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($applicant = 0)
    {
        if (Auth::user()->isAbleTo('rfx interview schedule create')) {

            $applicants = RfxApplication::where('is_vendor', '!=', 1)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $applicants->prepend('Select Interviewer', '');

            return view('procurement::interviewSchedule.create', compact('applicants', 'applicant'));
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
        if (Auth::user()->isAbleTo('rfx interview schedule create')) {

            $rules = [
                'applicant' => 'required',
                'date' => 'required|after:yesterday',
                'time' => 'required',
            ];

            $validator = \Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }


            $schedule = new ProcurementInterviewSchedule();

            $schedule->applicant = $request->applicant;
            $schedule->date = $request->date;
            $schedule->time = $request->time;
            $schedule->comment = $request->comment;
            $schedule->workspace = getActiveWorkSpace();
            $schedule->created_by = creatorId();
            $schedule->save();


            event(new CreateRfxInterviewSchedule($request, $schedule));

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
    public function show($id)
    {
        if (Auth::user()->isAbleTo('rfx interview schedule show')) {
            $stages = RfxStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $interviewSchedule = ProcurementInterviewSchedule::find($id);
            if ($interviewSchedule) {
                return view('procurement::interviewSchedule.show', compact('interviewSchedule', 'stages'));
            } else {
                return response()->json(['error' => __('The interview detail is not found.')], 401);
            }

        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function interviewDetail($id)
    {
        if (Auth::user()->isAbleTo('rfx interview schedule show')) {
            $interviewSchedule = ProcurementInterviewSchedule::find($id);
            if ($interviewSchedule) {
                $user = User::where('id', $interviewSchedule->created_by)->first();
                $stages = RfxStage::where('created_by', $user->id)->where('workspace', $user->workspace_id)->get();
                return view('procurement::rfx.interview_show', compact('interviewSchedule', 'stages'));
            } else {
                return redirect()->back()->with('error', __('The interview detail is not found.'));
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('rfx interview schedule edit')) {
            $interviewSchedule = ProcurementInterviewSchedule::find($id);
            if ($interviewSchedule) {
                $applicants = RfxApplication::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
                $applicants->prepend('Select Interviewer', '');

                return view('procurement::interviewSchedule.edit', compact('applicants', 'interviewSchedule'));
            } else {
                return response()->json(['error' => __('The interview detail is not found.')], 401);
            }

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
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('rfx interview schedule edit')) {
            $rules = [
                'applicant' => 'required',
                'date' => 'required|after:yesterday',
                'time' => 'required',
            ];


            $validator = \Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $interviewSchedule = ProcurementInterviewSchedule::find($id);
            if ($interviewSchedule) {
                $interviewSchedule->applicant = $request->applicant;
                $interviewSchedule->date = $request->date;
                $interviewSchedule->time = $request->time;
                $interviewSchedule->comment = $request->comment;
                $interviewSchedule->save();
                event(new UpdateRfxInterviewSchedule($request, $interviewSchedule));
                return redirect()->back()->with('success', __('The interview schedule are updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('The interview detail is not found.'));
            }

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
        if (Auth::user()->isAbleTo('rfx interview schedule delete')) {
            $interviewSchedule = ProcurementInterviewSchedule::find($id);
            if ($interviewSchedule) {
                event(new DestroyRfxInterviewSchedule($interviewSchedule));
                $interviewSchedule->delete();
                return redirect()->back()->with('success', __('The interview schedule has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('The interview detail is not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
