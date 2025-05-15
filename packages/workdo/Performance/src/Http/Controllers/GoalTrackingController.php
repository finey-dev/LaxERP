<?php

namespace Workdo\Performance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Workdo\Performance\Entities\GoalTracking;
use Workdo\Hrm\Entities\Branch;
use Workdo\Performance\Entities\GoalType;
use Illuminate\Routing\Controller;
use Workdo\Performance\DataTables\GoalTrackingDataTable;
use Workdo\Performance\Events\CreateGoalTracking;
use Workdo\Performance\Events\DestroyGoalTracking;
use Workdo\Performance\Events\UpdateGoalTracking;

class GoalTrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(GoalTrackingDataTable $dataTable)
    {
        if (\Auth::user()->isAbleTo('goaltracking manage')) {

            return $dataTable->render('performance::goaltracking.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (\Auth::user()->isAbleTo('goaltracking create')) {
            $brances = Branch::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $brances->prepend('Select Branch', '');
            $goalTypes = GoalType::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $goalTypes->prepend('Select Goal Type', '');

            if (module_is_active('CustomField')) {
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'performance')->where('sub_module', 'Goal Tracking')->get();
            } else {
                $customFields = null;
            }
            return view('performance::goaltracking.create', compact('brances', 'goalTypes', 'customFields'));
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
        if (\Auth::user()->isAbleTo('goaltracking create')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'branch' => 'required',
                    'goal_type' => 'required',
                    'start_date' => 'required|after:yesterday',
                    'end_date' => 'required|after_or_equal:start_date',
                    'subject' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $goalTracking                     = new GoalTracking();
            $goalTracking->branch             = $request->branch;
            $goalTracking->goal_type          = $request->goal_type;
            $goalTracking->start_date         = $request->start_date;
            $goalTracking->end_date           = $request->end_date;
            $goalTracking->subject            = $request->subject;
            $goalTracking->target_achievement = $request->target_achievement;
            $goalTracking->description        = $request->description;
            $goalTracking->workspace          = getActiveWorkSpace();
            $goalTracking->created_by         = creatorId();
            $goalTracking->save();

            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($goalTracking, $request->customField);
            }
            event(new CreateGoalTracking($request, $goalTracking));

            return redirect()->back()->with('success', __('The goal tracking has been created successfully.'));
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
        return redirect()->back();
        return view('performance::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (\Auth::user()->isAbleTo('goaltracking edit')) {
            $goalTracking = GoalTracking::find($id);
            $brances      = Branch::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $brances->prepend('Select Branch', '');
            $goalTypes = GoalType::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $goalTypes->prepend('Select Goal Type', '');
            $status = GoalTracking::$status;
            if (module_is_active('CustomField')) {
                $goalTracking->customField = \Workdo\CustomField\Entities\CustomField::getData($goalTracking, 'performance', 'Goal Tracking');
                $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'performance')->where('sub_module', 'Goal Tracking')->get();
            } else {
                $customFields = null;
            }

            return view('performance::goaltracking.edit', compact('brances', 'goalTypes', 'goalTracking', 'status', 'customFields'));
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
        if (\Auth::user()->isAbleTo('goaltracking edit')) {
            $goalTracking = GoalTracking::find($id);
            $validator    = \Validator::make(
                $request->all(),
                [
                    'branch' => 'required',
                    'goal_type' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'subject' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $goalTracking->branch             = $request->branch;
            $goalTracking->goal_type          = $request->goal_type;
            $goalTracking->start_date         = $request->start_date;
            $goalTracking->end_date           = $request->end_date;
            $goalTracking->subject            = $request->subject;
            $goalTracking->target_achievement = $request->target_achievement;
            $goalTracking->status             = $request->status;
            $goalTracking->progress           = $request->progress;
            $goalTracking->description        = $request->description;
            $goalTracking->rating             = $request->rating;
            $goalTracking->save();
            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($goalTracking, $request->customField);
            }
            event(new UpdateGoalTracking($request, $goalTracking));

            return redirect()->back()->with('success', __('The goal tracking details are updated successfully.'));
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
        if (\Auth::user()->isAbleTo('goaltracking delete')) {
            $goalTracking = GoalTracking::find($id);
            if ($goalTracking->created_by == creatorId()  &&  $goalTracking->workspace  == getActiveWorkSpace()) {
                if (module_is_active('CustomField')) {
                    $customFields = \Workdo\CustomField\Entities\CustomField::where('module', 'Performance')->where('sub_module', 'Goal Tracking')->get();
                    foreach ($customFields as $customField) {
                        $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $goalTracking->id)->where('field_id', $customField->id)->first();
                        if (!empty($value)) {
                            $value->delete();
                        }
                    }
                }
                event(new DestroyGoalTracking($goalTracking));

                $goalTracking->delete();

                return redirect()->route('goaltracking.index')->with('success', __('The goal tracking has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function grid()
    {
        if (\Auth::user()->isAbleTo('goaltracking manage')) {
            $user = \Auth::user();
            if (!in_array(\Auth::user()->type, \Auth::user()->not_emp_type)) {
                $employee      = \Workdo\Hrm\Entities\Employee::where('user_id', $user->id)->first();
                $goalTrackings = GoalTracking::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->where('branch', $employee->branch_id);
                $goalTrackings = $goalTrackings->paginate(11);
            } else {
                $goalTrackings = GoalTracking::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                $goalTrackings = $goalTrackings->paginate(11);
            }

            return view('performance::goaltracking.grid', compact('goalTrackings'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
