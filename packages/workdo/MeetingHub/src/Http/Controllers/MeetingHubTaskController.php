<?php

namespace Workdo\MeetingHub\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\MeetingHub\Entities\MeetingHubTask;
use Workdo\MeetingHub\Entities\MeetingHubMeeting;
use Illuminate\Support\Facades\Auth;
use Workdo\MeetingHub\Events\CreateMeeingHubTask;
use Workdo\MeetingHub\Events\DestroyMeeingHubTask;
use Workdo\MeetingHub\Events\UpdateMeeingHubTask;
class MeetingHubTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('meeting-hub::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($id=null)
    {
        if (Auth::user()->isAbleTo('meetingTask create')) {
            $status = MeetingHubMeeting::$statues;
            return view('meeting-hub::task.create', compact('status','id'));
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
        if (Auth::user()->isAbleTo('meetingTask create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:30',
                    'date' => 'required',
                    'time' => 'required',
                    'priority' => 'required',
                    'status' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $task                    = new MeetingHubTask();
            $task->meeting_minute_id = $request->meeting_minute_id;
            $task->name              = $request->name;
            $task->date              = $request->date;
            $task->time              = $request->time;
            $task->priority          = $request->priority;
            $task->status            = $request->status;
            $task->workspace_id      = getActiveWorkSpace();
            $task->created_by        = creatorId();
            $task->save();
            event(new CreateMeeingHubTask($request, $task));
            return redirect()->back()->with('success', __('The task has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function assign($id)
    {

    }
    

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('meetingTask edit')) {
            $status = MeetingHubMeeting::$statues;
            $task = MeetingHubTask::find($id);
            return view('meeting-hub::task.edit', compact('task', 'status'));
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
        if (Auth::user()->isAbleTo('meetingTask edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:30',
                    'date' => 'required',
                    'time' => 'required',
                    'priority' => 'required',
                    'status' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $task                    = MeetingHubTask::find($id);
            $task->name              = $request->name;
            $task->date              = $request->date;
            $task->time              = $request->time;
            $task->priority          = $request->priority;
            $task->status            = $request->status;
            $task->workspace_id      = getActiveWorkSpace();
            $task->created_by        = creatorId();
            $task->save();
            event(new UpdateMeeingHubTask($request, $task));
            return redirect()->back()->with('success', __('The task details are updated successfully.'));
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
        if (Auth::user()->isAbleTo('meetingTask delete')) {
            $task = MeetingHubTask::find($id);
            event(new DestroyMeeingHubTask($task));
            $task->delete();
            
            return redirect()->back()->with('success', __('The task has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
