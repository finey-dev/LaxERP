<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\RepairManagementSystem\DataTables\RepairTechnicianDataTable;
use Illuminate\Support\Facades\Auth;
use Workdo\RepairManagementSystem\Entities\RepairTechnician;
use Workdo\RepairManagementSystem\Events\CreateRepairTechnician;
use Workdo\RepairManagementSystem\Events\UpdateRepairTechnician;
use Workdo\RepairManagementSystem\Events\DestroyRepairTechnician;

class RepairTechnicianController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(RepairTechnicianDataTable $datatable)
    {
        if (Auth::user()->isAbleTo('repair technician manage')) {
            return $datatable->render('repair-management-system::repair-technician.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('repair technician create')) {
            return view('repair-management-system::repair-technician.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('repair technician create')) {
            $validator = \Validator::make(
                $request->all(),
                [

                    'name' => 'required',
                    'email' => 'required',
                    'mobile_no' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $repair_technician                = new RepairTechnician();
            $repair_technician->name          = $request->name;
            $repair_technician->email         = $request->email;
            $repair_technician->mobile_no     = $request->mobile_no;
            $repair_technician->workspace     = getActiveWorkSpace();
            $repair_technician->created_by    = creatorId();
            $repair_technician->save();

            event(new CreateRepairTechnician($request, $repair_technician));

            return redirect()->route('repair-technician.index')->with('success', __('The repair technician has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('repair technician edit')) {
            $repair_technician = RepairTechnician::find($id);
            return view('repair-management-system::repair-technician.edit', compact('repair_technician'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->isAbleTo('repair technician edit')) {
            $repair_technician  = RepairTechnician::find($id);

                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                        'email' => 'required',
                        'mobile_no' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }


                $repair_technician->name      = $request->name;
                $repair_technician->email     = $request->email;
                $repair_technician->mobile_no = $request->mobile_no;
                $repair_technician->workspace          = getActiveWorkSpace();
                $repair_technician->created_by         = creatorId();
                $repair_technician->save();

                event(new UpdateRepairTechnician($request, $repair_technician));

                return redirect()->route('repair-technician.index')->with('success', __('The repair technician details are updated successfully.'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('repair technician delete')) {

            $repair_technician = RepairTechnician::find($id);

            event(new DestroyRepairTechnician($repair_technician));

            $repair_technician->delete();

            return redirect()->back()->with('success', __('The repair technician has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
