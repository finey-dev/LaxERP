<?php

namespace Workdo\MachineRepairManagement\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\MachineRepairManagement\DataTables\MachineDataTable;
use Workdo\MachineRepairManagement\Entities\Machine;
use Workdo\MachineRepairManagement\Events\CreateMachine;
use Workdo\MachineRepairManagement\Events\DestroyMachine;
use Workdo\MachineRepairManagement\Events\UpdateMachine;

class MachinesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(MachineDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('machine manage')) {
            return $dataTable->render('machine-repair-management::machine-repair.index');
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
        if (Auth::user()->isAbleTo('machine create')) {
            $status = ['Active' => __('Active'), 'Inactive' => __('Inactive')];
            if (module_is_active('CustomField')) {
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'MachineRepairManagement')->where('sub_module', 'Machine')->get();
            } else {
                $customFields = null;
            }

            return view('machine-repair-management::machine-repair.create', compact('status', 'customFields'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('machine create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name'              => 'required|string|max:255',
                    'manufacturer'      => 'required|string|max:255',
                    'model'             => 'required|string|max:255',
                    'installation_date' => 'required|date',
                    'status'            => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $machine                         = new Machine();
            $machine->name                   = $request->name;
            $machine->manufacturer           = $request->manufacturer;
            $machine->model                  = $request->model;
            $machine->installation_date      = $request->installation_date;
            $machine->description            = $request->description;
            $machine->last_maintenance_date  = $request->installation_date;
            $machine->status                 = $request->status;
            $machine->workspace              = getActiveWorkSpace();
            $machine->created_by             = creatorId();
            $machine->save();
            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($machine, $request->customField);
            }
            event(new CreateMachine($request, $machine));

            return redirect()->route('machine-repair.index')->with('success', __('The machine has been created successfully.'));
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
        if (Auth::user()->isAbleTo('machine show')) {
            $id       = \Crypt::decrypt($id);
            $machine = Machine::where('id', $id)->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->first();
            if (module_is_active('CustomField')) {
                $machine->customField = \Workdo\CustomField\Entities\CustomField::getData($machine, 'MachineRepairManagement', 'Machine');
                $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'MachineRepairManagement')->where('sub_module', 'Machine')->get();
            } else {
                $customFields = null;
            }
            return view('machine-repair-management::machine-repair.show', compact('machine', 'customFields'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('machine edit')) {
            $machine = Machine::find($id);
            if (isset($machine) && !empty($machine)) {
                $status = ['Active' => __('Active'), 'Inactive' => __('Inactive')];
                if (module_is_active('CustomField')) {
                    $machine->customField = \Workdo\CustomField\Entities\CustomField::getData($machine, 'MachineRepairManagement', 'Machine');
                    $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'MachineRepairManagement')->where('sub_module', 'Machine')->get();
                } else {
                    $customFields = null;
                }
                return view('machine-repair-management::machine-repair.edit', compact('machine', 'status', 'customFields'));
            } else {
                redirect()->back()->with('error', __('Machine not found.'));
            }
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
        if (Auth::user()->isAbleTo('machine edit')) {
            $machine = Machine::find($id);
            if (isset($machine) && !empty($machine)) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name'              => 'required|string|max:255',
                        'manufacturer'      => 'required|string|max:255',
                        'model'             => 'required|string|max:255',
                        'installation_date' => 'required|date',
                        'status'            => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $machine->name                   = $request->name;
                $machine->manufacturer           = $request->manufacturer;
                $machine->model                  = $request->model;
                $machine->installation_date      = $request->installation_date;
                $machine->description            = $request->description;
                $machine->last_maintenance_date  = $request->installation_date;
                $machine->status                 = $request->status;
                $machine->workspace              = getActiveWorkSpace();
                $machine->created_by             = creatorId();
                $machine->save();
                if (module_is_active('CustomField')) {
                    \Workdo\CustomField\Entities\CustomField::saveData($machine, $request->customField);
                }
                event(new UpdateMachine($request, $machine));

                return redirect()->route('machine-repair.index')->with('success', __('The machine has been updated successfully.'));
            } else {
                redirect()->back()->with('error', __('Machine not found.'));
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
        if (Auth::user()->isAbleTo('machine delete')) {
            $machine = Machine::find($id);
            if (isset($machine) && !empty($machine)) {
                if (module_is_active('CustomField')) {
                    $customFields = \Workdo\CustomField\Entities\CustomField::where('module', 'MachineRepairManagement')->where('sub_module', 'Machine')->get();
                    foreach ($customFields as $customField) {
                        $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $machine->id)->where('field_id', $customField->id)->first();
                        if (!empty($value)) {
                            $value->delete();
                        }
                    }
                }
                event(new DestroyMachine($machine));

                $machine->delete();

                return redirect()->route('machine-repair.index')->with('success', __('The machine has been#MRR00004 deleted.'));
            } else {
                return redirect()->back()->with('error', __('Machine not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
