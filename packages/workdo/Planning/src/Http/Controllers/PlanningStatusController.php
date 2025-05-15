<?php

namespace Workdo\Planning\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Workdo\Planning\Entities\PlanningCharters;
use Workdo\Planning\Entities\PlanningStatus;
use Workdo\Planning\Events\CreatePlanningStatus;
use Workdo\Planning\Events\DestroyPlanningStatus;
use Workdo\Planning\Events\UpdatePlanningStatus;

class PlanningStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('planning status manage')) {

            $Planningstatus = PlanningStatus::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();

            return view('planning::planningstatus.index', compact('Planningstatus'));
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
        if (Auth::user()->isAbleTo('planning status create')) {

            return view('planning::planningstatus.create');
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
        if (Auth::user()->isAbleTo('planning status create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $Planningstatus             = new PlanningStatus();

            $Planningstatus->name       = $request->name;
            $Planningstatus->workspace  = getActiveWorkSpace();
            $Planningstatus->created_by = creatorId();
            $Planningstatus->save();
            event(new CreatePlanningStatus($request, $Planningstatus));
            return redirect()->route('planning-status.index')->with('success', __('The Status has been created successfully'));
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
        return view('planning::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $Planningstatus = PlanningStatus::find($id);

        if (Auth::user()->isAbleTo('planning status edit')) {
            if ($Planningstatus->created_by == creatorId() &&  $Planningstatus->workspace  == getActiveWorkSpace()) {

                return view('planning::planningstatus.edit', compact('Planningstatus'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
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

        $Planningstatus = PlanningStatus::find($id);

        if (Auth::user()->isAbleTo('planning status edit')) {
            if ($Planningstatus->created_by == creatorId() &&  $Planningstatus->workspace  == getActiveWorkSpace()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $Planningstatus->name       = $request->name;
                $Planningstatus->workspace  = getActiveWorkSpace();
                $Planningstatus->created_by = creatorId();

                $Planningstatus->save();

                event(new UpdatePlanningStatus($request, $Planningstatus));
                return redirect()->route('planning-status.index')->with('success', __('The Status details are updated successfully'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
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

        $Planningstatus = PlanningStatus::find($id);

        if (Auth::user()->isAbleTo('planning status delete')) {

            if ($Planningstatus->created_by == creatorId() &&  $Planningstatus->workspace  == getActiveWorkSpace()) {

                $Charters     = PlanningCharters::where('status',$Planningstatus->id)->where('workspace',getActiveWorkSpace())->get();
                if(count($Charters) == 0)
                {
                    event(new DestroyPlanningStatus($Planningstatus));
                    $Planningstatus->delete();
                }
                else
                {
                    return redirect()->back()->with('error', __('This status has creativity. Please remove the creativity from this status.'));
                }
                return redirect()->back()->with('success', __('The Category has been deleted'));
            } else {

                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {

            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
