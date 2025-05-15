<?php

namespace Workdo\MeetingHub\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\MeetingHub\Entities\MeetingType;
use Workdo\MeetingHub\Entities\MeetingHubMeeting;
use Illuminate\Support\Facades\Auth;
use Workdo\MeetingHub\DataTables\MeetingTypeDataTable;
use Workdo\MeetingHub\Events\CreateMeeingHubMeetingType;
use Workdo\MeetingHub\Events\DestroyMeeingHubMeetingType;
use Workdo\MeetingHub\Events\UpdateMeeingHubMeetingType;
class MeetingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(MeetingTypeDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('meetingtype manage')) {
            return $dataTable->render('meeting-hub::meetingtype.index');
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
        if (Auth::user()->isAbleTo('meetingtype create')) {
            return view('meeting-hub::meetingtype.create');
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
        if (Auth::user()->isAbleTo('meetingtype create')) {
            $validator = \Validator::make(
                $request->all(),
                [

                    'name' => 'required|max:30',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $meetingtype             = new MeetingType();
            $meetingtype->name       = $request->name;
            $meetingtype->workspace   = getActiveWorkSpace();
            $meetingtype->created_by = creatorId();
            $meetingtype->save();
            event(new CreateMeeingHubMeetingType($request, $meetingtype));
            return redirect()->route('meeting-type.index')->with('success', __('The meeting type has been created successfully.'));
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
        return view('meeting-hub::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('meetingtype edit')) {
            $meetingtype = MeetingType::find($id);
            return view('meeting-hub::meetingtype.edit', compact('meetingtype'));
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
        if (Auth::user()->isAbleTo('meetingtype edit')) {
            $meetingtype = MeetingType::find($id);
            $meetingtype->name  = $request->name;
            $meetingtype->save();
            event(new UpdateMeeingHubMeetingType($request, $meetingtype));
            return redirect()->route('meeting-type.index')->with('success', __('The meeting type details are updated successfully.'));
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
        if (Auth::user()->isAbleTo('meetingtype delete')) {
            $meetings = MeetingHubMeeting::where('meeting_type', $id)->get();
            if (count($meetings) == 0) {
                $meetingType = MeetingType::find($id);
                event(new DestroyMeeingHubMeetingType($meetingType));
                $meetingType->delete();
                
                return redirect()->route('meeting-type.index')->with('success', __('The meeting type has been deleted.'));
            } else {
                return redirect()->route('meeting-type.index')->with('error', __('The meeting type is used on meeting.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
