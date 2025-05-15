<?php

namespace Workdo\TeamWorkload\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\TeamWorkload\DataTables\WorkloadTimesheetDataTable;
use Workdo\TeamWorkload\Entities\WorkloadTimesheet;

class WorkloadTimesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(WorkloadTimesheetDataTable $dataTable)
    {
        if(\Auth::user()->isAbleTo('workload manage'))
        {
            return $dataTable->render('team-workload::workload-timesheet.index');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $user = User::where('workspace_id', getActiveWorkSpace())->where('created_by', '=', creatorId())->emp()->get()->pluck('name', 'id');
        $mytime = \Carbon\Carbon::now();
        $date = $mytime->toDateString();
        return view('team-workload::workload-timesheet.create', compact('user', 'date'));
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('workload overview manage')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if ($request->user_id) {
                $user_id = $request->user_id;
            } else {
                $user_id = \Auth::user()->id;
            }

            $timesheet                 = new WorkloadTimesheet();
            $timesheet['user_id']      = $user_id;
            $timesheet['date']         = $request->date;
            $timesheet['hours']        = $request->hours;
            $timesheet['minutes']      = $request->minutes;
            $timesheet['notes']        = $request->notes;
            $timesheet['workspace_id'] = getActiveWorkSpace();
            $timesheet['created_by']   = creatorId();
            $timesheet->save();

            return redirect()->back()->with('success', __('Timesheet Successfully Created.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $user = User::where('workspace_id', getActiveWorkSpace())->where('created_by', '=', creatorId())->emp()->get()->pluck('name', 'id');
        $mytime = \Carbon\Carbon::now();
        $date = $mytime->toDateString();
        $timesheet        = WorkloadTimesheet::find($id);
        $hours = $timesheet->hours;
        $minutes = $timesheet->minutes;
        return view('team-workload::workload-timesheet.edit', compact('timesheet','user','hours','minutes','date'));
    }



    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request,$id)
    {

        if(\Auth::user()->isAbleTo('workload holidays edit'))
        {
            $timesheet        = WorkloadTimesheet::find($id);
            $validator = \Validator::make(
                    $request->all(), [
                                'date' => 'required',
                                'hours' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('workload-timesheet.index')->with('error', $messages->first());
                }

                if($request->user_id)
                {
                    $user_id = $request->user_id;
                }
                else{
                    $user_id = \Auth::user()->id;
                }

                $timesheet['user_id']      = $user_id;
                $timesheet['date']         = $request->date;
                $timesheet['hours']        = $request->hours;
                $timesheet['minutes']      = $request->minutes;
                $timesheet['notes']        = $request->notes;
                $timesheet['workspace_id'] = getActiveWorkSpace();
                $timesheet['created_by']   = creatorId();
                $timesheet->save();

                return redirect()->back()->with('success', __('Timesheet successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if(\Auth::user()->isAbleTo('workload holidays delete'))
        {
            $timesheet        = WorkloadTimesheet::find($id);
            $timesheet->delete();

            return redirect()->back()->with('success', __('Timesheet Successfully Deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function totalhours(Request $request)
    {
        $hours = date("H:i", 0);
        return response()->json($hours);
    }
}
