<?php

namespace Workdo\BeverageManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\BeverageManagement\DataTables\BeverageMaintenanceDataTable;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\Entities\BeverageMaintenance;
use Workdo\BeverageManagement\Events\CreateBeverageMaintenance;
use Workdo\BeverageManagement\Events\DestroyBeverageMaintenance;
use Workdo\BeverageManagement\Events\UpdateBeverageMaintenance;

class BeverageMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(BeverageMaintenanceDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('beverage-maintenance manage')) {
            return $dataTable->render('beverage-management::beverage-maintenance.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('beverage-maintenance create')) {

            return view('beverage-management::beverage-maintenance.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('beverage-maintenance create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'maintenance_type' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $beverage_maintenance                        = new BeverageMaintenance();
            $beverage_maintenance->maintenance_date               = $request->maintenance_date;
            $beverage_maintenance->maintenance_type            = $request->maintenance_type;
            $beverage_maintenance->status            = $request->status;
            $beverage_maintenance->comments                = $request->comments;
            $beverage_maintenance->workspace             = getActiveWorkSpace();
            $beverage_maintenance->created_by            = creatorId();
            $beverage_maintenance->save();
            event(new CreateBeverageMaintenance($request, $beverage_maintenance));

            return redirect()->route('beverage-maintenance.index')->with('success', __('The Maintenance has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function show($id)
    {
        if (Auth::user()->isAbleTo('beverage-maintenance manage')) {
            $Maintenance = BeverageMaintenance::find($id);
            return view('beverage-management::beverage-maintenance.comment' ,compact('Maintenance'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }    }
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('beverage-maintenance edit')) {
            $beverage_maintenance = BeverageMaintenance::find($id);
            if ($beverage_maintenance) {
                if ($beverage_maintenance->created_by == creatorId() && $beverage_maintenance->workspace == getActiveWorkSpace()) {
                    return view('beverage-management::beverage-maintenance.edit', compact('beverage_maintenance'));
                } else {
                    return response()->json(['error' => __('Permission denied.')]);
                }
            } else {
                return response()->json(['error' => __('Beverage Maintenance not found.')]);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('beverage-maintenance edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'maintenance_type' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $beverage_maintenance                        = BeverageMaintenance::find($id);
            $beverage_maintenance->maintenance_date               = $request->maintenance_date;
            $beverage_maintenance->maintenance_type            = $request->maintenance_type;
            $beverage_maintenance->status            = $request->status;
            $beverage_maintenance->comments                = $request->comments;
            $beverage_maintenance->workspace             = getActiveWorkSpace();
            $beverage_maintenance->created_by            = creatorId();
            $beverage_maintenance->update();
            event(new UpdateBeverageMaintenance($request, $beverage_maintenance));

            return redirect()->route('beverage-maintenance.index')->with('success', __('The Maintenance has been Updated Successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('beverage-maintenance delete')) {
            $beverage_maintenance = BeverageMaintenance::find($id);
            if ($beverage_maintenance->created_by == creatorId()  && $beverage_maintenance->workspace == getActiveWorkSpace()) {
                event(new DestroyBeverageMaintenance($beverage_maintenance));

                $beverage_maintenance->delete();
                return redirect()->route('beverage-maintenance.index')->with('success', __('The Maintenance has been deleted successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
