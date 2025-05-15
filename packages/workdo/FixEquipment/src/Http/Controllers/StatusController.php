<?php

namespace Workdo\FixEquipment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\FixEquipment\Entities\EquipmentStatus;
use Workdo\FixEquipment\Events\CreateStatus;
use Workdo\FixEquipment\Events\DestroyStatus;
use Workdo\FixEquipment\Events\UpdateStatus;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(Auth::user()->isAbleTo('equipment status labels manage')){

            $statuses = EquipmentStatus::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            return view('fix-equipment::status.index', compact('statuses'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->isAbleTo('equipment status labels create')){
            return view('fix-equipment::status.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->isAbleTo('equipment status labels create')){

            $status = new EquipmentStatus();

            $status->title = $request->status;
            $status->color = '#' . $request->color;
            $status->created_by = creatorId();
            $status->workspace = getActiveWorkSpace();
            $status->save();

            event(new CreateStatus($request, $status));

            return redirect()->back()->with('success', __('The status has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        return view('fix-equipment::show');
    }

    public function edit($id)
    {
        if(Auth::user()->isAbleTo('equipment status labels edit')){
            $status = EquipmentStatus::find($id);

            return view('fix-equipment::status.edit', compact('status'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->isAbleTo('equipment status labels edit')){

            $status = EquipmentStatus::find($id);

            $status->title = $request->status;
            $status->color = '#' . $request->color;
            $status->save();

            event(new UpdateStatus($request, $status));

            return redirect()->back()->with('success', __('The status details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if(Auth::user()->isAbleTo('equipment status labels delete')){
            $status = EquipmentStatus::find($id);

            event(new DestroyStatus($status));

            $status->delete();

            return redirect()->back()->with('success', __('The status has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
