<?php

namespace Workdo\Planning\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Workdo\Planning\Entities\PlanningCharters;
use Workdo\Planning\Entities\PlanningStage;
use Workdo\Planning\Events\CreatePlanningStage;
use Workdo\Planning\Events\DestroyPlanningStage;
use Workdo\Planning\Events\UpdatePlanningStage;

class PlanningStageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('planning stage manage')) {

            $Planningstages = PlanningStage::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();

            return view('planning::planningstage.index', compact('Planningstages'));
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
        if (Auth::user()->isAbleTo('planning stage create')) {

            return view('planning::planningstage.create');
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
        if (Auth::user()->isAbleTo('planning stage create')) {
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

            $Planningstages             = new PlanningStage();

            $Planningstages->name       = $request->name;
            $Planningstages->workspace  = getActiveWorkSpace();
            $Planningstages->created_by = creatorId();
            $Planningstages->save();
            event(new CreatePlanningStage($request, $Planningstages));
            return redirect()->route('planning-stage.index')->with('success', __('The Stage has been created successfully.'));
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
        if (Auth::user()->isAbleTo('planning stage edit')) {

            $Planningstage = PlanningStage::find($id);

            if ($Planningstage->created_by == creatorId() &&  $Planningstage->workspace  == getActiveWorkSpace()) {

                return view('planning::planningstage.edit', compact('Planningstage'));
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

        if (Auth::user()->isAbleTo('planning stage edit')) {
            $Planningstage = PlanningStage::find($id);

            if ($Planningstage->created_by == creatorId() &&  $Planningstage->workspace  == getActiveWorkSpace()) {
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

                $Planningstage->name       = $request->name;
                $Planningstage->workspace  = getActiveWorkSpace();
                $Planningstage->created_by = creatorId();

                $Planningstage->save();
                event(new UpdatePlanningStage($request, $Planningstage));
                return redirect()->route('planning-stage.index')->with('success', __('The Stage details are updated successfully'));
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

        if (Auth::user()->isAbleTo('planning stage delete')) {

            $Planningstage = PlanningStage::find($id);

            if ($Planningstage->created_by == creatorId() &&  $Planningstage->workspace  == getActiveWorkSpace()) {

                $Charters     = PlanningCharters::where('stage',$Planningstage->id)->where('workspace',getActiveWorkSpace())->get();
                if(count($Charters) == 0)
                {
                    event(new DestroyPlanningStage($Planningstage));
                    $Planningstage->delete();
                }
                else
                {
                    return redirect()->back()->with('error', __('This stage has creativity. Please remove the creativity from this stage.'));
                }
                return redirect()->back()->with('success', __('The stage has been deleted'));
            } else {

                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {

            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
